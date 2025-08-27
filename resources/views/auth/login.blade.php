@extends('layouts.auth')

@section('content')
  <div class="rounded-2xl bg-gray-900 border border-gray-800 p-6 shadow">
    <h1 class="text-2xl font-bold text-center mb-6">Acceso al sistema</h1>
    <x-flash />
    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
      @csrf
      <label class="block">
        <span class="text-sm text-gray-400">Email</span>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-600">
      </label>

      <label class="block">
        <span class="text-sm text-gray-400">Contraseña</span>
        <input type="password" name="password" required
               class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-600">
      </label>

      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="remember" class="rounded bg-gray-900 border-gray-700">
        <span class="text-sm text-gray-400">Recordarme</span>
      </label>

      <button class="w-full rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2 font-medium">
        Ingresar
      </button>
    </form>
  </div>
@endsection
