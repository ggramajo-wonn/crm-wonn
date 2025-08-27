@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-bold">Routers</h1>

  <div class="flex items-center gap-2">
    <form method="GET" action="{{ route('routers.index') }}" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar: ID, nombre, IP"
             class="w-72 rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm" />
      <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Filtrar</button>
    </form>
    <a href="{{ route('routers.create') }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Añadir router</a>
  </div>
</div>

<div class="rounded-lg border border-gray-800 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-900/50 text-left">
      <tr>
        <th class="p-3 text-gray-400">ID</th>
        <th class="p-3 text-gray-400">Nombre</th>
        <th class="p-3 text-gray-400">IP</th>
        <th class="p-3 text-gray-400">Modelo</th>
        <th class="p-3 text-gray-400">Versión</th>
        <th class="p-3 text-gray-400">Clientes</th>
        <th class="p-3 text-right text-gray-400">Acciones</th>
      </tr>
    </thead>
    <tbody class="bg-black/10">
      @forelse($routers as $r)
      <tr class="border-t border-gray-800 hover:bg-gray-900/40">
        <td class="p-3 text-gray-400">#{{ $r->id }}</td>
        <td class="p-3">{{ $r->name }}</td>
        <td class="p-3">{{ $r->ip }}</td>
        <td class="p-3 text-gray-300">{{ $r->model }}</td>
        <td class="p-3 text-gray-300">{{ $r->version }}</td>
        <td class="p-3 text-gray-300">{{ $r->clients_count ?? 0 }}</td>
        <td class="p-3">
          <div class="flex justify-end gap-2">
            <a href="{{ route('routers.edit', $r->id) }}" class="text-sky-400 hover:underline">Editar</a>
            <form method="POST" action="{{ route('routers.destroy', $r->id) }}"
                  onsubmit="return confirm('¿Eliminar router?');">
              @csrf @method('DELETE')
              <button class="text-red-400 hover:underline">Eliminar</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="p-6 text-center text-gray-400">No hay routers cargados.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
