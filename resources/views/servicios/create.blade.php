@extends('layouts.app')
@section('content')
  <h1 class="text-2xl font-bold mb-4">Nuevo servicio</h1>
  <x-flash />
  @php
    $clientId = old('client_id')
      ?? request('client_id')
      ?? ($cliente->id ?? ($service->client_id ?? ($service->client->id ?? null)));
    $returnTo = $clientId ? route('clientes.show', $clientId) : (url()->previous() ?? route('clientes.index'));
  @endphp
  <form method="POST" action="{{ route('servicios.store') }}">
    @csrf
    <input type="hidden" name="client_id" value="{{ $clientId }}">
    @include('servicios._form', ['returnTo' => $returnTo])
  @include('servicios._ftth_fields')
  </form>
@endsection
