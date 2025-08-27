@extends('layouts.app')
@section('content')
  <h1 class="text-2xl font-bold mb-4">Editar factura</h1>
  <x-flash />
  <form method="POST" action="{{ route('facturas.update', $invoice) }}">
    @method('PUT')
    @include('facturas._form')
  </form>
@endsection