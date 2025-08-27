@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
  <div>
    <h1 class="text-2xl font-bold">OLT #{{ $olt->id }} — {{ $olt->name }}</h1>
    <p class="text-sm text-gray-400">Localidad: {{ $olt->localidad }}</p>
  </div>
  <div class="flex items-center gap-2">
    <a href="{{ route('olts.naps.create', $olt->id) }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">+ NAP</a>
    <a href="{{ route('olts.index') }}" class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Volver</a>
  </div>
</div>

@php
  // Unificamos los mensajes flash para evitar duplicados (ok/success/status)
  $flashOk = session('ok') ?? session('success') ?? session('status');
@endphp
@if($flashOk)
  <div class="mb-4 rounded border border-green-800 bg-green-900/30 px-3 py-2 text-green-200">{{ $flashOk }}</div>
@endif
@if(session('error'))
  <div class="mb-4 rounded border border-red-800 bg-red-900/30 px-3 py-2 text-red-200">{{ session('error') }}</div>
@endif

{{-- Sin mapa: solo listado/gestión de NAPs --}}
<div class="space-y-4">
  @forelse($naps as $nap)
  <div class="rounded-lg border border-gray-800 p-4">
    <div class="flex items-center justify-between mb-3">
      <div>
        <div class="text-sm text-gray-400">ID: O{{ $olt->id }}-N{{ $nap->id }}</div>
        <div class="text-lg font-semibold">{{ $nap->name }}</div>
        <div class="text-sm text-gray-400">Ubicación: {{ $nap->ubicacion }}</div>
        <div class="text-sm text-gray-400">GPS: {{ $nap->gps }}</div>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('olts.naps.edit', [$olt->id, $nap->id]) }}" class="text-sky-400 hover:underline">Editar</a>
        <form method="POST" action="{{ route('olts.naps.destroy', [$olt->id, $nap->id]) }}" onsubmit="return confirm('¿Eliminar NAP?');">
          @csrf @method('DELETE')
          <button class="text-red-400 hover:underline">Eliminar</button>
        </form>
      </div>
    </div>

    {{-- Grilla de puertos --}}
    <div class="grid grid-cols-8 md:grid-cols-12 gap-2 mt-2">
      @php
        $napUsage = $usage[$nap->id] ?? [];
      @endphp
      @for($i=1; $i<= (int)$nap->puertos; $i++)
        @php
          $used = $napUsage[$i]['used'] ?? false;
          $label = $napUsage[$i]['label'] ?? 'Libre';
        @endphp
        <button
          class="relative flex items-center justify-center w-9 h-9 rounded-full border text-xs
                 {{ $used ? 'bg-red-600/80 border-red-500 text-white' : 'bg-green-700/60 border-green-600 text-white' }}"
          title="{{ $used ? $label : 'Puerto libre' }}"
          data-label="{{ $label }}">
          {{ $i }}
        </button>
      @endfor
    </div>
  </div>
  @empty
  <div class="p-6 text-center text-gray-400 border border-gray-800 rounded-lg">No hay NAPs en esta OLT.</div>
  @endforelse
</div>
@endsection
