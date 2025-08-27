@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-bold">Cajas NAP — OLTs</h1>

  <div class="flex items-center gap-2">
    <form method="GET" action="{{ route('olts.index') }}" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar: nombre, localidad"
             class="w-72 rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm" />
      <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Filtrar</button>
    </form>
    <a href="{{ route('olts.create') }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">+ OLT</a>
  </div>
</div>

<div class="rounded-lg border border-gray-800 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-900/50 text-left">
      <tr>
        <th class="p-3 text-gray-400">ID</th>
        <th class="p-3 text-gray-400">Nombre</th>
        <th class="p-3 text-gray-400">Localidad</th>
        <th class="p-3 text-gray-400">NAPs</th>
        <th class="p-3 text-right text-gray-400">Acciones</th>
      </tr>
    </thead>
    <tbody class="bg-black/10">
      @forelse($olts as $o)
      <tr class="border-t border-gray-800 hover:bg-gray-900/40">
        <td class="p-3 text-gray-400">#{{ $o->id }}</td>
        <td class="p-3">{{ $o->name }}</td>
        <td class="p-3">{{ $o->localidad }}</td>
        <td class="p-3 text-gray-300">{{ $o->naps_count }}</td>
        <td class="p-3">
          <div class="flex justify-end gap-2">
            <a href="{{ route('olts.show', $o->id) }}" class="text-sky-400 hover:underline">Ver</a>
            <a href="{{ route('olts.edit', $o->id) }}" class="text-sky-400 hover:underline">Editar</a>
            <form method="POST" action="{{ route('olts.destroy', $o->id) }}" onsubmit="return confirm('¿Eliminar OLT?');">
              @csrf @method('DELETE')
              <button class="text-red-400 hover:underline">Eliminar</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="5" class="p-6 text-center text-gray-400">No hay OLTs cargadas.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
