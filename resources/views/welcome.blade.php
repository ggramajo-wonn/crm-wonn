<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WONN • Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 text-slate-100 antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-2xl w-full text-center">
            <div class="mx-auto inline-flex items-center justify-center h-16 w-16 rounded-2xl ring-1 ring-white/10 shadow">
                <!-- Placeholder logo box; replace with your logo if desired -->
                <span class="font-bold">W</span>
            </div>
            <h1 class="mt-6 text-3xl font-semibold tracking-tight">Bienvenido a WONN</h1>
            <p class="mt-2 text-slate-400">Panel del sistema</p>

            <div class="mt-8 flex items-center justify-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-4 py-2 rounded-xl ring-1 ring-white/10 hover:ring-white/20 transition">
                        Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 rounded-xl ring-1 ring-white/10 hover:ring-white/20 transition">
                        Iniciar sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 rounded-xl ring-1 ring-white/10 hover:ring-white/20 transition">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
