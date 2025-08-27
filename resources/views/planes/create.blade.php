@extends('layouts.app')
@section('content')
  <h1 class="text-2xl font-bold mb-4">Nuevo plan</h1>
  <x-flash />
  <form method="POST" action="{{ route('planes.store') }}">
    @include('planes._form')
  </form>
@endsection
