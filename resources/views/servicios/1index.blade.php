@extends('layouts.app')
@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Servicios</h1>
    {{-- Se quita el botón Nuevo: los servicios se crean desde el cliente --}}
  </div>

  <div class="overflow-x-auto rounded-xl border border-gray-800">
    <table class="min-w-full text-left">
      <thead class="bg-gray-900 text-gray-400">
        <tr>
          <th class="p-3">Cliente</th>
          <th class="p-3">Plan</th>
          <th class="p-3">Precio</th>
          <th class="p-3">Estado</th>
          <th class="p-3">IP</th>
          <th class="p-3">Instalación</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($items as $s)
          <tr class="border-t border-gray-800">
            <td class="p-3">{{ $s->client->name ?? '-' }}</td>
            <td class="p-3">{{ $s->plan->name ?? $s->name }}</td>
            <td class="p-3">$ {{ number_format($s->price, 2, ',', '.') }}</td>
            <td class="p-3">
              <span class="px-2 py-1 rounded text-xs {{ $s->status==='activo' ? 'bg-emerald-900/30 text-emerald-300' : 'bg-amber-900/30 text-amber-200' }}">
                {{ ucfirst($s->status) }}
              </span>
            </td>
            <td class="p-3">{{ $s->ip ?: '—' }}</td>
            <td class="p-3">{{ optional($s->started_at)->format('d/m/Y') }}</td>
            <td class="p-3 text-right space-x-2">
              <a href="{{ route('servicios.edit', $s) }}" class="text-primary-400 hover:underline">Editar</a>
              <form action="{{ route('servicios.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este servicio?');">
                @csrf @method('DELETE')
                <button class="text-red-400 hover:underline">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td class="p-4 text-gray-400" colspan="7">Sin servicios aún.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $items->links() }}</div>
@endsection
