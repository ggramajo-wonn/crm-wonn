@extends('layouts.app')
@section('content')
  <h1 class="text-2xl font-bold mb-4">Nuevo cliente</h1>
  <x-flash />
  <form method="POST" action="{{ route('clientes.store') }}">
    @include('clientes._form')
  </form>
@endsection