@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-bold mb-4">Nueva OLT</h1>

  <form method="POST" action="{{ route('olts.store') }}" class="space-y-4">
    @csrf
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">ID</label>
      <input type="text" value="{{ $olt->id }}" disabled class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Nombre</label>
      <input type="text" name="name" value="{{ old('name', $olt->name) }}" required class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Localidad</label>
      <input type="text" name="localidad" value="{{ old('localidad', $olt->localidad) }}" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
  </div>
    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Guardar</button>
      <a href="{{ route('olts.index') }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Cancelar</a>
    </div>
  </form>
@endsection
