<?php
namespace App\Http\Controllers;

use App\Models\{Invoice, Client, Payment};
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
     $clientId = $request->integer('client_id');
     $q = \App\Models\Invoice::with('client')->latest('issued_at');
     if ($clientId) $q->where('client_id', $clientId);
     $items = $q->paginate(12)->appends($request->only('client_id'));
     $cliente = $clientId ? \App\Models\Client::find($clientId) : null;
     return view('facturas.index', compact('items','cliente'));
    }

    public function create(Request $request)
    {
        $invoice = new Invoice([
            'status'    => 'emitida',
            'issued_at' => now(),
            'due_at'    => now()->addDays(10),
            'client_id' => $request->integer('client_id') ?: null,
            'total'     => $request->has('total') ? (float)$request->query('total') : null,
        ]);
        $clients = Client::orderBy('name')->get();
        return view('facturas.create', compact('invoice', 'clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'total'     => 'required|numeric|min:0.01',
            'issued_at' => 'nullable|date',
            'due_at'    => 'nullable|date',
            'status'    => 'required|in:borrador,emitida,pagada,vencida',
        ]);

        $inv = Invoice::create($data);

        // Consumir saldo a favor automáticamente
        $this->applyAvailableCredit($inv);

        return redirect()->route('facturas.index')->with('ok', 'Factura creada y saldo a favor aplicado si había.');
    }

    public function edit(Invoice $factura)
    {
        $clients = Client::orderBy('name')->get();
        return view('facturas.edit', ['invoice' => $factura, 'clients' => $clients]);
    }

    public function update(Request $request, Invoice $factura)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'total'     => 'required|numeric|min:0.01',
            'issued_at' => 'nullable|date',
            'due_at'    => 'nullable|date',
            'status'    => 'required|in:borrador,emitida,pagada,vencida',
        ]);

        $factura->update($data);

        // Por si cambió total o fechas, volvemos a intentar consumir saldo
        $this->applyAvailableCredit($factura);

        return redirect()->route('facturas.index')->with('ok', 'Factura actualizada.');
    }

    public function destroy(Invoice $factura)
    {
        $hasPaid = $factura->payments()->where('status','acreditado')->exists();
        if ($hasPaid) return back()->withErrors('No se puede eliminar: tiene pagos acreditados.');
        $factura->delete();
        return back()->with('ok', 'Factura eliminada.');
    }

    private function applyAvailableCredit(Invoice $invoice): void
    {
        $remaining = $invoice->balance();
        if ($remaining <= 0) return;

        // Tomamos pagos acreditados sin factura (saldo a favor), por orden de antigüedad
        $credits = Payment::where('client_id', $invoice->client_id)
            ->where('status','acreditado')
            ->whereNull('invoice_id')
            ->orderBy('paid_at', 'asc')
            ->get();

        $suffix = 1;

        foreach ($credits as $credit) {
            if ($remaining <= 0) break;

            $aplicar = min($remaining, (float)$credit->amount);

            // slice aplicado a la factura
            Payment::create([
                'client_id'  => $invoice->client_id,
                'invoice_id' => $invoice->id,
                'amount'     => $aplicar,
                'paid_at'    => $credit->paid_at,
                'source'     => 'saldo',
                'reference'  => $credit->reference ? ($credit->reference.'-F'.$suffix) : 'AUTO-SALDO',
                'status'     => 'acreditado',
                'notes'      => $credit->notes,
            ]);

            $remaining -= $aplicar;

            // restar del crédito original
            $resto = (float)$credit->amount - $aplicar;
            if ($resto <= 0.00001) {
                $credit->delete();
            } else {
                $credit->update(['amount' => $resto]);
            }

            $suffix++;
        }

        // Actualizar estado final de la factura
        $paid = $invoice->paidSum();
        $invoice->update(['status' => $paid >= (float)$invoice->total ? 'pagada' : 'emitida']);
    }
}
