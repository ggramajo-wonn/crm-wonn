@extends('layouts.app')
@section('content')
  <h1 class="text-2xl font-bold mb-4">Nueva factura</h1>
  <x-flash />
  <form method="POST" action="{{ route('facturas.store') }}">
    @include('facturas._form')
  </form>
@endsection