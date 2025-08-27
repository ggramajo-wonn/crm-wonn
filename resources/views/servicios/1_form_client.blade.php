@csrf

@php
  // Si venís desde el menú del cliente, fijamos el cliente y enviamos hidden
  $fixedClientId = $cliente->id ?? null;

  // Armar valor único de GPS (si no hay 'gps', usar lat/lng si existen)
  $gpsValue = old('gps');
  if ($gpsValue === null) {
      $gpsValue = $service->gps
          ?? (isset($service->gps_lat, $service->gps_lng) ? ($service->gps_lat . ',' . $service->gps_lng) : null)
          ?? (isset($service->lat, $service->lng) ? ($service->lat . ',' . $service->lng) : '');
  }
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="space-y-2">
    <label for="client_id" class="block text-sm text-gray-300">Cliente</label>
    <select name="client_id" id="client_id"
            class="form-select w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2"
            @if($fixedClientId) disabled @endif>
      @foreach(($clients ?? []) as $c)
        <option value="{{ $c->id }}" @selected(old('client_id', $service->client_id ?? $fixedClientId) == $c->id)>
          {{ $c->name }}
        </option>
      @endforeach
    </select>
    @if($fixedClientId)
      <input type="hidden" name="client_id" value="{{ $fixedClientId }}">
    @endif
  </div>

  <div class="space-y-2">
    <label for="plan_id" class="block text-sm text-gray-300">Plan</label>
    <select name="plan_id" id="plan_id"
            class="form-select w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2">
      @foreach(($planes ?? $plans ?? []) as $p)
        <option value="{{ $p->id }}" @selected(old('plan_id', $service->plan_id ?? null) == $p->id)>
          {{ $p->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="space-y-2">
    <label for="price" class="block text-sm text-gray-300">Precio (por defecto del plan)</label>
    <input type="text" name="price" id="price"
           class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2"
           value="{{ old('price', $service->price ?? '') }}">
  </div>

  <div class="space-y-2">
    <label for="status" class="block text-sm text-gray-300">Estado</label>
    <select name="status" id="status"
            class="form-select w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2">
      <option value="activo" @selected(old('status', $service->status ?? '') == 'activo')>Activo</option>
      <option value="suspendido" @selected(old('status', $service->status ?? '') == 'suspendido')>Suspendido</option>
    </select>
  </div>

  <div class="space-y-2">
    <label for="ip" class="block text-sm text-gray-300">IP</label>
    <input type="text" name="ip" id="ip"
           class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2"
           placeholder="Ej: 192.168.1.100"
           value="{{ old('ip', $service->ip ?? '') }}">
  </div>

  <div class="space-y-2">
    <label for="router" class="block text-sm text-gray-300">Router</label>
    @if(!empty($routers))
      <select name="router" id="router"
              class="form-select w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2">
        @foreach($routers as $r)
          @php
            $value = is_array($r) ? ($r['name'] ?? $r['label'] ?? $r['ip'] ?? $r['id']) : (string) $r;
          @endphp
          <option value="{{ $value }}" @selected(old('router', $service->router ?? '') == $value)>
            {{ $value }}
          </option>
        @endforeach
      </select>
    @else
      <input type="text" name="router" id="router"
             class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2"
             placeholder="Nombre/identificador del router"
             value="{{ old('router', $service->router ?? '') }}">
    @endif
  </div>

  <div class="space-y-2 md:col-span-2">
    <label for="gps" class="block text-sm text-gray-300">GPS (Lat,Lng)</label>
  @include('servicios._ftth_fields')

    <input type="text" name="gps" id="gps"
           class="form-input w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2"
           placeholder="Ejemplo: -23.12638,-64.323844 (sin espacios)"
           value="{{ $gpsValue }}">
  </div>
</div>

<div class="mt-6 flex items-center gap-3">
  <button type="submit" class="btn btn-primary bg-[#0EA5B7] hover:opacity-90 text-white font-medium px-4 py-2 rounded-lg">
    Guardar
  </button>
  <a href="{{ $returnTo ?? url()->previous() }}" class="btn btn-secondary border border-gray-600 text-gray-100 px-4 py-2 rounded-lg">
    Cancelar
  </a>
</div>
