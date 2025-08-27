@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-bold">Redes IPv4</h1>

  <div class="flex items-center gap-2">
    <form method="GET" action="{{ route('ipv4.index') }}" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar: nombre, red, CIDR"
             class="w-72 rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm" />
      <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Filtrar</button>
    </form>
    <a href="{{ route('ipv4.create') }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">+ Nuevo</a>
  </div>
</div>

<div class="rounded-lg border border-gray-800 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-900/50 text-left">
      <tr>
        <th class="p-3 text-gray-400">ID</th>
        <th class="p-3 text-gray-400">Nombre</th>
        <th class="p-3 text-gray-400">Red</th>
        <th class="p-3 text-gray-400">Uso de IPs</th>
        <th class="p-3 text-gray-400">CIDR</th>
        <th class="p-3 text-gray-400">Router</th>
        <th class="p-3 text-gray-400">Tipo</th>
        <th class="p-3 text-right text-gray-400">Acciones</th>
      </tr>
    </thead>
    <tbody class="bg-black/10">
      @forelse($networks as $n)
      <tr class="border-t border-gray-800 hover:bg-gray-900/40">
        <td class="p-3 text-gray-400">#{{ $n->id }}</td>
        <td class="p-3">{{ $n->name }}</td>
        <td class="p-3">{{ $n->network }}</td>
        <td class="p-3">
          <span class="font-medium">{{ $n->usage_pct }}%</span>
          <span class="text-gray-400">({{ $n->used_ips }} de {{ $n->total_ips }})</span>
        </td>
        <td class="p-3">/{{ $n->cidr }}</td>
        <td class="p-3">{{ $n->router_name }}</td>
        <td class="p-3">ESTATICO</td>
        <td class="p-3">
          <div class="flex justify-end gap-2">
            <a href="{{ route('ipv4.edit', $n->id) }}" class="text-sky-400 hover:underline">Editar</a>
            <form method="POST" action="{{ route('ipv4.destroy', $n->id) }}" onsubmit="return confirm('¿Eliminar la red?');">
              @csrf @method('DELETE')
              <button class="text-red-400 hover:underline">Eliminar</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="p-6 text-center text-gray-400">No hay redes creadas.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
