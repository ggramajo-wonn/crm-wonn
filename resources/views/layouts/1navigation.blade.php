<nav x-data="{ mobile:false, dd:{} }" class="bg-slate-900 border-b border-white/10">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 justify-between items-center">
      <!-- Brand + Mobile Menu -->
      <div class="flex items-center gap-2">
        <button class="md:hidden inline-flex items-center gap-2 rounded-lg border border-white/10 px-3 py-2 text-slate-200 hover:bg-white/5"
                @click="mobile = true">
          <!-- icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M3 5h14M3 10h14M3 15h14"/>
          </svg>
          Menú
        </button>

        <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
          <div class="h-8 w-8 rounded-xl ring-1 ring-white/10 flex items-center justify-center">
            <span class="font-bold">W</span>
          </div>
          <span class="text-slate-100 font-semibold hidden sm:block">WONN</span>
        </a>
      </div>

      <!-- Desktop main nav -->
      <div class="hidden md:flex items-center gap-6 text-sm">
        <a href="{{ Route::has('panel') ? route('panel') : url('/') }}"
           class="text-slate-300 hover:text-white {{ request()->routeIs('panel') ? 'text-white font-medium' : '' }}">
          Panel
        </a>

        <!-- CLIENTES -->
        <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
          <button type="button" class="inline-flex items-center gap-1 text-slate-300 hover:text-white {{ (request()->routeIs('clientes.*') || request()->routeIs('servicios.*')) ? 'text-white font-medium' : '' }}"
                  @click="open = !open">
            Clientes
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
          </button>
          <div x-show="open" x-transition.origin.top.left @click.outside="open=false"
               class="absolute left-0 mt-2 min-w-[220px] rounded-lg border border-white/10 bg-slate-900 shadow-xl z-50 p-1"
               style="display:none">
            @if (Route::has('clientes.index'))
              <a href="{{ route('clientes.index') }}" class="block rounded-md px-3 py-2 text-slate-300 hover:bg-white/5 {{ request()->routeIs('clientes.*') ? 'text-white font-medium' : '' }}">Clientes</a>
            @endif
            @if (Route::has('servicios.index'))
              <a href="{{ route('servicios.index') }}" class="block rounded-md px-3 py-2 text-slate-300 hover:bg-white/5 {{ request()->routeIs('servicios.*') ? 'text-white font-medium' : '' }}">Servicios</a>
            @endif
            <a href="{{ Route::has('clientes.map') ? route('clientes.map') : '#' }}" class="block rounded-md px-3 py-2 text-slate-400 hover:bg-white/5">Mapa de clientes</a>
            <a href="{{ Route::has('servicios.map') ? route('servicios.map') : '#' }}" class="block rounded-md px-3 py-2 text-slate-400 hover:bg-white/5">Mapa de servicios</a>
            <a href="{{ route('clientes.emails.index') }}" class="block rounded-md px-3 py-2 text-slate-400 hover:bg-white/5">Emails</a>
            @if (Route::has('clientes.create'))
              <a href="{{ route('clientes.prospectos.index') }}" class="block rounded-md px-3 py-2 text-slate-300 hover:bg-white/5">Prospectos</a>
            @else
              <span class="block rounded-md px-3 py-2 text-slate-500">Prospectos</span>
            @endif
          </div>
        </div>

        <!-- VENTAS -->
        <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
          <button type="button" class="inline-flex items-center gap-1 text-slate-300 hover:text-white {{ request()->routeIs('facturas.*') ? 'text-white font-medium' : '' }}"
                  @click="open = !open">
            Ventas
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
          </button>
          <div x-show="open" x-transition.origin.top.left @click.outside="open=false"
               class="absolute left-0 mt-2 min-w-[220px] rounded-lg border border-white/10 bg-slate-900 shadow-xl z-50 p-1"
               style="display:none">
            @if (Route::has('facturas.index'))
              <a href="{{ route('facturas.index') }}" class="block rounded-md px-3 py-2 text-slate-300 hover:bg-white/5 {{ request()->routeIs('facturas.*') ? 'text-white font-medium' : '' }}">Facturas</a>
            @endif
            <span class="block rounded-md px-3 py-2 text-slate-500">Facturador</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Saldos a facturar</span>
          </div>
        </div>

        <!-- FINANZAS -->
        <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
          <button type="button" class="inline-flex items-center gap-1 text-slate-300 hover:text-white {{ request()->routeIs('pagos.*') ? 'text-white font-medium' : '' }}"
                  @click="open = !open">
            Finanzas
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
          </button>
          <div x-show="open" x-transition.origin.top.left @click.outside="open=false"
               class="absolute left-0 mt-2 min-w-[220px] rounded-lg border border-white/10 bg-slate-900 shadow-xl z-50 p-1"
               style="display:none">
            @if (Route::has('pagos.index'))
              <a href="{{ route('pagos.index') }}" class="block rounded-md px-3 py-2 text-slate-300 hover:bg-white/5 {{ request()->routeIs('pagos.*') ? 'text-white font-medium' : '' }}">Pagos</a>
            @endif
            <span class="block rounded-md px-3 py-2 text-slate-500">Cuenta corriente</span>
          </div>
        </div>

        <!-- INVENTARIO -->
        <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
          <button type="button" class="inline-flex items-center gap-1 text-slate-300 hover:text-white" @click="open = !open">
            Inventario
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
          </button>
          <div x-show="open" x-transition.origin.top.left @click.outside="open=false"
               class="absolute left-0 mt-2 min-w-[220px] rounded-lg border border-white/10 bg-slate-900 shadow-xl z-50 p-1"
               style="display:none">
            <span class="block rounded-md px-3 py-2 text-slate-500">Ingreso de producto</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Productos</span>
          </div>
        </div>

        <!-- GESTIÓN DE RED -->
        <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
          <button type="button" class="inline-flex items-center gap-1 text-slate-300 hover:text-white {{ request()->routeIs('planes.*') ? 'text-white font-medium' : '' }}"
                  @click="open = !open">
            Gestión de red
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
          </button>
          <div x-show="open" x-transition.origin.top.left @click.outside="open=false"
               class="absolute left-0 mt-2 min-w-[220px] rounded-lg border border-white/10 bg-slate-900 shadow-xl z-50 p-1"
               style="display:none">
            @if (Route::has('planes.index'))
              <a href="{{ route('planes.index') }}" class="block rounded-md px-3 py-2 text-slate-300 hover:bg-white/5 {{ request()->routeIs('planes.*') ? 'text-white font-medium' : '' }}">Planes</a>
            @endif
            <span class="block rounded-md px-3 py-2 text-slate-500">Routers</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Tickets</span>
          </div>
        </div>

        <!-- CONFIG -->
        <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
          <button type="button" class="inline-flex items-center gap-1 text-slate-300 hover:text-white {{ request()->routeIs('empresa.*') ? 'text-white font-medium' : '' }}"
                  @click="open = !open">
            Configuración
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
          </button>
          <div x-show="open" x-transition.origin.top.left @click.outside="open=false"
               class="absolute left-0 mt-2 min-w-[260px] rounded-lg border border-white/10 bg-slate-900 shadow-xl z-50 p-1"
               style="display:none">
            @if (Route::has('empresa.edit'))
              <a href="{{ route('empresa.edit') }}" class="block rounded-md px-3 py-2 text-slate-300 hover:bg-white/5 {{ request()->routeIs('empresa.*') ? 'text-white font-medium' : '' }}">Empresa</a>
            @endif
            <span class="block rounded-md px-3 py-2 text-slate-500">Gestión de usuarios</span>
            <a href="{{ Route::has('config.email') ? route('config.email') : '#' }}" class="block rounded-md px-3 py-2 text-slate-400 hover:bg-white/5">Servidor Emails</a>
            <span class="block rounded-md px-3 py-2 text-slate-500">Facturación</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Factura electrónica</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Pasarelas de pago</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Editor de plantillas</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Portal cliente</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Localidades</span>
            <span class="block rounded-md px-3 py-2 text-slate-500">Mensajería</span>
          </div>
        </div>
      </div>

      <!-- Admin dropdown -->
      <div class="flex items-center gap-4">
        @auth
          <div class="relative" x-data="{ open:false }" @keydown.escape.window="open=false">
            <button type="button"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl ring-1 ring-white/10 hover:ring-white/20 transition text-sm"
                    @click="open = !open"
                    :aria-expanded="open.toString()">
              <span class="hidden sm:block">{{ Auth::user()->name ?? 'Usuario' }}</span>
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div x-show="open" x-transition.origin.top.right @click.outside="open=false"
                 class="absolute right-0 mt-2 w-48 rounded-xl bg-slate-900 ring-1 ring-white/10 shadow-lg z-50 p-1"
                 style="display:none">
              @if (Route::has('profile.edit'))
                <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-sm text-slate-200 hover:bg-white/5">Perfil</a>
              @endif
              <form method="POST" action="{{ route('logout') }}" class="@if(Route::has('profile.edit')) border-t border-white/10 mt-1 pt-1 @endif">
                @csrf
                <button type="submit" class="w-full text-left block rounded-md px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                  Cerrar sesión
                </button>
              </form>
            </div>
          </div>
        @endauth
      </div>
    </div>
  </div>

  <!-- Mobile drawer -->
  <div x-show="mobile" x-transition.opacity class="fixed inset-0 bg-black/50 z-40" style="display:none" @click="mobile=false"></div>
  <div class="fixed inset-y-0 left-0 z-50 w-72 transform bg-slate-900 ring-1 ring-white/10 shadow-xl p-4"
       x-show="mobile" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full opacity-0"
       x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-full opacity-0" style="display:none">
    <div class="flex items-center justify-between mb-2">
      <span class="font-semibold">Menú</span>
      <button class="rounded-lg border border-white/10 px-2 py-1" @click="mobile=false">Cerrar</button>
    </div>

    <nav class="space-y-2 overflow-y-auto h-[calc(100%-40px)] pr-1">
      <a href="{{ Route::has('panel') ? route('panel') : url('/') }}" class="block px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white">Panel</a>

      <!-- Accordion-like sections -->
      @php
        $sections = [
          'Clientes' => [
            ['label' => 'Clientes', 'route' => Route::has('clientes.index') ? route('clientes.index') : '#'],
            ['label' => 'Servicios', 'route' => Route::has('servicios.index') ? route('servicios.index') : '#'],
            ['label' => 'Mapa de clientes', 'route' => '#'],
            ['label' => 'Mapa de servicios', 'route' => '#'],
            ['label' => 'Emails', 'route' => route('clientes.emails.index')],
            ['label' => 'Prospectos', 'route' => Route::has('clientes.create') ? route('clientes.prospectos.index') : '#'],
          ],
          'Ventas' => [
            ['label' => 'Facturas', 'route' => Route::has('facturas.index') ? route('facturas.index') : '#'],
            ['label' => 'Facturador', 'route' => '#'],
            ['label' => 'Saldos a facturar', 'route' => '#'],
          ],
          'Finanzas' => [
            ['label' => 'Pagos', 'route' => Route::has('pagos.index') ? route('pagos.index') : '#'],
            ['label' => 'Cuenta corriente', 'route' => '#'],
          ],
          'Inventario' => [
            ['label' => 'Ingreso de producto', 'route' => '#'],
            ['label' => 'Productos', 'route' => '#'],
          ],
          'Gestión de red' => [
            ['label' => 'Planes', 'route' => Route::has('planes.index') ? route('planes.index') : '#'],
            ['label' => 'Routers', 'route' => '#'],
            ['label' => 'Tickets', 'route' => '#'],
          ],
          'Configuración' => [
            ['label' => 'Empresa', 'route' => Route::has('empresa.edit') ? route('empresa.edit') : '#'],
            ['label' => 'Gestión de usuarios', 'route' => '#'],
            ['label' => 'Servidor Emails', 'route' => Route::has('config.email') ? route('config.email') : '#'],
            ['label' => 'Facturación', 'route' => '#'],
            ['label' => 'Factura electrónica', 'route' => '#'],
            ['label' => 'Pasarelas de pago', 'route' => '#'],
            ['label' => 'Editor de plantillas', 'route' => '#'],
            ['label' => 'Portal cliente', 'route' => '#'],
            ['label' => 'Localidades', 'route' => '#'],
            ['label' => 'Mensajería', 'route' => '#'],
          ],
        ];
      @endphp

      @foreach ($sections as $title => $items)
        <div x-data="{open:false}" class="border border-white/10 rounded-lg">
          <button class="w-full px-3 py-2 flex items-center justify-between text-left text-slate-200 hover:bg-white/5"
                  @click="open=!open">
            <span>{{ $title }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div x-show="open" x-transition.scale.origin.top class="px-1 pb-2 space-y-1" style="display:none">
            @foreach ($items as $it)
              <a href="{{ $it['route'] }}" class="block px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white">{{ $it['label'] }}</a>
            @endforeach
          </div>
        </div>
      @endforeach

      <!-- Auth (mobile) -->
      <div class="mt-2 border-t border-white/10 pt-2">
        @auth
          <form method="POST" action="{{ route('logout') }}" class="px-1">
            @csrf
            <button class="w-full px-3 py-2 rounded-md text-left text-slate-300 hover:bg-white/5 hover:text-white">Cerrar sesión</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-slate-300 hover:bg-white/5 hover:text-white">Ingresar</a>
        @endauth
      </div>
    </nav>
  </div>
</nav>
