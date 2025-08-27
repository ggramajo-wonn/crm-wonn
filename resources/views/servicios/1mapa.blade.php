@extends('layouts.app')

@section('content')
@php
  $gkey = optional($company)->google_maps_key
      ?? optional($company)->maps_api_key
      ?? config('services.google.maps_key')
      ?? env('GOOGLE_MAPS_API_KEY');
@endphp

<div class="mb-3 flex items-center justify-between">
  <h1 class="text-2xl font-bold text-slate-200">Mapa de servicios</h1>
</div>

<div id="map" class="rounded-xl border border-slate-800" style="height: calc(100vh - 240px);"></div>

<script>
  // ====== Dataset ======
  const RAW  = @json($services ?? []);
  const NRAW = @json($naps ?? []);

  function parseLatLng(v){
    if (v == null) return {lat:null,lng:null};
    if (typeof v === 'string'){
      const p = v.split(',').map(x => Number(String(x).trim()));
      return {lat: Number.isFinite(p[0]) ? p[0] : null, lng: Number.isFinite(p[1]) ? p[1] : null};
    }
    const lat = Number(v.lat ?? v.latitude ?? v.latitud ?? v.latLng?.lat ?? null);
    const lng = Number(v.lng ?? v.longitude ?? v.longitud ?? v.latLng?.lng ?? null);
    return {lat: Number.isFinite(lat)?lat:null, lng: Number.isFinite(lng)?lng:null};
  }

  const SERVICES = (RAW || []).map(s => {
    const {lat, lng} = s.lat!=null && s.lng!=null ? {lat:Number(s.lat), lng:Number(s.lng)}
                       : parseLatLng(s.gps ?? s.location ?? '');
    return {
      id: s.id,
      lat, lng,
      name: s.client_name || s.name || ('Servicio #' + (s.id ?? '')),
      address: s.address || '',
      localidad: s.localidad || '',
      cel1: s.cel1 || s.cel_1 || '',
      cel2: s.cel2 || s.cel_2 || '',
      ip: s.ip || '',
      plan: s.plan || '',
      saldo: Number(s.saldo ?? 0),
      ftth: !!(s.ftth ?? false),
      nap_id: s.nap_id ?? null,
    };
  }).filter(d => Number.isFinite(d.lat) && Number.isFinite(d.lng));

  const NAPS = (NRAW || []).map(n => {
    const {lat, lng} = n.lat!=null && n.lng!=null ? {lat:Number(n.lat), lng:Number(n.lng)}
                       : parseLatLng(n.gps ?? n.location ?? '');
    return {
      id: n.id,
      lat, lng,
      name: n.name || ('NAP #' + (n.id ?? '')),
      puertos: Number(n.puertos ?? 0),
    };
  }).filter(n => Number.isFinite(n.lat) && Number.isFinite(n.lng));

  function ARS(n){
    try { return new Intl.NumberFormat('es-AR',{style:'currency',currency:'ARS'}).format(Number(n||0)); }
    catch(e){ return '$ ' + (Math.round(Number(n||0)*100)/100).toLocaleString('es-AR'); }
  }

  function cardService(d){
    const saldoClass = d.saldo < 0 ? 'style="color:#ef4444"' : (d.saldo > 0 ? 'style="color:#22c55e"' : '');
    const cel = (d.cel1 || d.cel2) ? `Cel: ${[d.cel1, d.cel2].filter(Boolean).join(' / ')}` : '';
    return `<div style="font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#0f172a">
      <div style="font-weight:700;font-size:16px;margin-bottom:4px">${d.name}</div>
      <div style="font-size:13px;margin-bottom:2px">${d.localidad} · ${d.address}</div>
      ${cel ? `<div style="font-size:13px;margin-bottom:2px">${cel}</div>` : ''}
      ${d.ip ? `<div style="font-size:13px;margin-bottom:2px">IP: ${d.ip}</div>` : ''}
      ${d.plan ? `<div style="font-size:13px;margin-bottom:2px">Plan: ${d.plan}</div>` : ''}
      <div style="font-size:13px;margin-top:4px">Saldo: <span ${saldoClass}>${ARS(d.saldo)}</span></div>
    </div>`;
  }

  function cardNap(n){
    return `<div style="font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#0f172a">
      <div style="font-weight:700;font-size:15px;margin-bottom:4px">NAP #${n.id} — ${n.name}</div>
      <div style="font-size:13px">Puertos: ${n.puertos || '—'}</div>
    </div>`;
  }

  function makeLayerControl(){
    const wrap = document.createElement('div');
    wrap.style.background = '#0b1220';
    wrap.style.border = '1px solid #1f2a44';
    wrap.style.borderRadius = '8px';
    wrap.style.padding = '6px 10px';
    wrap.style.marginLeft = '8px';   // pegado al control "Mapa/Satélite"
    wrap.style.color = '#dbe2f1';
    wrap.style.font = '400 13px Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif';
    wrap.style.display = 'flex';
    wrap.style.gap = '12px';
    wrap.style.alignItems = 'center';

    function add(label, id, checked){
      const lab = document.createElement('label');
      lab.style.display = 'flex';
      lab.style.alignItems = 'center';
      lab.style.gap = '6px';
      lab.style.cursor = 'pointer';
      const cb = document.createElement('input');
      cb.type = 'checkbox';
      cb.id = id;
      cb.checked = checked;
      lab.appendChild(cb);
      lab.appendChild(document.createTextNode(label));
      wrap.appendChild(lab);
      return cb;
    }

    return { el: wrap, add };
  }

  function initMap(){
    const hasServices = SERVICES.length > 0;
    const center = hasServices ? {lat:SERVICES[0].lat, lng:SERVICES[0].lng} : {lat:-24.2, lng:-64.3};

    const map = new google.maps.Map(document.getElementById('map'), {
      center, zoom: hasServices ? 12 : 5,
      mapTypeId: 'roadmap',
      streetViewControl: false,
      fullscreenControl: true,
      mapTypeControl: true,
      mapTypeControlOptions: { position: google.maps.ControlPosition.TOP_LEFT },
    });

    const iw = new google.maps.InfoWindow();

    // Markers Servicios
    const svcMarkers = SERVICES.map(s => {
      const m = new google.maps.Marker({ position:{lat:s.lat,lng:s.lng}, map, icon: undefined });
      m.addListener('click', () => { iw.setContent(cardService(s)); iw.open({anchor:m, map}); });
      return m;
    });

    // Markers NAPs
    const napIcon = {
      path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
      fillColor: '#06b6d4', fillOpacity: 0.9,
      strokeColor: '#0e7490', strokeWeight: 1,
      scale: 5,
    };
    const napMarkers = NAPS.map(n => {
      const m = new google.maps.Marker({ position:{lat:n.lat,lng:n.lng}, map, icon: napIcon });
      m.addEventListener && m.addEventListener('click', () => { iw.setContent(cardNap(n)); iw.open({anchor:m, map}); });
      m.addListener && m.addListener('click', () => { iw.setContent(cardNap(n)); iw.open({anchor:m, map}); });
      return m;
    });

    // Enlaces Servicio <-> NAP
    const napIndex = new Map(NAPS.map(n => [String(n.id), n]));
    const links = [];
    SERVICES.forEach(s => {
      if (!s.ftth || !s.nap_id) return;
      const nap = napIndex.get(String(s.nap_id));
      if (!nap) return;
      const poly = new google.maps.Polyline({
        path: [{lat:s.lat,lng:s.lng},{lat:nap.lat,lng:nap.lng}],
        geodesic: true,
        strokeColor: '#f97316', strokeOpacity: .9, strokeWeight: 2.5,
        map
      });
      links.push(poly);
    });

    // ===== Control de capas al lado de "Mapa / Satélite" =====
    const ctrl = makeLayerControl();
    const chkServices = ctrl.add('Servicios', 'chkServices', true);
    const chkNaps     = ctrl.add('NAPs',      'chkNaps',     true);
    const chkLinks    = ctrl.add('Enlaces',   'chkLinks',    true);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(ctrl.el);

    function setVisible(arr, visible){
      arr.forEach(x => { if (x.setMap) x.setMap(visible ? map : null); });
    }
    function refreshLayers(){
      setVisible(svcMarkers, !!chkServices?.checked);
      setVisible(napMarkers, !!chkNaps?.checked);
      setVisible(links, !!chkLinks?.checked);
    }
    chkServices.addEventListener('change', refreshLayers);
    chkNaps.addEventListener('change', refreshLayers);
    chkLinks.addEventListener('change', refreshLayers);
    refreshLayers();

    // Fit bounds
    const bounds = new google.maps.LatLngBounds();
    let hasAny = false;
    [...svcMarkers, ...napMarkers].forEach(m => {
      const pos = m.getPosition && m.getPosition();
      if (pos) { bounds.extend(pos); hasAny = true; }
    });
    if (hasAny) map.fitBounds(bounds, { top:60, right:60, bottom:60, left:60 });
  }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $gkey }}&callback=initMap"></script>
@endsection
