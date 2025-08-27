@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Empresa</h1>
    <a href="{{ route('panel') }}" class="text-sm text-gray-400 hover:text-primary-400">Volver al Panel</a>
  </div>

  <x-flash />

  <form id="empresa-form" method="POST" action="{{ route('empresa.update') }}" enctype="multipart/form-data" class="space-y-8 max-w-5xl">
    @csrf
    @method('PUT')

    {{-- Datos generales --}}
    <div>
      <h2 class="text-lg font-semibold mb-3">Datos generales</h2>
      <div class="grid sm:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-400">Razón social</span>
          <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">CUIT</span>
          <input type="text" name="cuit" value="{{ old('cuit', $company->cuit) }}"
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </label>
      </div>
    </div>

    {{-- Datos de la empresa --}}
    <div>
      <h2 class="text-lg font-semibold mb-3">Más datos de la empresa</h2>
      <div class="grid sm:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-400">Nombre fantasía</span>
          <input type="text" name="fantasy_name" value="{{ old('fantasy_name', $company->fantasy_name) }}"
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500"
                 placeholder="Ej: WONN Internet">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Teléfonos</span>
          <input type="text" name="phones" value="{{ old('phones', $company->phones) }}"
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500"
                 placeholder="Ej: 3876-123456 / 3878-654321">
        </label>

        <label class="block sm:col-span-2">
          <span class="text-sm text-gray-400">Dirección</span>
          <input type="text" name="address" value="{{ old('address', $company->address) }}"
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500"
                 placeholder="Calle y número">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Localidad</span>
          <input type="text" name="locality" value="{{ old('locality', $company->locality) }}"
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500"
                 placeholder="Ej: Orán">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">CP</span>
          <input type="text" name="postal_code" value="{{ old('postal_code', $company->postal_code) }}"
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500"
                 placeholder="Ej: 4530">
        </label>

        <label class="block sm:col-span-2">
          <span class="text-sm text-gray-400">Web</span>
          <input type="text" name="website" value="{{ old('website', $company->website) }}"
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500"
                 placeholder="https://tusitio.com">
        </label>
      </div>
    </div>

    {{-- Logo --}}
    <div>
      <h2 class="text-lg font-semibold mb-3">Logo</h2>
      <div class="grid sm:grid-cols-[1fr_auto] gap-4 items-center">
        <div>
          <label class="block">
            <span class="text-sm text-gray-400">Subí un logo (PNG/JPG/WEBP/SVG, máx. 5MB)</span>
            <input type="file" accept="image/*" name="logo"
                   class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
          </label>
        </div>
        @if($company->logo_path)
          <div class="flex items-center gap-3">
            <img src="{{ asset('storage/'.$company->logo_path) }}" alt="Logo actual" class="h-14 rounded-md border border-gray-800 bg-gray-900 p-1">
            {{-- Usamos el mismo formulario principal, este botón envía remove_logo=1 --}}
            <button type="submit" name="remove_logo" value="1"
               class="inline-flex items-center rounded-md border border-gray-700 px-3 py-2 text-sm text-gray-300 hover:text-white"
               onclick="return confirm('¿Quitar el logo actual?');">
              Quitar logo
            </button>
          </div>
        @endif
      </div>
    </div>

    {{-- Claves Google --}}
    <div>
      <h2 class="text-lg font-semibold mb-3">Claves Google</h2>
      <div class="space-y-3">
        <label class="block">
          <span class="text-sm text-gray-400">Clave API Google (Maps & Street View)</span>
          <input type="text" name="google_maps_key" value="{{ old('google_maps_key', $company->google_maps_key) }}"
                 class="mt-1 w-full rounded-md border border-gray-800 bg-gray-900/50 px-3 py-2 text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary-500"
                 placeholder="AIza...">
        </label>
        <p class="text-xs text-gray-500">Usaremos esta clave para mapas, geocodificación y Street View.</p>
      </div>
    </div>

    {{-- Botones --}}
    <div class="flex gap-3">
      <button type="submit" class="inline-flex items-center rounded-md bg-primary-600 hover:bg-primary-500 px-4 py-2 text-white">
        Guardar cambios
      </button>
      <a href="{{ route('panel') }}" class="inline-flex items-center rounded-md border border-gray-700 px-4 py-2 text-gray-300 hover:text-white">
        Cancelar
      </a>
    </div>
  </form>
@endsection
