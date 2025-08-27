@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Perfil</h1>

    @if (session('status') === 'profile-updated')
        <div class="mb-4 rounded-lg bg-green-500/10 border border-green-500/30 text-green-300 p-3 text-sm">
            Perfil actualizado.
        </div>
    @endif

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')
        <div>
            <label class="block text-sm text-slate-300">Nombre</label>
            <input name="name" type="text" value="{{ old('name', $user->name) }}"
                   class="mt-1 w-full rounded-xl bg-slate-800/60 ring-1 ring-white/10 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-slate-300">Email</label>
            <input name="email" type="email" value="{{ old('email', $user->email) }}"
                   class="mt-1 w-full rounded-xl bg-slate-800/60 ring-1 ring-white/10 px-3 py-2">
        </div>
        <button class="px-4 py-2 rounded-xl ring-1 ring-white/10 hover:ring-white/20">Guardar</button>
    </form>

    <hr class="my-8 border-white/10">

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-3">
        @csrf
        @method('delete')
        <p class="text-sm text-slate-400">Eliminar cuenta</p>
        <input type="password" name="password" placeholder="Confirmá tu contraseña"
               class="w-full rounded-xl bg-slate-800/60 ring-1 ring-white/10 px-3 py-2">
        <button class="px-4 py-2 rounded-xl ring-1 ring-red-500/40 text-red-300 hover:ring-red-500/60">
            Eliminar
        </button>
    </form>
</div>
@endsection
