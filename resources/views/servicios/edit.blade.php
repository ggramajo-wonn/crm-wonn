@extends('layouts.app')

@section('content')
@php
    $clientName = $cliente->name ?? ($service->client->name ?? 'Cliente');
    $clientId = $cliente->id ?? ($service->client_id ?? ($service->client->id ?? null));
    $returnTo = $clientId ? route('clientes.show', $clientId) : (url()->previous() ?? route('clientes.index'));
@endphp

<h1 class="text-2xl font-bold mb-4">Editar servicio — {{ $clientName }}</h1>

<x-flash />

<form method="POST" action="{{ route('servicios.update', $service) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="client_id" value="{{ $clientId }}">
    @include('servicios._form_client', [
        'service' => $service,
        'cliente' => $cliente,
        'clients' => $clients ?? [],
        'planes'  => $planes ?? [],
        'plans'   => $plans  ?? ($planes ?? []),
        'returnTo'=> $returnTo,
    ])
</form>
@endsection
