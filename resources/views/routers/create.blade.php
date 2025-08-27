@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-bold mb-4">Añadir router</h1>

  <form method="POST" action="{{ route('routers.store') }}" class="space-y-4">
    @csrf

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">ID</label>
      <input type="text" value="{{ $router->id }}" disabled
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Nombre</label>
      <input type="text" name="name" value="{{ old('name', $router->name) }}" required
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">IP</label>
      <input type="text" name="ip" value="{{ old('ip', $router->ip) }}" required
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">GPS (lat,lng)</label>
      <input type="text" name="gps" value="{{ old('gps', $router->gps) }}"
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm" placeholder="-27.47,-58.83">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Usuario API</label>
      <input type="text" name="api_user" value="{{ old('api_user', $router->api_user) }}"
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Contraseña API</label>
      <input type="text" name="api_pass" value="{{ old('api_pass', $router->api_pass) }}"
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm text-gray-400 mb-1">Control de velocidad</label>
      <select name="speed_control" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
        <option value="simple_queues" {{ old('speed_control', $router->speed_control)=='simple_queues' ? 'selected' : '' }}>Colas simples (Estática)</option>
      </select>
      <p class="text-xs text-gray-500 mt-1">Más opciones se sumarán al integrar la API.</p>
    </div>
  </div>

    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Guardar</button>
      <a href="{{ route('routers.index') }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Cancelar</a>
    </div>
  </form>
@endsection
