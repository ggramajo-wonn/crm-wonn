@php
    use Illuminate\Support\Facades\Route;
@endphp

<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Brand -->
            <div class="flex items-center">
                <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}"
                   class="text-white font-bold tracking-wide">WONN</a>
            </div>

            <!-- Desktop links -->
            <div class="hidden sm:flex sm:items-center sm:space-x-6">
                @if (Route::has('dashboard'))
                    <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white text-sm">Panel</a>
                @endif
                @if (Route::has('clientes.index'))
                    <a href="{{ route('clientes.index') }}" class="text-gray-300 hover:text-white text-sm">Clientes</a>
                @endif
                @if (Route::has('servicios.index'))
                    <a href="{{ route('servicios.index') }}" class="text-gray-300 hover:text-white text-sm">Servicios</a>
                @endif

                @auth
                    <div class="relative group">
                        <button class="text-gray-300 hover:text-white text-sm">{{ auth()->user()?->name }}</button>
                        <div class="hidden group-hover:block absolute right-0 mt-2 w-40 rounded-xl bg-gray-900 border border-gray-800 shadow">
                            @if (Route::has('profile.edit'))
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800">Perfil</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm">Ingresar</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-gray-300 hover:text-white text-sm">Registrarse</a>
                    @endif
                @endauth
            </div>

            <!-- Mobile burger -->
            <div class="sm:hidden flex items-center">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" class="sm:hidden border-t border-gray-800 px-4 py-3 space-y-2">
        @if (Route::has('dashboard'))
            <a href="{{ route('dashboard') }}" class="block text-gray-300 hover:text-white text-sm">Panel</a>
        @endif
        @if (Route::has('clientes.index'))
            <a href="{{ route('clientes.index') }}" class="block text-gray-300 hover:text-white text-sm">Clientes</a>
        @endif
        @if (Route::has('servicios.index'))
            <a href="{{ route('servicios.index') }}" class="block text-gray-300 hover:text-white text-sm">Servicios</a>
        @endif

        @auth
            @if (Route::has('profile.edit'))
                <a href="{{ route('profile.edit') }}" class="block text-gray-300 hover:text-white text-sm">Perfil</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-gray-300 hover:text-white text-sm">Cerrar sesión</button>
            </form>
        @else
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="block text-gray-300 hover:text-white text-sm">Ingresar</a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="block text-gray-300 hover:text-white text-sm">Registrarse</a>
            @endif
        @endauth
    </div>
</nav>
