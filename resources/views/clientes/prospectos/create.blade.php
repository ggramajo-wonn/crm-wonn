@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Nuevo prospecto</h1>
  </div>

  <form method="POST" action="{{ route('clientes.prospectos.store') }}" class="rounded-lg border border-gray-800 p-4">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

      <div>
        <label class="block text-sm text-gray-400 mb-1">ID (opcional)</label>
        <input type="number" name="id" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm" placeholder="Dejar vacío para autoincremental">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-1">DNI</label>
        <input type="text" name="dni" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-1">Nombre *</label>
        <input type="text" name="nombre" required class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-1">Cel 1</label>
        <input type="text" name="cel1" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-1">Cel 2</label>
        <input type="text" name="cel2" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-1">Email</label>
        <input type="email" name="email" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm text-gray-400 mb-1">Dirección</label>
        <input type="text" name="direccion" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-1">Localidad</label>
        <input type="text" name="localidad" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-1">CP</label>
        <input type="text" name="cp" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm text-gray-400 mb-1">GPS (lat,lng)</label>
        <input type="text" name="gps" placeholder="-27.471,-58.834" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
      </div>

    </div>

    <div class="flex items-center gap-2 mt-4">
      <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Guardar prospecto</button>
      <a href="{{ route('clientes.prospectos.index') }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Cancelar</a>
    </div>
  </form>
@endsection
