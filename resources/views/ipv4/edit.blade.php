@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-bold mb-4">Editar red IPv4</h1>

  <form method="POST" action="{{ route('ipv4.update', $network->id) }}" class="space-y-4">
    @csrf @method('PUT')

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">ID</label>
      <input type="text" value="{{ $network->id }}" disabled
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Nombre de la red</label>
      <input type="text" name="name" value="{{ old('name', $network->name) }}" required
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Red (inicio)</label>
      <input type="text" name="network" placeholder="192.168.0.0" value="{{ old('network', $network->network) }}" required
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">CIDR</label>
      <input type="number" name="cidr" min="0" max="32" value="{{ old('cidr', $network->cidr) }}" required
             class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Router</label>
      <select name="router_id" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
        <option value="">— Ninguno —</option>
        @foreach($routers as $r)
          <option value="{{ $r->id }}" {{ (string)old('router_id', $network->router_id) === (string)$r->id ? 'selected' : '' }}>{{ $r->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Tipo</label>
      <select name="type" class="w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
        <option value="ESTATICO" {{ old('type', $network->type)=='ESTATICO' ? 'selected' : '' }}>ESTATICO</option>
      </select>
    </div>
  </div>

    <div class="flex items-center gap-2 pt-2">
      <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Actualizar</button>
      <a href="{{ route('ipv4.index') }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Cancelar</a>
    </div>
  </form>
@endsection
