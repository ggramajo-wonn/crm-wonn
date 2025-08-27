@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl text-gray-100 font-semibold">Servicio #{{ $service->id }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('servicios.edit', $service) }}" class="px-3 py-1 rounded bg-blue-700 hover:bg-blue-600">Editar</a>
            <a href="{{ route('servicios.index') }}" class="px-3 py-1 rounded bg-gray-800 hover:bg-gray-700">Volver</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-emerald-900/30 text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-xl border border-gray-800 p-4">
            <h2 class="text-gray-300 mb-3">Datos</h2>
            <dl class="text-gray-200 text-sm">
                <div class="flex justify-between py-1">
                    <dt>Cliente</dt>
                    <dd>{{ $service->client->nombre ?? $service->client->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt>Plan</dt>
                    <dd>{{ $service->plan->nombre ?? $service->plan->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt>Precio</dt>
                    <dd>$ {{ number_format($service->price ?? 0, 2, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt>Estado</dt>
                    <dd>{{ ucfirst($service->status ?? '—') }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt>IP</dt>
                    <dd>{{ $service->ip ?? '—' }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt>Router</dt>
                    <dd>{{ $router->nombre ?? $router->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt>Instalación</dt>
                    <dd>{{ optional($service->installed_at ?? $service->started_at)->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt>GPS</dt>
                    <dd>{{ $service->gps ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
