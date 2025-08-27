@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Clientes</h1>

    <div class="flex items-center gap-2">
      <form method="GET" action="{{ route('clientes.index') }}" class="flex items-center gap-2">
        <input
          type="text"
          name="q"
          value="{{ request('q') }}"
          placeholder="Buscar: nombre, cel o email"
          class="w-80 rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm"
        >
        <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">
          Filtrar
        </button>
        <a href="{{ route('clientes.index') }}" class="rounded-lg border border-gray-800/60 px-3 py-2 text-sm text-gray-300 hover:bg-gray-900">
          Limpiar
        </a>
      </form>

      <a href="{{ route('clientes.create') }}" class="ml-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white px-3 py-2 text-sm">
        Nuevo
      </a>
    </div>
  </div>

  @php
    // Helper para componer URLs de ordenamiento preservando filtros/paginación
    function sort_url($column) {
      $current = request('sort', 'id');
      $dir     = request('dir', 'asc');
      $nextDir = ($current === $column && $dir === 'asc') ? 'desc' : 'asc';
      return request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir, 'page' => null]);
    }
    function sort_icon($column) {
      $current = request('sort', 'id');
      $dir     = request('dir', 'asc');
      if ($current !== $column) return '↕';
      return $dir === 'asc' ? '↑' : '↓';
    }
  @endphp

  <div class="overflow-x-auto rounded-xl border border-gray-800">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-900 text-gray-400">
        <tr>
          <th class="p-3 text-left w-[80px]">
            <a href="{{ sort_url('id') }}" class="inline-flex items-center gap-1 hover:text-white">
              <span>ID</span><span class="opacity-60">{{ sort_icon('id') }}</span>
            </a>
          </th>
          <th class="p-3 text-left">Nombre</th>
          <th class="p-3 text-left w-[160px]">
            <a href="{{ sort_url('localidad') }}" class="inline-flex items-center gap-1 hover:text-white">
              <span>Localidad</span><span class="opacity-60">{{ sort_icon('localidad') }}</span>
            </a>
          </th>
          <th class="p-3 text-left w-[150px]">Cel 1</th>
          <th class="p-3 text-left w-[150px]">Cel 2</th>
          <th class="p-3 text-left w-[120px]">
            <a href="{{ sort_url('estado') }}" class="inline-flex items-center gap-1 hover:text-white">
              <span>Estado</span><span class="opacity-60">{{ sort_icon('estado') }}</span>
            </a>
          </th>
          <th class="p-3 text-right w-[140px]">
            <a href="{{ sort_url('saldo') }}" class="inline-flex items-center gap-1 hover:text-white float-right">
              <span>Saldo</span><span class="opacity-60">{{ sort_icon('saldo') }}</span>
            </a>
          </th>
          <th class="p-3 text-center w-[120px]">Acción</th>
        </tr>
      </thead>

      <tbody>
        @forelse ($items as $c)
          @php
            $saldo = $c->saldo ?? 0;
            $isDebt = $saldo < 0;
          @endphp
          <tr class="border-t border-gray-800 hover:bg-gray-900/40">
            <td class="p-3 align-top text-gray-400">#{{ $c->id }}</td>
            <td class="p-3 align-top">
              <div class="font-medium">{{ $c->name ?? $c->nombre ?? '—' }}</div>
              <div class="text-xs text-gray-400">{{ $c->email ?? '' }}</div>
            </td>
            <td class="p-3 align-top">{{ $c->localidad ?? '—' }}</td>
            <td class="p-3 align-top">{{ $c->cel ?? $c->cel1 ?? '—' }}</td>
            <td class="p-3 align-top">{{ $c->cel2 ?? '—' }}</td>
            <td class="p-3 align-top">
              @php $active = ($c->estado ?? ($c->status ?? '')) === 'Activo'; @endphp
              <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $active ? 'bg-emerald-900/40 text-emerald-300' : 'bg-gray-800 text-gray-300' }}">
                {{ $c->estado ?? $c->status ?? '—' }}
              </span>
            </td>
            <td class="p-3 align-top text-right">
              <div class="{{ $isDebt ? 'text-rose-300' : 'text-emerald-300' }}">
                $ {{ number_format(abs($saldo), 2, ',', '.') }}
              </div>
              <div class="text-xs text-gray-400">{{ $isDebt ? '— a abonar' : 'a favor' }}</div>
            </td>
            <td class="p-3 align-top text-center">
              <a href="{{ route('clientes.show', $c->id) }}" class="rounded-lg bg-sky-700 hover:bg-sky-600 text-white px-3 py-1.5 text-xs">
                Acceder
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="p-4 text-center text-gray-400">Sin clientes.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if(method_exists($items, 'links'))
    <div class="mt-4">{{ $items->appends(request()->except('page'))->links() }}</div>
  @endif
@endsection
