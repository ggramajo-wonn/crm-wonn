@csrf
<div class="grid sm:grid-cols-2 gap-4">
  <label class="block">
    <span class="text-sm text-gray-400">Cliente</span>
    <select name="client_id" class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
      @foreach ($clients as $c)
        <option value="{{ $c->id }}" {{ (int)old('client_id', $clientId) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
      @endforeach
    </select>
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Factura (opcional)</span>
    <select name="invoice_id" class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
      <option value="">— Autodetectar con saldo —</option>
      @foreach ($invoices as $inv)
        @php $saldo = $inv->balance(); @endphp
        <option value="{{ $inv->id }}" {{ (int)old('invoice_id') === $inv->id ? 'selected' : '' }}>
          #{{ $inv->id }} — {{ $inv->client->name }} — Saldo ${{ number_format($saldo,2,',','.') }}
        </option>
      @endforeach
    </select>
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Monto</span>
    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $payment->amount) }}" required
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Fecha de pago</span>
    <input type="datetime-local" name="paid_at" value="{{ old('paid_at', optional($payment->paid_at)->format('Y-m-d\TH:i')) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Fuente</span>
    <input type="text" name="source" value="{{ old('source', $payment->source) }}" placeholder="SIRO / TIM / Prisma / manual"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Referencia (para evitar duplicados)</span>
    <input type="text" name="reference" value="{{ old('reference', $payment->reference) }}" placeholder="id de transacción"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Estado</span>
    <select name="status" class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
      @foreach (['acreditado','pendiente','duplicado','fallido'] as $st)
        <option value="{{ $st }}" {{ old('status', $payment->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
  </label>

  <label class="block sm:col-span-2">
    <span class="text-sm text-gray-400">Notas</span>
    <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">{{ old('notes', $payment->notes) }}</textarea>
  </label>
</div>

<div class="mt-4 flex gap-3">
  <button class="rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2">Guardar</button>
  <a href="{{ route('pagos.index') }}" class="px-4 py-2 rounded-lg border border-gray-700 hover:bg-gray-900">Cancelar</a>
</div>