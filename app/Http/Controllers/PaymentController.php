<?php
namespace App\Http\Controllers;

use App\Models\{Payment, Invoice, Client};
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
     $estado   = $request->query('estado', 'acreditado');   // acreditado|saldo|duplicado|fallido
     $clientId = $request->integer('client_id');

     $query = \App\Models\Payment::with(['client','invoice'])->latest('paid_at');

     if ($estado === 'saldo') {
        $query->where('status','acreditado')->whereNull('invoice_id');
     } elseif (in_array($estado, ['acreditado','duplicado','fallido'])) {
        $query->where('status', $estado);
     }

     if ($clientId) $query->where('client_id', $clientId);

     $items   = $query->paginate(15)->appends($request->only('estado','client_id'));
     $cliente = $clientId ? \App\Models\Client::find($clientId) : null;

     return view('pagos.index', compact('items','estado','cliente'));
    }

    public function create(Request $request)
    {
        $payment  = new Payment(['status' => 'acreditado', 'paid_at' => now()]);
        $clients  = Client::orderBy('name')->get();
        $clientId = (int) $request->query('client_id');
        return view('pagos.create', compact('payment','clients','clientId'))
            ->with('invoices', collect());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'  => 'required|exists:clients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount'     => 'required|numeric|min:0.01',
            'paid_at'    => 'nullable|date',
            'source'     => 'nullable|string|max:50',   // SIRO / TIM / Prisma / manual
            'reference'  => 'nullable|string|max:100',  // para deduplicar
            'status'     => 'required|in:acreditado,duplicado,fallido', // quitamos 'pendiente'
            'notes'      => 'nullable|string',
        ]);

        // Duplicados por referencia (+ fuente)
        if (!empty($data['reference'])) {
            $dup = Payment::where('reference', $data['reference'])
                ->when(!empty($data['source']), fn($q) => $q->where('source', $data['source']))
                ->exists();
            if ($dup) {
                $data['status'] = 'duplicado';
            }
        }

        // Creamos un pago "global" (sin factura) y luego lo asignamos a facturas con saldo;
        // si sobra, queda como saldo a favor (invoice_id = null).
        $global = Payment::create([
            'client_id' => $data['client_id'],
            'invoice_id'=> null,
            'amount'    => $data['amount'],
            'paid_at'   => $data['paid_at'] ?? now(),
            'source'    => $data['source'] ?? 'manual',
            'reference' => $data['reference'] ?? null,
            'status'    => $data['status'], // duplicado/fallido/acreditado
            'notes'     => $data['notes'] ?? null,
        ]);

        if ($global->status === 'acreditado') {
            // 1) Si vino invoice_id, aplicar primero ahí
            if (!empty($data['invoice_id'])) {
                $this->allocateToInvoices($global, Invoice::find($data['invoice_id']));
            }
            // 2) Luego seguir con cualquier otra factura emitida/vencida con saldo
            $this->allocateToInvoices($global, null);
        }

        return redirect()
            ->route('pagos.index', ['estado' => $global->invoice_id ? 'acreditado' : 'saldo'])
            ->with('ok', 'Pago registrado en cuenta del cliente.');
    }

    public function destroy(Payment $pago)
    {
        $invoice = $pago->invoice;
        $pago->delete();
        if ($invoice) { $this->recalcInvoice($invoice->fresh()); }
        return back()->with('ok', 'Pago eliminado.');
    }

    /**
     * Asigna un pago acreditado sin factura a facturas con saldo.
     * Si se pasa $prefer, se aplica primero a esa factura y luego a las demás.
     * Si luego de asignar queda amount=0 en el pago global, se elimina.
     */
    private function allocateToInvoices(Payment $global, ?Invoice $prefer): void
    {
        if ($global->status !== 'acreditado' || $global->amount <= 0) return;

        $remaining = (float) $global->amount;

        $invoices = collect();

        if ($prefer) {
            $invoices->push($prefer);
        }

        // Todas las facturas emitidas o vencidas con saldo (asc por fecha)
        $more = Invoice::where('client_id', $global->client_id)
            ->whereIn('status', ['emitida','vencida'])
            ->orderBy('issued_at', 'asc')
            ->get()
            ->filter(fn($i) => $i->balance() > 0);

        // Evitar repetir la preferida
        if ($prefer) {
            $more = $more->reject(fn($i) => $i->id === $prefer->id);
        }

        $invoices = $invoices->merge($more);

        $suffix = 1;

        foreach ($invoices as $inv) {
            if ($remaining <= 0) break;

            $saldo = $inv->balance();
            if ($saldo <= 0) continue;

            $aplicar = min($remaining, $saldo);

            // Creamos un slice del pago aplicado a la factura
            Payment::create([
                'client_id'  => $global->client_id,
                'invoice_id' => $inv->id,
                'amount'     => $aplicar,
                'paid_at'    => $global->paid_at,
                'source'     => 'saldo', // <- marca que proviene de saldo a favor
                'reference'  => $global->reference ? ($global->reference.'-A'.$suffix) : 'AUTO-SALDO',
                'status'     => 'acreditado',
                'notes'      => $global->notes,
            ]);

            $remaining -= $aplicar;
            $this->recalcInvoice($inv->fresh());
            $suffix++;
        }

        // Actualizar o borrar el pago global
        if ($remaining <= 0.00001) {
            $global->delete(); // todo aplicado
        } else {
            $global->update(['amount' => $remaining]); // queda como saldo a favor
        }
    }

    // Recalcula el estado de una factura
    private function recalcInvoice(Invoice $invoice): void
    {
        $paid = $invoice->paidSum();

        if ($paid >= (float) $invoice->total) {
            $invoice->update(['status' => 'pagada']);
        } else {
            $invoice->update([
                'status' => now()->greaterThan($invoice->due_at ?? now())
                    ? 'vencida'
                    : 'emitida',
            ]);
        }
    }
}
