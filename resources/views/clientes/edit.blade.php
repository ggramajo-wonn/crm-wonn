@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-bold mb-4">Editar cliente</h1>
  <x-flash />

  <form method="POST" action="{{ route('clientes.update', $client->id) }}">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <div class="space-y-2">
        <label for="id" class="block text-sm text-gray-300">ID</label>
        <input type="number" name="id" id="id"
               class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2"
               value="{{ old('id', $client->id) }}"
               min="1">
        <p class="text-xs text-gray-400">Podés modificar el ID. Debe ser único.</p>
      </div>

      <div class="space-y-2">
        <label for="dni" class="block text-sm text-gray-300">DNI</label>
        <input type="text" name="dni" id="dni" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('dni', $client->dni) }}">
      </div>

      <div class="space-y-2">
        <label for="name" class="block text-sm text-gray-300">Nombre</label>
        <input type="text" name="name" id="name" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('name', $client->name) }}">
      </div>

      <div class="space-y-2">
        <label for="cel1" class="block text-sm text-gray-300">Cel 1</label>
        <input type="text" name="cel1" id="cel1" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('cel1', $client->cel1) }}">
      </div>

      <div class="space-y-2">
        <label for="email" class="block text-sm text-gray-300">Email</label>
        <input type="email" name="email" id="email" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('email', $client->email) }}">
      </div>

      <div class="space-y-2">
        <label for="cel2" class="block text-sm text-gray-300">Cel 2</label>
        <input type="text" name="cel2" id="cel2" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('cel2', $client->cel2) }}">
      </div>

      <div class="space-y-2 md:col-span-2">
        <label for="address" class="block text-sm text-gray-300">Dirección</label>
        <input type="text" name="address" id="address" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('address', $client->address) }}">
      </div>

      <div class="space-y-2">
        <label for="localidad" class="block text-sm text-gray-300">Localidad</label>
        <input type="text" name="localidad" id="localidad" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('localidad', $client->localidad) }}">
      </div>

      <div class="space-y-2">
        <label for="cp" class="block text-sm text-gray-300">CP</label>
        <input type="text" name="cp" id="cp" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('cp', $client->cp) }}">
      </div>

      <div class="space-y-2 md:col-span-2">
        <label for="gps" class="block text-sm text-gray-300">GPS (Lat,Lng)</label>
        <input type="text" name="gps" id="gps" class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2" value="{{ old('gps', $client->gps) }}">
        <p class="text-xs text-gray-400">Ingresá <strong>latitud,longitud</strong> separadas por coma (sin espacios).</p>
      </div>

      <div class="space-y-2 md:col-span-2">
        <label for="status" class="block text-sm text-gray-300">Estado</label>
        <select name="status" id="status" class="form-select w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2">
          <option value="activo" @selected(old('status', $client->status) == 'activo')>Activo</option>
          <option value="inactivo" @selected(old('status', $client->status) == 'inactivo')>Inactivo</option>
        </select>
      </div>

    </div>

    <div class="mt-6 flex items-center gap-3">
      <button type="submit" class="btn btn-primary bg-[#0EA5B7] hover:opacity-90 text-white font-medium px-4 py-2 rounded-lg">Guardar</button>
      <a href="{{ route('clientes.show', $client->id) }}" class="btn btn-secondary border border-gray-600 text-gray-100 px-4 py-2 rounded-lg">Cancelar</a>
    </div>
  </form>
@endsection
