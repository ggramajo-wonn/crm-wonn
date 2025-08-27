<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $company->name ?? config('app.name', 'WONN') }}</title>

    {{-- Tailwind por CDN para ir rápido --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: {
                50:'#ecfeff',100:'#cffafe',200:'#a5f3fc',300:'#67e8f9',400:'#22d3ee',
                500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63'
              }
            }
          }
        }
      }
    </script>
</head>
<body class="bg-gray-950 text-gray-200 min-h-screen">
  <header class="bg-[#0b111b] border-b border-gray-800">
  <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
    {{-- Marca (logo + nombre). Click -> Panel --}}
    <a href="{{ Route::has('panel') ? route('panel') : url('/') }}" class="flex items-center gap-3 group">
      @php $logo = $company?->logo_path ? asset('storage/'.$company->logo_path) : null; @endphp
      @if($logo)
        <img src="{{ $logo }}" alt="Logo" class="h-8 w-auto object-contain" />
      @else
        <div class="h-8 w-8 bg-primary-600 rounded-md"></div>
      @endif
      <span class="font-semibold text-gray-200">{{ $company->name ?? config('app.name', 'WONN internet') }}</span>
    </a>

    {{-- Botón para abrir sidebar en móviles/tablets --}}
    <button id="btn-open-sidebar" class="md:hidden inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-800 text-gray-300 hover:text-white">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4zM4 11h16v2H4zM4 16h16v2H4z"/></svg>
      Menú
    </button>

    {{-- NAV PRINCIPAL (solo desktop) --}}
    <nav class="hidden md:flex items-center gap-6 text-sm">

      {{-- PANEL (link directo) --}}
      <a href="{{ Route::has('panel') ? route('panel') : url('/') }}"
         class="text-gray-300 hover:text-white {{ request()->routeIs('panel') ? 'text-white font-medium' : '' }}">
        Panel
      </a>

      {{-- ========== CLIENTES (Dropdown desktop por click) ========== --}}
      <div class="relative" data-dd="wrapper">
        <button type="button" data-dd="trigger"
                class="inline-flex items-center gap-1 text-gray-300 hover:text-white {{ (request()->routeIs('clientes.*') || request()->routeIs('servicios.*')) ? 'text-white font-medium' : '' }}">
          Clientes
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
          </svg>
        </button>
        <div data-dd="menu"
             class="hidden absolute left-0 mt-2 min-w-[220px] rounded-lg border border-gray-800 bg-[#0b111b] shadow-xl z-50">
          @if (Route::has('clientes.index'))
            <a href="{{ route('clientes.index') }}"
               class="block px-4 py-2 text-gray-300 hover:bg-gray-800/80 hover:text-white {{ request()->routeIs('clientes.*') ? 'text-white font-medium' : '' }}">
              Clientes
            </a>
          @endif
          @if (Route::has('servicios.index'))
            <a href="{{ route('servicios.index') }}"
               class="block px-4 py-2 text-gray-300 hover:bg-gray-800/80 hover:text-white {{ request()->routeIs('servicios.*') ? 'text-white font-medium' : '' }}">
              Servicios
            </a>
          @endif
          <a href="{{ Route::has('clientes.map') ? route('clientes.map') : url('/clientes/mapa') }}" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Mapa de clientes</a>
          <a href="{{ Route::has('servicios.map') ? route('servicios.map') : url('/servicios/mapa') }}" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Mapa de servicios</a>
          <a href="{{ route('clientes.emails.index') }}" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Emails</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Mensajería</a>
          @if (Route::has('clientes.create'))
            <a href="{{ route('clientes.prospectos.index') }}" class="block px-4 py-2 text-gray-300 hover:bg-gray-800/80 hover:text-white">Prospectos</a>
          @else
            <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Prospectos</a>
          @endif
        </div>
      </div>

      {{-- ========== VENTAS (Dropdown) ========== --}}
      <div class="relative" data-dd="wrapper">
        <button type="button" data-dd="trigger"
                class="inline-flex items-center gap-1 text-gray-300 hover:text-white {{ request()->routeIs('facturas.*') ? 'text-white font-medium' : '' }}">
          Ventas
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
          </svg>
        </button>
        <div data-dd="menu" class="hidden absolute left-0 mt-2 min-w-[220px] rounded-lg border border-gray-800 bg-[#0b111b] shadow-xl z-50">
          @if (Route::has('facturas.index'))
            <a href="{{ route('facturas.index') }}" class="block px-4 py-2 text-gray-300 hover:bg-gray-800/80 hover:text-white {{ request()->routeIs('facturas.*') ? 'text-white font-medium' : '' }}">Facturas</a>
          @endif
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Facturador</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Saldos a facturar</a>
        </div>
      </div>

      {{-- ========== FINANZAS (Dropdown) ========== --}}
      <div class="relative" data-dd="wrapper">
        <button type="button" data-dd="trigger"
                class="inline-flex items-center gap-1 text-gray-300 hover:text-white {{ request()->routeIs('pagos.*') ? 'text-white font-medium' : '' }}">
          Finanzas
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
          </svg>
        </button>
        <div data-dd="menu" class="hidden absolute left-0 mt-2 min-w-[220px] rounded-lg border border-gray-800 bg-[#0b111b] shadow-xl z-50">
          @if (Route::has('pagos.create'))
            <a href="{{ route('pagos.create') }}" class="block px-4 py-2 text-gray-300 hover:bg-gray-800/80 hover:text-white">Registrar pago</a>
          @else
            <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Registrar pago</a>
          @endif
          @if (Route::has('pagos.index'))
            <a href="{{ route('pagos.index') }}" class="block px-4 py-2 text-gray-300 hover:bg-gray-800/80 hover:text-white {{ request()->routeIs('pagos.*') ? 'text-white font-medium' : '' }}">Pagos</a>
          @endif
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Cuenta corriente</a>
        </div>
      </div>

      {{-- ========== INVENTARIO (Dropdown) ========== --}}
      <div class="relative" data-dd="wrapper">
        <button type="button" data-dd="trigger" class="inline-flex items-center gap-1 text-gray-300 hover:text-white">
          Inventario
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
          </svg>
        </button>
        <div data-dd="menu" class="hidden absolute left-0 mt-2 min-w-[220px] rounded-lg border border-gray-800 bg-[#0b111b] shadow-xl z-50">
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Ingreso de producto</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Productos</a>
        </div>
      </div>

      {{-- ========== GESTIÓN DE RED (Dropdown) ========== --}}
      <div class="relative" data-dd="wrapper">
        <button type="button" data-dd="trigger"
                class="inline-flex items-center gap-1 text-gray-300 hover:text-white {{ request()->routeIs('planes.*') ? 'text-white font-medium' : '' }}">
          Gestión de red
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
          </svg>
        </button>
        <div data-dd="menu" class="hidden absolute left-0 mt-2 min-w-[240px] rounded-lg border border-gray-800 bg-[#0b111b] shadow-xl z-50">
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Routers</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Redes IPV4</a>
          @if (Route::has('planes.index'))
            <a href="{{ route('planes.index') }}" class="block px-4 py-2 text-gray-300 hover:bg-gray-800/80 hover:text-white {{ request()->routeIs('planes.*') ? 'text-white font-medium' : '' }}">Planes</a>
          @endif
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Cajas NAP</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Informes</a>
        </div>
      </div>

      {{-- ========== CONFIGURACIÓN (Dropdown) ========== --}}
      <div class="relative" data-dd="wrapper">
        <button type="button" data-dd="trigger"
                class="inline-flex items-center gap-1 text-gray-300 hover:text-white {{ request()->routeIs('empresa.*') ? 'text-white font-medium' : '' }}">
          Configuración
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
          </svg>
        </button>
        <div data-dd="menu" class="hidden absolute left-0 right-auto mt-2 min-w-[240px] rounded-lg border border-gray-800 bg-[#0b111b] shadow-xl z-50">
          @if (Route::has('empresa.edit'))
            <a href="{{ route('empresa.edit') }}" class="block px-4 py-2 text-gray-300 hover:bg-gray-800/80 hover:text-white {{ request()->routeIs('empresa.*') ? 'text-white font-medium' : '' }}">Empresa</a>
          @endif
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Gestión de usuarios</a>
          <a href="{{ route('config.email') }}" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Servidor Emails</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Facturación</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Facturas electrónica</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Pasarelas de pago</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Editor de plantillas</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Portal cliente</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Localidades</a>
          <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800/80 hover:text-white">Mensajería</a>
        </div>
      </div>

      {{-- ========== AUTH (desktop) ========== --}}
      @auth
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button class="text-gray-300 hover:text-white">Cerrar sesión</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white">Ingresar</a>
      @endauth
    </nav>
  </div>

  {{-- Controlador de dropdowns (desktop; solo click; uno abierto a la vez) --}}
  <script>
    (function () {
      const wrappers = Array.from(document.querySelectorAll('[data-dd="wrapper"]'));
      const closeAll = () => wrappers.forEach(w => {
        const m = w.querySelector('[data-dd="menu"]');
        if (m) m.classList.add('hidden');
      });

      wrappers.forEach(w => {
        const btn  = w.querySelector('[data-dd="trigger"]');
        const menu = w.querySelector('[data-dd="menu"]');
        if (!btn || !menu) return;

        btn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          const willOpen = menu.classList.contains('hidden');
          closeAll();
          if (willOpen) menu.classList.remove('hidden');
        });

        // Evitar cierre al hacer click dentro
        menu.addEventListener('click', (e) => e.stopPropagation());
      });

      document.addEventListener('click', closeAll);
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAll(); });
    })();
  </script>
</header>

{{-- ============ SIDEBAR MÓVIL (derecho) ============ --}}
<div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 hidden">
  <div class="absolute inset-0 bg-black/60"></div>
  <aside id="mobile-sidebar-panel" class="absolute right-0 top-0 h-full w-80 max-w-[85%] bg-[#0b111b] border-l border-gray-800 shadow-2xl transform translate-x-full transition-transform">
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800">
      <span class="font-semibold text-gray-200">Menú</span>
      <button id="btn-close-sidebar" class="p-2 rounded-lg border border-gray-800 text-gray-300 hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6.4 5l12.6 12.6-1.4 1.4L5 6.4 6.4 5zm12.6 1.4L6.4 19.1l-1.4-1.4L17.6 5l1.4 1.4z"/></svg>
      </button>
    </div>

    <nav class="px-2 py-2 overflow-y-auto h-[calc(100%-56px)]">
      {{-- PANEL --}}
      <a href="{{ Route::has('panel') ? route('panel') : url('/') }}"
         class="block px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800/70 hover:text-white">Panel</a>

      {{-- Secciones como acordeones --}}
      @php
        $sections = [
          'Clientes' => [
            ['label' => 'Clientes', 'route' => Route::has('clientes.index') ? route('clientes.index') : '#'],
            ['label' => 'Servicios', 'route' => Route::has('servicios.index') ? route('servicios.index') : '#'],
            ['label' => 'Mapa de clientes', 'route' => '#'],
            ['label' => 'Mapa de servicios', 'route' => '#'],
            ['label' => 'Emails', 'route' => '#'],
            ['label' => 'Mensajería', 'route' => '#'],
            ['label' => 'Alta cliente', 'route' => Route::has('clientes.create') ? route('clientes.prospectos.index') : '#'],
          ],
          'Tickets' => [
            ['label' => 'Nuevo ticket', 'route' => '#'],
            ['label' => 'Activos', 'route' => '#'],
            ['label' => 'Cerrados', 'route' => '#'],
          ],
          'Ventas' => [
            ['label' => 'Facturas', 'route' => Route::has('facturas.index') ? route('facturas.index') : '#'],
            ['label' => 'Facturador', 'route' => '#'],
            ['label' => 'Saldos a facturar', 'route' => '#'],
          ],
          'Finanzas' => [
            ['label' => 'Registrar pago', 'route' => Route::has('pagos.create') ? route('pagos.create') : '#'],
            ['label' => 'Pagos', 'route' => Route::has('pagos.index') ? route('pagos.index') : '#'],
            ['label' => 'Cuenta corriente', 'route' => '#'],
          ],
          'Inventario' => [
            ['label' => 'Ingreso de producto', 'route' => '#'],
            ['label' => 'Productos', 'route' => '#'],
          ],
          'Gestión de red' => [
            ['label' => 'Routers', 'route' => '#'],
            ['label' => 'Redes IPV4', 'route' => '#'],
            ['label' => 'Planes', 'route' => Route::has('planes.index') ? route('planes.index') : '#'],
            ['label' => 'Cajas NAP', 'route' => '#'],
            ['label' => 'Informes', 'route' => '#'],
          ],
          'Configuración' => [
            ['label' => 'Empresa', 'route' => Route::has('empresa.edit') ? route('empresa.edit') : '#'],
            ['label' => 'Gestión de usuarios', 'route' => '#'],
            ['label' => 'Servidor Emails', 'route' => '#'],
            ['label' => 'Facturación', 'route' => '#'],
            ['label' => 'Facturas electrónica', 'route' => '#'],
            ['label' => 'Pasarelas de pago', 'route' => '#'],
            ['label' => 'Editor de plantillas', 'route' => '#'],
            ['label' => 'Portal cliente', 'route' => '#'],
            ['label' => 'Localidades', 'route' => '#'],
            ['label' => 'Mensajería', 'route' => '#'],
          ],
        ];
      @endphp

      @foreach($sections as $title => $items)
        <div class="mt-2">
          <button class="w-full flex items-center justify-between px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800/70 hover:text-white"
                  data-acc="trigger">
            <span>{{ $title }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" /></svg>
          </button>
          <div class="ml-2 mt-1 hidden" data-acc="panel">
            @foreach($items as $it)
              <a href="{{ $it['route'] }}" class="block px-3 py-2 rounded-md text-gray-400 hover:bg-gray-800/60 hover:text-white">{{ $it['label'] }}</a>
            @endforeach
          </div>
        </div>
      @endforeach

      {{-- AUTH (mobile) --}}
      <div class="mt-4 border-t border-gray-800 pt-2">
        @auth
          <form method="POST" action="{{ route('logout') }}" class="px-2">
            @csrf
            <button class="w-full px-3 py-2 rounded-md text-left text-gray-300 hover:bg-gray-800/70 hover:text-white">Cerrar sesión</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800/70 hover:text-white">Ingresar</a>
        @endauth
      </div>
    </nav>
  </aside>
</div>

{{-- Script del sidebar móvil (abrir/cerrar + acordeones) --}}
<script>
  (function(){
    const openBtn = document.getElementById('btn-open-sidebar');
    const closeBtn = document.getElementById('btn-close-sidebar');
    const backdrop = document.getElementById('mobile-sidebar-backdrop');
    const panel = document.getElementById('mobile-sidebar-panel');

    const open = () => { backdrop.classList.remove('hidden'); panel.classList.remove('translate-x-full'); };
    const close = () => { panel.classList.add('translate-x-full'); setTimeout(() => backdrop.classList.add('hidden'), 200); };

    if (openBtn) openBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (backdrop) backdrop.addEventListener('click', (e) => { if (e.target === backdrop) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

    // Acordeones
    document.querySelectorAll('[data-acc="trigger"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const panel = btn.parentElement.querySelector('[data-acc="panel"]');
        const icon = btn.querySelector('svg');
        const willOpen = panel.classList.contains('hidden');
        // Cerrar otros
        document.querySelectorAll('[data-acc="panel"]').forEach(p => { if (p !== panel) p.classList.add('hidden'); });
        document.querySelectorAll('[data-acc="trigger"] svg').forEach(i => i.classList.remove('rotate-180'));
        // Toggle actual
        if (willOpen) { panel.classList.remove('hidden'); icon.classList.add('rotate-180'); }
        else { panel.classList.add('hidden'); icon.classList.remove('rotate-180'); }
      });
    });
  })();
</script>


  <main class="max-w-7xl mx-auto px-4 py-8">
    <x-flash />
    @yield('content')
  </main>
</body>
</html>
