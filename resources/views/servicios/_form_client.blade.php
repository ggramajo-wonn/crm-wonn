@php
    /** @var \App\Models\Service|null $service */
    $isEdit = isset($service) && $service;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Cliente --}}
    <div>
        <label class="block text-sm text-gray-300 mb-1">Cliente</label>
        <select name="client_id" class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-100 p-2">
            @foreach($clients ?? [] as $c)
                <option value="{{ $c->id }}" @selected(old('client_id', $service->client_id ?? null) == $c->id)>
                    {{ $c->id }} — {{ $c->nombre ?? $c->name }}
                </option>
            @endforeach
        </select>
        @error('client_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Plan --}}
    <div>
        <label class="block text-sm text-gray-300 mb-1">Plan</label>
        <select name="plan_id" class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-100 p-2">
            <option value="">— Sin plan —</option>
            @foreach($planes ?? $plans ?? [] as $p)
                <option value="{{ $p->id }}" @selected(old('plan_id', $service->plan_id ?? null) == $p->id)>
                    {{ $p->nombre ?? $p->name }} {{ isset($p->price) ? '— $'.number_format($p->price,2,',','.') : '' }}
                </option>
            @endforeach
        </select>
        @error('plan_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Precio --}}
    <div>
        <label class="block text-sm text-gray-300 mb-1">Precio</label>
        <input type="number" step="0.01" name="price"
               value="{{ old('price', $service->price ?? null) }}"
               class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-100 p-2"
               placeholder="0.00">
        @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- IP --}}
    <div>
        <label class="block text-sm text-gray-300 mb-1">IP</label>
        <input type="text" name="ip"
               value="{{ old('ip', $service->ip ?? null) }}"
               class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-100 p-2"
               placeholder="Ej: 192.168.1.10">
        @error('ip') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Router --}}
    <div>
        <label class="block text-sm text-gray-300 mb-1">Router</label>
        <select name="router_id" class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-100 p-2">
            <option value="">— Sin router —</option>
            @foreach($routers ?? [] as $r)
                <option value="{{ $r->id }}" @selected(old('router_id', $service->router_id ?? null) == $r->id)>
                    {{ $r->nombre ?? $r->name }} {{ $r->ip ? '— '.$r->ip : '' }}
                </option>
            @endforeach
        </select>
        @error('router_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Estado --}}
    <div>
        <label class="block text-sm text-gray-300 mb-1">Estado</label>
        @php
            $estados = ['activo' => 'Activo', 'suspendido' => 'Suspendido', 'baja' => 'Baja'];
        @endphp
        <select name="status" class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-100 p-2">
            @foreach($estados as $k => $label)
                <option value="{{ $k }}" @selected(old('status', $service->status ?? 'activo') === $k)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- GPS --}}
    <div class="md:col-span-2">
        <label class="block text-sm text-gray-300 mb-1">GPS (lat,lng)</label>
        <input type="text" name="gps"
               value="{{ old('gps', $service->gps ?? null) }}"
               class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-100 p-2"
               placeholder="-24.7851,-65.4106">
        @error('gps') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        <p class="text-gray-400 text-xs mt-1">Formato: <code>lat,lng</code> — un único campo como venimos usando.</p>
    </div>
</div>
