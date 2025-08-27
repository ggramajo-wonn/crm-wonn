@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Empresa</h1>
    <a href="{{ route('panel') }}" class="text-sm text-gray-400 hover:text-primary-400">Volver al Panel</a>
  </div>

  <x-flash />

  <form method="POST" action="{{ route('empresa.update') }}" enctype="multipart/form-data" class="space-y-8 max-w-3xl">
    @csrf
    @method('PUT')

    {{-- Datos generales --}}
    <div>
      <h2 class="text-lg font-semibold mb-3">Datos generales</h2>
      <div class="grid sm:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-400">Nombre / Razón social</span>
          <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                 class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">CUIT</span>
          <input type="text" name="cuit" value="{{ old('cuit', $company->cuit) }}" placeholder="20-12345678-3"
                 class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
        </label>
      </div>
    </div>

    {{-- Logo --}}
    <div>
      <h2 class="text-lg font-semibold mb-3">Logo</h2>
      <div class="grid sm:grid-cols-2 gap-4 items-start">
        <div>
          <input type="file" name="logo" accept="image/*" class="block mt-1 w-full text-sm text-gray-300">
          <p class="text-xs text-gray-500 mt-1">PNG/JPG/WEBP/SVG, máx. 5MB.</p>
        </div>

        <div class="flex items-center gap-4">
          @php $logo = $company->logo_path ? asset('storage/'.$company->logo_path) : null; @endphp
          @if($logo)
            {{-- Vista sin recorte: se muestra completo --}}
            <div class="border border-gray-800 rounded-lg p-2 bg-gray-900">
              <img src="{{ $logo }}" alt="Logo" class="h-14 w-28 object-contain">
            </div>
            <form action="{{ route('empresa.logo.destroy') }}" method="POST" onsubmit="return confirm('¿Eliminar logo actual?');">
              @csrf @method('DELETE')
              <button class="rounded-lg border border-gray-700 px-3 py-2 hover:bg-gray-900 text-sm">Quitar logo</button>
            </form>
          @else
            <div class="w-28 h-14 rounded-lg bg-gray-800 border border-gray-700 flex items-center justify-center text-gray-500">Sin logo</div>
          @endif
        </div>
      </div>
    </div>

    {{-- Router Mikrotik (opcional) --}}
    <div>
      <h2 class="text-lg font-semibold mb-3">Router Mikrotik (opcional)</h2>
      <div class="grid sm:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-400">Nombre</span>
          <input type="text" name="router_name" value="{{ old('router_name', $company->router_name) }}"
                 class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2" placeholder="Ej: RB1100 Datacenter">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">IP</span>
          <input type="text" name="router_ip" value="{{ old('router_ip', $company->router_ip) }}"
                 class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2" placeholder="Ej: 192.168.88.1">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Usuario API</span>
          <input type="text" name="router_api_user" value="{{ old('router_api_user', $company->router_api_user) }}"
                 class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2" placeholder="api-user">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Clave API</span>
          <input type="text" name="router_api_key" value="{{ old('router_api_key', $company->router_api_key) }}"
                 class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2" placeholder="token o contraseña">
        </label>
      </div>
      <p class="text-xs text-gray-500 mt-2">* Estos datos se usarán más adelante para conectar por API al Mikrotik.</p>
    </div>

    <div class="flex gap-3">
      <button class="rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2">Guardar cambios</button>
      <a href="{{ route('panel') }}" class="px-4 py-2 rounded-lg border border-gray-700 hover:bg-gray-900">Cancelar</a>
    </div>
  </form>
@endsection
