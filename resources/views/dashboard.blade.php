@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold leading-tight text-white">
        Panel
    </h2>
@endsection

@section('content')
@php
    use Illuminate\Support\Facades\Route;
@endphp

<div class="space-y-8">

    <!-- KPI cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-5">
            <div class="text-sm text-gray-400">Clientes activos</div>
            <div class="mt-2 text-2xl font-bold text-white">—</div>
            <div class="mt-1 text-xs text-gray-500">Próximamente</div>
        </div>

        <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-5">
            <div class="text-sm text-gray-400">Servicios activos</div>
            <div class="mt-2 text-2xl font-bold text-white">—</div>
            <div class="mt-1 text-xs text-gray-500">Próximamente</div>
        </div>

        <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-5">
            <div class="text-sm text-gray-400">Saldo total</div>
            <div class="mt-2 text-2xl font-bold text-white">$ —</div>
            <div class="mt-1 text-xs text-gray-500">Incluye facturas y pagos</div>
        </div>

        <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-5">
            <div class="text-sm text-gray-400">Tickets abiertos</div>
            <div class="mt-2 text-2xl font-bold text-white">—</div>
            <div class="mt-1 text-xs text-gray-500">Módulo futuro</div>
        </div>
    </div>

    <!-- Quick links -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @if (Route::has('clientes.index'))
        <a href="{{ route('clientes.index') }}"
           class="group rounded-2xl border border-gray-800 bg-gray-900/60 hover:bg-gray-900 p-6 transition shadow-sm hover:shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Clientes</h3>
                <span class="text-gray-500 group-hover:text-gray-300 transition">→</span>
            </div>
            <p class="mt-2 text-sm text-gray-400">Altas, datos, ubicación y saldo. Rutas en español.</p>
        </a>
        @endif

        @if (Route::has('servicios.index'))
        <a href="{{ route('servicios.index') }}"
           class="group rounded-2xl border border-gray-800 bg-gray-900/60 hover:bg-gray-900 p-6 transition shadow-sm hover:shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Servicios</h3>
                <span class="text-gray-500 group-hover:text-gray-300 transition">→</span>
            </div>
            <p class="mt-2 text-sm text-gray-400">Planes, IP, router MikroTik, estado y gestión.</p>
        </a>
        @endif

        <a href="{{ url('/') }}"
           class="group rounded-2xl border border-gray-800 bg-gray-900/60 hover:bg-gray-900 p-6 transition shadow-sm hover:shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Inicio</h3>
                <span class="text-gray-500 group-hover:text-gray-300 transition">→</span>
            </div>
            <p class="mt-2 text-sm text-gray-400">Volver a la portada (WONN — CRM ISP).</p>
        </a>
    </div>

    <!-- Placeholder panel content -->
    <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-6">
        <h3 class="text-white font-semibold">Novedades</h3>
        <p class="mt-2 text-sm text-gray-400">
            Este panel se irá poblando con métricas reales (clientes, servicios, saldo y SIRO/MikroWISP).
        </p>
    </div>
</div>
@endsection
