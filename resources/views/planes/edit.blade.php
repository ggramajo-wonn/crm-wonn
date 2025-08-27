@extends('layouts.app')
@section('content')
  <h1 class="text-2xl font-bold mb-4">Gestionar plan</h1>
  <x-flash />
  <form method="POST" action="{{ route('planes.update', $plan) }}">
    @method('PUT')
    @include('planes._form')
  </form>
@endsection
