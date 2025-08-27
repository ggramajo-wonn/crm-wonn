@extends('layouts.app')
@section('content')
  <h1 class="text-2xl font-bold mb-4">Nuevo servicio — {{ $cliente->name }}</h1>
  <x-flash />
  <form method="POST" action="{{ route('servicios.store') }}">
    @include('servicios._form_client', ['service' => $service, 'cliente' => $cliente, 'plans' => $plans, 'routers' => $routers])
  </form>
@endsection
