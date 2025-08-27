@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl text-gray-100 font-semibold">Servicios</h1>
        <a href="{{ route('servicios.create') }}" class="btn btn-primary">Nuevo servicio</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-emerald-900/30 text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-gray-800">
        <table class="min-w-full text-sm text-gray-200">
            <thead class="bg-gray-900/60 text-gray-400">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Cliente</th>
                    <th class="p-3 text-left">Plan</th>
                    <th class="p-3 text-left">Precio</th>
                    <th class="p-3 text-left">Estado</th>
                    <th class="p-3 text-left">IP</th>
                    <th class="p-3 text-left">Router</th>
                    <th class="p-3 text-left">Instalación</th>
                    <th class="p-3 text-left w-40">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = $items ?? ($services ?? ($servicios ?? collect()));
                @endphp

                @forelse ($items as $s)
                    <tr class="border-t border-gray-800">
                        <td class="p-3">{{ $s->id }}</td>
                        <td class="p-3">{{ $s->client->nombre ?? $s->client->name ?? '—' }}</td>
                        <td class="p-3">{{ $s->plan->nombre ?? $s->plan->name ?? '—' }}</td>
                        <td class="p-3">$ {{ number_format($s->price ?? 0, 2, ',', '.') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs {{ ($s->status ?? '')==='activo' ? 'bg-emerald-900/30 text-emerald-300' : (($s->status ?? '')==='suspendido' ? 'bg-amber-900/30 text-amber-300' : 'bg-gray-800 text-gray-300') }}">
                                {{ ucfirst($s->status ?? '—') }}
                            </span>
                        </td>
                        <td class="p-3">{{ $s->ip ?: '—' }}</td>
                        <td class="p-3">
                            @php $r = $routersMap[$s->router_id] ?? null; @endphp
                            {{ $r->nombre ?? $r->name ?? '—' }}
                        </td>
                        <td class="p-3">{{ optional($s->installed_at ?? $s->started_at)->format('d/m/Y') }}</td>
                        <td class="p-3">
                            <div class="flex gap-2">
                                <a href="{{ route('servicios.show', $s) }}" class="px-3 py-1 rounded bg-gray-800 hover:bg-gray-700">Ver</a>
                                <a href="{{ route('servicios.edit', $s) }}" class="px-3 py-1 rounded bg-blue-700 hover:bg-blue-600">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-6 text-center text-gray-400">No hay servicios.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($items,'links'))
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
