@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Prospectos</h1>

    <div class="flex items-center gap-2">
      <form method="GET" action="{{ route('clientes.prospectos.index') }}" class="flex items-center gap-2">
        <input
          type="text"
          name="q"
          value="{{ request('q') }}"
          placeholder="Filtrar: nombre, ID, documento, email"
          class="w-80 rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm"
        >
        <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">
          Filtrar
        </button>
      </form>

      <a
        href="{{ route('clientes.prospectos.create') }}"
        class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900"
      >
        Nuevo prospecto
      </a>
    </div>
  </div>

  <div class="rounded-lg border border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-900/50 text-left">
        <tr>
          <th class="p-3 text-gray-400">ID</th>
          <th class="p-3 text-gray-400">Nombre</th>
          <th class="p-3 text-gray-400">Email</th>
          <th class="p-3 text-gray-400">Teléfono</th>
          <th class="p-3 text-gray-400">Localidad</th>
          <th class="p-3 text-gray-400">CP</th>
          <th class="p-3 text-right text-gray-400">Acciones</th>
        </tr>
      </thead>
      <tbody class="bg-black/10">
        @forelse($prospectos as $c)
          <tr class="border-t border-gray-800 hover:bg-gray-900/40">
            <td class="p-3 align-top text-gray-400">#{{ $c->id }}</td>
            <td class="p-3 align-top">
              <div class="font-medium">{{ $c->name ?? $c->nombre ?? '—' }}</div>
              <div class="text-xs text-gray-400">{{ $c->email ?? '' }}</div>
            </td>
            <td class="p-3 align-top text-gray-300">{{ $c->email ?? '—' }}</td>
            <td class="p-3 align-top text-gray-300">{{ $c->cel1 ?? $c->cel ?? $c->phone1 ?? '—' }}</td>
            <td class="p-3 align-top text-gray-300">{{ $c->localidad ?? $c->city ?? '—' }}</td>
            <td class="p-3 align-top text-gray-300">{{ $c->cp ?? $c->zipcode ?? $c->postal_code ?? '—' }}</td>
            <td class="p-3 align-top">
              <div class="flex justify-end gap-2">
                <a
                  href="{{ route('servicios.create', ['cliente' => $c->id, 'pref_estado' => 'INSTALAR']) }}"
                  class="rounded-lg border border-sky-700/60 px-3 py-1.5 text-xs hover:bg-sky-900/20"
                >
                  Servicio
                </a>
                <form action="{{ route('clientes.prospectos.activate', $c->id) }}" method="post">
                  @csrf
                  <button
                    class="rounded-lg border border-emerald-700/60 px-3 py-1.5 text-xs hover:bg-emerald-900/20"
                  >
                    Cargar
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="p-4 text-center text-gray-400">No hay prospectos cargados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if(method_exists($prospectos, 'links'))
    <div class="mt-4">{{ $prospectos->appends(request()->except('page'))->links() }}</div>
  @endif
@endsection
