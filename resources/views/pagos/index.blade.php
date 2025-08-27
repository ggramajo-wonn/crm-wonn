@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Pagos</h1>
    <a href="{{ route('pagos.create') }}" class="rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2">Nuevo</a>
  </div>

  <div class="mb-4 flex gap-2 text-sm">
    @foreach (['acreditado'=>'Acreditados','saldo'=>'Saldos a favor','duplicado'=>'Duplicados','fallido'=>'Fallidos'] as $key => $label)
      <a href="{{ route('pagos.index', ['estado' => $key]) }}"
         class="px-3 py-1 rounded border {{ $estado === $key ? 'border-primary-700 bg-primary-900/20' : 'border-gray-800 hover:bg-gray-900' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <div class="overflow-x-auto rounded-xl border border-gray-800">
    <table class="min-w-full text-left">
      <thead class="bg-gray-900 text-gray-400">
        <tr>
          <th class="p-3">Fecha</th>
          <th class="p-3">Cliente</th>
          <th class="p-3">Monto</th>
          <th class="p-3">Fuente</th>
          <th class="p-3">Referencia</th>
          <th class="p-3">Factura</th>
          <th class="p-3">Estado</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($items as $p)
          <tr class="border-t border-gray-800">
            <td class="p-3">{{ optional($p->paid_at)->format('d/m/Y H:i') }}</td>
            <td class="p-3">{{ $p->client->name ?? '-' }}</td>
            <td class="p-3">$ {{ number_format($p->amount, 2, ',', '.') }}</td>
            <td class="p-3">{{ $p->source }}</td>
            <td class="p-3">{{ $p->reference }}</td>
            <td class="p-3">
              @if ($p->invoice)
                #{{ $p->invoice->id }} — {{ $p->invoice->client->name }}
              @else
                <span class="text-gray-400">(sin factura)</span>
              @endif
            </td>
            <td class="p-3">
              @php
                $etiqueta = (!$p->invoice && $p->status === 'acreditado') ? 'Saldo a favor' : ucfirst($p->status);
              @endphp
              <span class="px-2 py-1 rounded text-xs
                {{ $p->status==='acreditado' ? 'bg-emerald-900/30 text-emerald-300'
                   : ($p->status==='duplicado' ? 'bg-amber-900/30 text-amber-200'
                   : ($p->status==='fallido' ? 'bg-red-900/30 text-red-200' : 'bg-gray-700 text-gray-300')) }}">
                {{ $etiqueta }}
              </span>
            </td>
            <td class="p-3 text-right">
              <form action="{{ route('pagos.destroy', $p) }}" method="POST" class="inline"
                    onsubmit="return confirm('¿Eliminar este pago?');">
                @csrf
                @method('DELETE')
                <button class="text-red-400 hover:underline">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td class="p-4 text-gray-400" colspan="8">No hay pagos en este estado.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $items->links() }}
  </div>
@endsection
