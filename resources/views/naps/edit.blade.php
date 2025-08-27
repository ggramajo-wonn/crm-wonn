@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-bold mb-4">Editar NAP (OLT #{{ $olt->id }} — {{ $olt->name }})</h1>

  <form method="POST" action="{{ route('olts.naps.update', [$olt->id, $nap->id]) }}" class="space-y-4">
    @csrf @method('PUT')
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">ID</label>
      <input type="text" value="O{{ $olt->id }}-N{{ $nap->id }}" disabled
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Nombre</label>
      <input type="text" name="name" value="{{ old('name', $nap->name) }}" required
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm text-gray-400 mb-1">Ubicación</label>
      <input type="text" name="ubicacion" value="{{ old('ubicacion', $nap->ubicacion) }}"
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm" placeholder="Calle 123, Barrio...">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">GPS (lat,lng)</label>
      <input type="text" name="gps" value="{{ old('gps', $nap->gps) }}"
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm" placeholder="-27.47,-58.83">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Puertos</label>
      <input type="number" name="puertos" min="1" max="128" value="{{ old('puertos', $nap->puertos) }}" required
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm text-gray-400 mb-1">Detalles</label>
      <textarea name="detalles" rows="3" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">{{ old('detalles', $nap->detalles) }}</textarea>
    </div>
  </div>
    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Actualizar</button>
      <a href="{{ route('olts.show', $olt->id) }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Cancelar</a>
    </div>
  </form>
@endsection
