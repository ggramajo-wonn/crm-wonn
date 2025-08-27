@csrf
@php
  // Prearmar el valor del campo combinado de GPS a partir de las columnas guardadas
  $gpsValue = trim(
      ($client->gps_lat ?? '') .
      ((isset($client->gps_lat) && isset($client->gps_lng)) ? ',' : '') .
      ($client->gps_lng ?? '')
  );
@endphp

<div class="grid sm:grid-cols-2 gap-4">
  <label class="block">
    <span class="text-sm text-gray-400">Nombre</span>
    <input type="text" name="name" value="{{ old('name', $client->name) }}" required
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">DNI</span>
    <input type="text" name="dni" value="{{ old('dni', $client->dni) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Email</span>
    <input type="email" name="email" value="{{ old('email', $client->email) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Cel 1</span>
    <input type="text" name="cel1" value="{{ old('cel1', $client->cel1) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Cel 2</span>
    <input type="text" name="cel2" value="{{ old('cel2', $client->cel2) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block sm:col-span-2">
    <span class="text-sm text-gray-400">Dirección</span>
    <input type="text" name="address" value="{{ old('address', $client->address) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Localidad</span>
    <input type="text" name="localidad" value="{{ old('localidad', $client->localidad) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">CP</span>
    <input type="text" name="cp" value="{{ old('cp', $client->cp) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>

  {{-- Campo combinado para GPS --}}
  <label class="block sm:col-span-2">
    <span class="text-sm text-gray-400">GPS (Lat,Lng)</span>
    <input
      type="text"
      name="gps_coords"
      value="{{ old('gps_coords', $gpsValue) }}"
      placeholder="-23.126381,-64.323846"
      class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2"
    >
    <p class="mt-1 text-xs text-gray-500">
      Ingresá <strong>latitud,longitud</strong> separadas por coma (sin espacios). Ejemplo: <em>-23.126381,-64.323846</em>
    </p>
  </label>

  <label class="block">
    <span class="text-sm text-gray-400">Estado</span>
    <select name="status" class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
      <option value="activo"   {{ old('status', $client->status) === 'activo' ? 'selected' : '' }}>Activo</option>
      <option value="inactivo" {{ old('status', $client->status) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
    </select>
  </label>
</div>

<div class="mt-4 flex gap-3">
  <button class="rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2">Guardar</button>
  <a href="{{ route('clientes.index') }}" class="px-4 py-2 rounded-lg border border-gray-700 hover:bg-gray-900">Cancelar</a>
</div>
