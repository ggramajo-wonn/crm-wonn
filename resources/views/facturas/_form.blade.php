@csrf
<div class="grid sm:grid-cols-2 gap-4">
  <label class="block">
    <span class="text-sm text-gray-400">Cliente</span>
    <select name="client_id" class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
      @foreach ($clients as $c)
        <option value="{{ $c->id }}" {{ (int)old('client_id', $invoice->client_id) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
      @endforeach
    </select>
  </label>
  <label class="block">
    <span class="text-sm text-gray-400">Total</span>
    <input type="number" step="0.01" min="0.01" name="total" value="{{ old('total', $invoice->total) }}" required
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>
  <label class="block">
    <span class="text-sm text-gray-400">Emitida</span>
    <input type="datetime-local" name="issued_at" value="{{ old('issued_at', optional($invoice->issued_at)->format('Y-m-d\TH:i')) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>
  <label class="block">
    <span class="text-sm text-gray-400">Vence</span>
    <input type="datetime-local" name="due_at" value="{{ old('due_at', optional($invoice->due_at)->format('Y-m-d\TH:i')) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>
  <label class="block">
    <span class="text-sm text-gray-400">Estado</span>
    <select name="status" class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
      @foreach (['borrador','emitida','pagada','vencida'] as $st)
        <option value="{{ $st }}" {{ old('status', $invoice->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
  </label>
</div>
<div class="mt-4 flex gap-3">
  <button class="rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2">Guardar</button>
  <a href="{{ route('facturas.index') }}" class="px-4 py-2 rounded-lg border border-gray-700 hover:bg-gray-900">Cancelar</a>
</div>