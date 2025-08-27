@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Facturas</h1>
    <a href="{{ route('facturas.create') }}" class="rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2">Nueva</a>
  </div>

  <div class="overflow-x-auto rounded-xl border border-gray-800">
    <table class="min-w-full text-left">
      <thead class="bg-gray-900 text-gray-400">
        <tr>
          <th class="p-3">Cliente</th>
          <th class="p-3">Emitida</th>
          <th class="p-3">Vence</th>
          <th class="p-3">Total</th>
          <th class="p-3">Pagado</th>
          <th class="p-3">Saldo</th>
          <th class="p-3">Estado</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($items as $i)
          @php
            $pagado = $i->paidSum();
            $saldo  = $i->balance();
          @endphp
          <tr class="border-t border-gray-800">
            <td class="p-3 whitespace-nowrap">{{ $i->client->name }}</td>
            <td class="p-3 whitespace-nowrap">{{ optional($i->issued_at)->format('d/m/Y') }}</td>
            <td class="p-3 whitespace-nowrap">{{ optional($i->due_at)->format('d/m/Y') }}</td>
            <td class="p-3 whitespace-nowrap">{{ number_format($i->total, 2, ',', '.') }} $</td>
            <td class="p-3 whitespace-nowrap">{{ number_format($pagado, 2, ',', '.') }} $</td>
            <td class="p-3 whitespace-nowrap">{{ number_format($saldo, 2, ',', '.') }} $</td>
            <td class="p-3 whitespace-nowrap">
              <span class="px-2 py-1 rounded text-xs
                {{ $i->status==='pagada' ? 'bg-emerald-900/30 text-emerald-300'
                   : ($i->status==='vencida' ? 'bg-red-900/30 text-red-200'
                   : 'bg-gray-700 text-gray-300') }}">
                {{ ucfirst($i->status) }}
                @if($i->status!=='pagada' && $pagado>0 && $saldo>0) (parcial) @endif
              </span>
            </td>
            <td class="p-3 text-right space-x-2 whitespace-nowrap">
              <a href="{{ route('facturas.edit', $i) }}" class="text-primary-400 hover:underline">Editar</a>
              <form action="{{ route('facturas.destroy', $i) }}" method="POST" class="inline"
                    onsubmit="return confirm('¿Eliminar esta factura?');">
                @csrf @method('DELETE')
                <button class="text-red-400 hover:underline">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td class="p-4 text-gray-400" colspan="8">Sin facturas aún.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $items->links() }}
  </div>
@endsection
