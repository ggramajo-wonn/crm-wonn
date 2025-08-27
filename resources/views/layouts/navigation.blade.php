<nav class="bg-slate-900 border-b border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                    <div class="h-8 w-8 rounded-xl ring-1 ring-white/10 flex items-center justify-center">
                        <span class="font-bold">W</span>
                    </div>
                    <span class="text-slate-100 font-semibold hidden sm:block">WONN</span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <div class="relative" x-data="{ open:false }" @keydown.escape.window="open=false">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl ring-1 ring-white/10 hover:ring-white/20 transition text-sm"
                            @click="open = !open"
                            aria-haspopup="menu"
                            :aria-expanded="open.toString()"
                        >
                            <span class="hidden sm:block">{{ Auth::user()->name ?? 'Usuario' }}</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition.origin.top.right
                            @click.outside="open=false"
                            class="absolute right-0 mt-2 w-48 rounded-xl bg-slate-900 ring-1 ring-white/10 shadow-lg z-50"
                            style="display:none"
                            role="menu"
                        >
                            @if (Route::has('profile.edit'))
                                <a href="{{ route('profile.edit') }}"
                                   class="block px-4 py-2 text-sm text-slate-200 hover:bg-white/5"
                                   role="menuitem">Perfil</a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="@if(Route::has('profile.edit')) border-t border-white/10 @endif">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left block px-4 py-2 text-sm text-slate-200 hover:bg-white/5"
                                        role="menuitem">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
