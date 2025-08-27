@extends('layouts.app')

@section('content')
@php
    $clientesTotal        = $clientesTotal        ?? 0;
    $serviciosTotal       = $serviciosTotal       ?? 0;
    $serviciosActivos     = $serviciosActivos     ?? 0;
    $serviciosSuspendidos = $serviciosSuspendidos ?? 0;
    $facturado            = $facturado            ?? 0;
    $pagado               = $pagado               ?? 0;
@endphp

<div class="container mx-auto px-4">
    <h1 class="text-2xl text-gray-100 font-semibold mb-6">
        Panel {{ $companyName ? "— $companyName" : "" }}
    </h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="rounded-2xl bg-gray-900 border border-gray-800 p-5">
            <div class="text-sm text-gray-400">Clientes</div>
            <div class="text-3xl font-semibold mt-2">{{ number_format($clientesTotal, 0, ',', '.') }}</div>
        </div>

        <div class="rounded-2xl bg-gray-900 border border-gray-800 p-5">
            <div class="text-sm text-gray-400">Servicios (Total)</div>
            <div class="text-3xl font-semibold mt-2">{{ number_format($serviciosTotal, 0, ',', '.') }}</div>
        </div>

        <div class="rounded-2xl bg-gray-900 border border-gray-800 p-5">
            <div class="text-sm text-gray-400">Servicios activos</div>
            <div class="text-3xl font-semibold mt-2">{{ number_format($serviciosActivos, 0, ',', '.') }}</div>
        </div>

        <div class="rounded-2xl bg-gray-900 border border-gray-800 p-5">
            <div class="text-sm text-gray-400">Servicios suspendidos</div>
            <div class="text-3xl font-semibold mt-2">{{ number_format($serviciosSuspendidos, 0, ',', '.') }}</div>
        </div>

        <div class="rounded-2xl bg-gray-900 border border-gray-800 p-5">
            <div class="text-sm text-gray-400">Facturado</div>
            <div class="text-3xl font-semibold mt-2">$ {{ number_format($facturado, 2, ',', '.') }}</div>
        </div>

        <div class="rounded-2xl bg-gray-900 border border-gray-800 p-5">
            <div class="text-sm text-gray-400">Pagado</div>
            <div class="text-3xl font-semibold mt-2">$ {{ number_format($pagado, 2, ',', '.') }}</div>
        </div>
    </div>
</div>
@endsection
