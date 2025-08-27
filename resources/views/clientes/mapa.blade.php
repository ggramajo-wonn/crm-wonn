@extends('layouts.app')

@section('content')
@php
    // API key
    $gmapsKey = optional($company)->google_maps_key
        ?? optional($company)->gmaps_api_key
        ?? optional($company)->maps_api_key
        ?? optional($company)->google_api_key
        ?? config('services.google.maps.key')
        ?? env('GOOGLE_MAPS_API_KEY');

    // Normalizamos teléfonos (varios nombres posibles) y armamos el array para JS
    $CLIENTS = ($clients ?? collect())->map(function ($c) {
        $cel1 = $c->cel1 ?? $c->cel_1 ?? $c->cel ?? $c->phone1 ?? $c->phone_1 ?? $c->telefono1 ?? $c->celular1 ?? $c->tel1 ?? $c->movil1 ?? null;
        $cel2 = $c->cel2 ?? $c->cel_2 ?? $c->phone2 ?? $c->phone_2 ?? $c->telefono2 ?? $c->celular2 ?? $c->tel2 ?? $c->movil2 ?? null;

        return [
            'lat'       => is_numeric($c->lat ?? null) ? (float) $c->lat : null,
            'lng'       => is_numeric($c->lng ?? null) ? (float) $c->lng : null,
            'name'      => $c->name ?? null,
            'localidad' => $c->localidad ?? null,
            'address'   => $c->address ?? null,
            'saldo'     => is_numeric($c->saldo ?? null) ? (float) $c->saldo : 0,
            'cel1'      => $cel1,
            'cel2'      => $cel2,
        ];
    })->values();
@endphp

<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Mapa de clientes</h1>
  <form method="GET" action="{{ url()->current() }}" class="flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar nombre, email, localidad"
           class="w-80 rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm">
    <button class="rounded-lg border border-gray-700 px-3 py-2 text-sm hover:bg-gray-900">Filtrar</button>
    <a href="{{ url()->current() }}"
       class="rounded-lg border border-gray-800/60 px-3 py-2 text-sm text-gray-300 hover:bg-gray-900">Limpiar</a>
  </form>
</div>

<div id="map" class="rounded-xl border border-gray-800" style="height: calc(100vh - 240px);"></div>

<script>
  const CLIENTS = @json($CLIENTS);

  function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), {
      center: {lat: -24.2, lng: -64.3},
      zoom: 12,
      mapTypeId: 'roadmap'
    });

    const bounds = new google.maps.LatLngBounds();
    const fmt = (n) => new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2 }).format(Number(n ?? 0));

    const infowin = new google.maps.InfoWindow();
    let hasAny = false;

    for (const d of CLIENTS) {
      if (Number.isFinite(d.lat) && Number.isFinite(d.lng)) {
        const pos = { lat: parseFloat(d.lat), lng: parseFloat(d.lng) };
        const marker = new google.maps.Marker({ position: pos, map });

        marker.addListener('click', () => {
          const saldo = Number(d.saldo ?? 0);
          // ROJO si es a pagar (saldo negativo), VERDE si es a favor (saldo positivo)
          const saldoColor = saldo < 0 ? '#dc2626' : (saldo > 0 ? '#16a34a' : '#111827');
          const phones = [d.cel1, d.cel2].filter(Boolean).join(' / ');
          const phoneLine = phones ? `<div>Cel: ${phones}</div>` : '';

          infowin.setContent(`
            <div style="min-width:240px;color:#111">
              <div style="font-weight:700">${d.name ?? ''}</div>
              <div>${d.localidad ?? ''}${d.address ? ' · ' + d.address : ''}</div>
              ${phoneLine}
              <div>Saldo: <span style="color:${saldoColor}">$ ${fmt(saldo)}</span></div>
            </div>
          `);
          infowin.open(map, marker);
        });

        bounds.extend(pos);
        hasAny = true;
      }
    }

    if (hasAny) {
      map.fitBounds(bounds);
    }
  }
  window.initMap = initMap;
</script>

@if($gmapsKey)
<script src="https://maps.googleapis.com/maps/api/js?key={{ $gmapsKey }}&callback=initMap" async defer></script>
@endif
@endsection
