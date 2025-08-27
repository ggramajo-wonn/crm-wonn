@extends('layouts.app')

@section('content')
@php
  // Normalizamos variables que pueden venir con otros nombres
  $cliente = $cliente ?? $client ?? null;

  // Servicios: usa el que pase el controlador o, si no, la relación del cliente
  $servs = $servicios ?? ($services ?? ($cliente?->services ?? collect()));

  // Últimos registros (fallback si no vinieron)
  $ultimaFactura = $ultimaFactura ?? ($cliente?->invoices()->latest('issued_at')->first());
  $ultimoPago    = $ultimoPago    ?? ($cliente?->payments()->latest('paid_at')->first());

  // Cálculo de saldo (facturas - pagos acreditados)
  $totalFact = $cliente?->invoices()->sum('total') ?? 0;
  $totalPago = $cliente?->payments()->where('status','acreditado')->sum('amount') ?? 0;
  $saldo = ($saldoCliente ?? null) ?? ($totalFact - $totalPago);

  // GPS: puede venir en un solo campo "gps" ("lat,lng") o en lat/lng separados
  $gpsRaw   = trim((string)($cliente->gps ?? ($cliente->gps_coords ?? '')));
  $lat = $cliente->gps_lat ?? null;
  $lng = $cliente->gps_lng ?? null;
  if (!$lat || !$lng) {
      if ($gpsRaw && str_contains($gpsRaw, ',')) {
          [$lat, $lng] = array_map('trim', explode(',', $gpsRaw, 2));
      }
  }
  $hasGps = $lat && $lng;
@endphp

<div class="flex items-start justify-between mb-6">
  <h1 class="text-2xl font-bold">
    {{ strtoupper($cliente->name ?? $cliente->apellido_nombre ?? 'CLIENTE') }}
    <span class="text-sm font-normal text-gray-400 ml-2">(ID: #{{ $cliente->id }})</span>
  </h1>

  <div class="flex items-center gap-2">
    {{-- Saldo del cliente --}}
    @php
      $pillClass = $saldo > 0 ? 'bg-red-900/30 text-red-200' : ($saldo < 0 ? 'bg-emerald-900/30 text-emerald-300' : 'bg-gray-800 text-gray-300');
      $textoSaldo = $saldo > 0 ? 'Debe' : ($saldo < 0 ? 'A favor' : 'En cero');
    @endphp
    <span class="px-2 py-1 rounded text-xs {{$pillClass}}">
      {{ $textoSaldo }}
      @if($saldo !== 0)
        — $ {{ number_format(abs($saldo),2,',','.') }}
      @endif
    </span>

    <a href="{{ route('facturas.create') }}?client_id={{ $cliente->id }}"
       class="rounded-lg border border-gray-700 px-3 py-2 hover:bg-gray-900">
      Nueva factura
    </a>

    <a href="{{ route('pagos.create') }}?client_id={{ $cliente->id }}"
       class="rounded-lg bg-primary-600 hover:bg-primary-500 px-3 py-2">
      Registrar pago
    </a>
  </div>
</div>

{{-- DATOS DEL CLIENTE --}}
<div class="rounded-2xl bg-gray-900 border border-gray-800 p-5 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Datos</h2>

    <div class="flex items-center gap-2">
      {{-- Estado --}}
      @php
        $estado = $cliente->status ?? 'activo';
        $estadoClass = $estado === 'activo' ? 'bg-emerald-900/30 text-emerald-300' : 'bg-gray-700 text-gray-300';
      @endphp
      <span class="px-2 py-1 rounded text-xs {{ $estadoClass }}">{{ ucfirst($estado) }}</span>

      {{-- Ver ubicación --}}
      <button id="btn-map-open" type="button"
              class="rounded-lg border border-gray-700 px-3 py-2 hover:bg-gray-900">
        Ver ubicación
      </button>

      {{-- Editar --}}
      <a href="{{ route('clientes.edit', $cliente) }}"
         class="rounded-lg border border-gray-700 px-3 py-2 hover:bg-gray-900">
        Editar
      </a>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-8">
    <div>
      <div class="text-sm text-gray-400">DNI</div>
      <div class="mt-1">{{ $cliente->dni ?? '-' }}</div>

      <div class="text-sm text-gray-400 mt-4">Cel 1</div>
      <div class="mt-1">{{ $cliente->cel1 ?? $cliente->phone ?? '-' }}</div>

      <div class="text-sm text-gray-400 mt-4">Cel 2</div>
      <div class="mt-1">{{ $cliente->cel2 ?? $cliente->phone2 ?? '-' }}</div>

      <div class="text-sm text-gray-400 mt-4">Email</div>
      <div class="mt-1">{{ $cliente->email ?? '-' }}</div>
    </div>

    <div>
      <div class="text-sm text-gray-400">Dirección</div>
      <div class="mt-1">{{ $cliente->address ?? $cliente->direccion ?? '-' }}</div>

      <div class="text-sm text-gray-400 mt-4">Localidad</div>
      <div class="mt-1">{{ $cliente->localidad ?? $cliente->city ?? '-' }}</div>

      <div class="text-sm text-gray-400 mt-4">CP</div>
      <div class="mt-1">{{ $cliente->cp ?? '-' }}</div>

      <div class="text-sm text-gray-400 mt-4">GPS</div>
      <div class="mt-1">
        @if($hasGps)
          <a href="https://maps.google.com/?q={{ $lat }},{{ $lng }}" class="text-primary-400 hover:underline" target="_blank" rel="noopener">
            {{ $lat }}, {{ $lng }}
          </a>
        @else
          <span class="text-gray-400">—</span>
        @endif
      </div>
    </div>
  </div>

  {{-- Botón eliminar (mantengo tu posición/estilo) --}}
  <div class="mt-4 flex justify-end">
    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
          onsubmit="return confirm('¿Eliminar este cliente? Se borrarán todos sus datos asociados.');">
      @csrf @method('DELETE')
      <button class="rounded-lg border border-red-900/60 text-red-300 px-3 py-2 hover:bg-red-900/10">
        Eliminar cliente
      </button>
    </form>
  </div>
</div>

{{-- SERVICIOS CONTRATADOS --}}
<div class="rounded-2xl bg-gray-900 border border-gray-800 p-5 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Servicios contratados</h2>
    <a href="{{ route('servicios.create') }}?client_id={{ $cliente->id }}"
       class="rounded-lg border border-gray-700 px-3 py-2 hover:bg-gray-900">
      Agregar
    </a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-left">
      <thead class="bg-gray-800/50 text-gray-300">
        <tr>
          <th class="p-3">Servicio</th>
          <th class="p-3">Precio</th>
          <th class="p-3">IP</th>
          <th class="p-3">Router</th>
          <th class="p-3">Estado</th>
          <th class="p-3">Acción</th>
        </tr>
      </thead>
      <tbody>
        @forelse($servs as $s)
          <tr class="border-t border-gray-800">
            <td class="p-3">{{ $s->name ?? '-' }}</td>
            <td class="p-3">$ {{ number_format($s->price ?? 0, 2, ',', '.') }}</td>
            <td class="p-3">{{ $s->ip ?? '—' }}</td>
            <td class="p-3">{{ $s->router_name ?? $s->router ?? '—' }}</td>
            <td class="p-3">
              @php
                $st = $s->status ?? 'activo';
                $stClass = $st === 'activo' ? 'bg-emerald-900/30 text-emerald-300'
                                             : 'bg-amber-900/30 text-amber-200';
              @endphp
              <span class="px-2 py-1 rounded text-xs {{ $stClass }}">{{ ucfirst($st) }}</span>
            </td>
            <td class="p-3">
              <a href="{{ route('servicios.edit', $s) }}"
                 class="rounded-lg border border-gray-700 px-3 py-1 text-sm hover:bg-gray-900">Gestionar</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="p-4 text-gray-400">Sin servicios aún.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- FACTURAS (ÚLTIMA) --}}
<div class="rounded-2xl bg-gray-900 border border-gray-800 p-5 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Facturas (última)</h2>
    <a href="{{ route('facturas.index') }}?client_id={{ $cliente->id }}" class="text-sm text-gray-400 hover:text-primary-400">Ver todas</a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-left">
      <thead class="bg-gray-800/50 text-gray-300">
        <tr>
          <th class="p-3">#</th>
          <th class="p-3">Fecha</th>
          <th class="p-3">Total</th>
          <th class="p-3">Pagado</th>
          <th class="p-3">Saldo</th>
          <th class="p-3">Estado</th>
        </tr>
      </thead>
      <tbody>
        @if($ultimaFactura)
          @php
            $pagadoF = $ultimaFactura->payments()->where('status','acreditado')->sum('amount');
            $saldoF  = max(0, (float)$ultimaFactura->total - (float)$pagadoF);
            $estadoF = $ultimaFactura->status;
            $estadoClassF = $estadoF === 'pagada' ? 'bg-emerald-900/30 text-emerald-300'
                          : ($estadoF === 'vencida' ? 'bg-red-900/30 text-red-200' : 'bg-gray-700 text-gray-300');
          @endphp
          <tr class="border-t border-gray-800">
            <td class="p-3">#{{ $ultimaFactura->id }}</td>
            <td class="p-3">{{ optional($ultimaFactura->issued_at)->format('d/m/Y') }}</td>
            <td class="p-3">$ {{ number_format($ultimaFactura->total,2,',','.') }}</td>
            <td class="p-3">$ {{ number_format($pagadoF,2,',','.') }}</td>
            <td class="p-3">$ {{ number_format($saldoF,2,',','.') }}</td>
            <td class="p-3">
              <span class="px-2 py-1 rounded text-xs {{ $estadoClassF }}">
                {{ ucfirst($estadoF) }} @if($estadoF!=='pagada' && $pagadoF>0 && $saldoF>0) (parcial) @endif
              </span>
            </td>
          </tr>
        @else
          <tr><td colspan="6" class="p-4 text-gray-400">Sin facturas para mostrar.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
</div>

{{-- PAGOS (ÚLTIMO) --}}
<div class="rounded-2xl bg-gray-900 border border-gray-800 p-5 mb-2">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Pagos (último)</h2>
    <a href="{{ route('pagos.index') }}?client_id={{ $cliente->id }}" class="text-sm text-gray-400 hover:text-primary-400">Ver todos</a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-left">
      <thead class="bg-gray-800/50 text-gray-300">
        <tr><th class="p-3">Factura</th>
          <th class="p-3">Fecha</th>
          <th class="p-3">Monto</th>
          <th class="p-3">Fuente</th>
          <th class="p-3">Ref.</th>
          <th class="p-3">Estado</th></tr>
      </thead>
      <tbody>
        @if($ultimoPago)
          @php
            $st = $ultimoPago->status;
            $stClass = $st==='acreditado' ? 'bg-emerald-900/30 text-emerald-300'
                      : ($st==='duplicado' ? 'bg-amber-900/30 text-amber-200'
                      : ($st==='fallido' ? 'bg-red-900/30 text-red-200' : 'bg-gray-700 text-gray-300'));
          @endphp
          <tr class="border-t border-gray-800">
            <td class="p-3">{{ $ultimoPago->invoice_id ? '#'.$ultimoPago->invoice_id : '—' }}</td>
<td class="p-3">{{ optional($ultimoPago->paid_at)->format('d/m/Y H:i') }}</td>
<td class="p-3">$ {{ number_format($ultimoPago->amount,2,',','.') }}</td>
<td class="p-3">{{ $ultimoPago->source ?? '—' }}</td>
<td class="p-3">{{ $ultimoPago->reference ?? '—' }}</td>
<td class="p-3"><span class="px-2 py-1 rounded text-xs {{ $stClass }}">{{ ucfirst($st) }}</span></td></tr>
        @else
          <tr><td colspan="6" class="p-4 text-gray-400">Sin pagos para mostrar.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
</div>

{{-- MODAL MAPA (centrado) --}}
<div id="map-modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/70" id="map-backdrop"></div>
  <div class="relative max-w-5xl mx-auto mt-20 sm:mt-28 lg:mt-36 bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800">
      <div class="font-semibold">Ubicación del cliente</div>
      <button id="map-close" class="text-gray-400 hover:text-gray-200 px-2 py-1">✕</button>
    </div>
    <div class="w-[90vw] max-w-5xl h-[60vh]">
      @if($hasGps)
        <iframe
          id="map-frame"
          src="https://maps.google.com/maps?q={{ $lat }},{{ $lng }}&hl=es&z=18&output=embed&t=k"
          class="w-full h-full"
          style="border:0"
          allowfullscreen
          loading="lazy">
        </iframe>
      @else
        <div class="flex items-center justify-center h-full text-gray-400">
          Sin coordenadas GPS cargadas para este cliente.
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Script modal básico (sin Alpine) --}}
<script>
  (function () {
    const openBtn  = document.getElementById('btn-map-open');
    const modal    = document.getElementById('map-modal');
    const closeBtn = document.getElementById('map-close');
    const backdrop = document.getElementById('map-backdrop');

    function openModal() {
      modal.classList.remove('hidden');
    }
    function closeModal() {
      modal.classList.add('hidden');
    }
    if (openBtn)  openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
  })();
</script>
@endsection
