@php
    // Usa Breeze (Blade). Este login reemplaza el por defecto con tema oscuro WONN.
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WONN • Iniciar sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-900 text-slate-100 antialiased">
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="flex flex-col items-center">
            <div class="h-14 w-14 rounded-2xl ring-1 ring-white/10 flex items-center justify-center shadow">
                <span class="font-bold">W</span>
            </div>
            <h1 class="mt-4 text-2xl font-semibold">Iniciar sesión</h1>
            <p class="mt-1 text-slate-400 text-sm">Accedé al panel del sistema</p>
        </div>

        @if ($errors->any())
            <div class="mt-6 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 p-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6">
            @csrf
            <div class="space-y-4 rounded-2xl p-6 ring-1 ring-white/10 bg-slate-900/60 shadow">
                <div>
                    <label for="email" class="block text-sm text-slate-300">Correo</label>
                    <input id="email" name="email" type="email" required autofocus
                           class="mt-1 w-full rounded-xl bg-slate-800/60 ring-1 ring-white/10 px-3 py-2 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-white/20"
                           value="{{ old('email') }}">
                </div>
                <div>
                    <label for="password" class="block text-sm text-slate-300">Contraseña</label>
                    <input id="password" name="password" type="password" required
                           class="mt-1 w-full rounded-xl bg-slate-800/60 ring-1 ring-white/10 px-3 py-2 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-white/20">
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-400">
                        <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-800">
                        Recuérdame
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-sky-400 hover:underline" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                <button type="submit"
                        class="w-full mt-2 rounded-xl px-4 py-2 ring-1 ring-white/10 hover:ring-white/20 transition">
                    Entrar
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <p class="mt-6 text-center text-sm text-slate-400">
                ¿No tenés cuenta?
                <a class="text-sky-400 hover:underline" href="{{ route('register') }}">Registrate</a>
            </p>
        @endif
    </div>
</div>
</body>
</html>
