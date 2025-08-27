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
    ])
</form>

<div class="mt-4">
  <a href="{{ $returnTo }}" class="btn btn-secondary">Cancelar</a>
</div>

{{-- Quitar botón "Cancelar" del parcial para evitar duplicado --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="servicios"]');
    if (!form) return;
    const candidates = form.querySelectorAll('button, a');
    candidates.forEach(el => {
      const txt = (el.textContent || '').trim().toLowerCase();
      if (txt === 'cancelar') {
        el.remove();
      }
    });
  });
</script>
@endsection
