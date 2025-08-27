@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="text-center">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white">
            WONN — CRM ISP
        </h1>
        <p class="mt-3 text-gray-400 max-w-2xl mx-auto">
            Gestión unificada de clientes, servicios y pagos con diseño oscuro.
        </p>

        @auth
            @if (Route::has('dashboard'))
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center mt-6 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium transition">
                    Ir al Panel
                </a>
            @endif
        @else
            <div class="mt-6 space-x-3">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium transition">
                       Ingresar
                    </a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-100 border border-gray-700 transition">
                       Registrarse
                    </a>
                @endif
            </div>
        @endauth
    </div>

    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @if (Route::has('clientes.index'))
        <a href="{{ route('clientes.index') }}"
           class="group rounded-2xl border border-gray-800 bg-gray-900/60 hover:bg-gray-900 p-5 transition shadow-sm hover:shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Clientes</h3>
                <span class="text-gray-500 group-hover:text-gray-300 transition">→</span>
            </div>
            <p class="mt-2 text-sm text-gray-400">Altas, datos, ubicación y saldo.</p>
        </a>
        @endif

        @if (Route::has('servicios.index'))
        <a href="{{ route('servicios.index') }}"
           class="group rounded-2xl border border-gray-800 bg-gray-900/60 hover:bg-gray-900 p-5 transition shadow-sm hover:shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Servicios</h3>
                <span class="text-gray-500 group-hover:text-gray-300 transition">→</span>
            </div>
            <p class="mt-2 text-sm text-gray-400">Planes, IP, router, estado y gestión.</p>
        </a>
        @endif

        <a href="{{ url('/') }}"
           class="group rounded-2xl border border-gray-800 bg-gray-900/60 hover:bg-gray-900 p-5 transition shadow-sm hover:shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Sistema</h3>
                <span class="text-gray-500 group-hover:text-gray-300 transition">→</span>
            </div>
            <p class="mt-2 text-sm text-gray-400">Base Laravel + Vite + Breeze (Blade).</p>
        </a>
    </div>
</div>
@endsection
