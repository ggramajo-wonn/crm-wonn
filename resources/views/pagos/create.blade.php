@extends('layouts.app')
@section('content')
  <h1 class="text-2xl font-bold mb-4">Nuevo pago</h1>
  <x-flash />
  <form method="POST" action="{{ route('pagos.store') }}">
    @include('pagos._form')
  </form>
@endsection