@php
    $current = request()->route('modulo');

    $icons = [
        'home'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>',
        'users'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 10-8 0M12 7a3 3 0 100 6 3 3 0 000-6zM3 20c0-2.5 2-4 5-4M21 20c0-2.5-2-4-5-4"/>',
        'calendar'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 6h14a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/>',
        'clipboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 4h6v2H9zM7 4H6a1 1 0 00-1 1v15a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-1-1h-1"/>',
        'video'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.5-2.5v9L15 14M4 6h9a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"/>',
        'shield'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/>',
        'scissors'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 9a3 3 0 100-6 3 3 0 000 6zM6 21a3 3 0 100-6 3 3 0 000 6zM8.5 8.5L20 20M8.5 15.5L20 4M12 12l3 3"/>',
        'box'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 00-1-1.7l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.7l7 4a2 2 0 002 0l7-4A2 2 0 0021 16zM3.3 7L12 12l8.7-5M12 22V12"/>',
        'document'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5M9 13h6M9 17h6"/>',
        'wallet'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h12a2 2 0 012 2v2H5a2 2 0 00-2 2zM3 11h16a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2zM16 14h.01"/>',
        'chart'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 20V10M12 20V4M19 20v-6M3 20h18"/>',
        'cog'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9a3 3 0 100 6 3 3 0 000-6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 12a7 7 0 00-.1-1.2l2-1.6-2-3.4-2.4 1a7 7 0 00-2-1.2l-.4-2.6h-4l-.4 2.6a7 7 0 00-2 1.2l-2.4-1-2 3.4 2 1.6A7 7 0 005 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.4-1c.6.5 1.3.9 2 1.2l.4 2.6h4l.4-2.6c.7-.3 1.4-.7 2-1.2l2.4 1 2-3.4-2-1.6c.1-.4.1-.8.1-1.2z"/>',
        'sliders'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h10M18 6h2M4 12h2M10 12h10M4 18h7M15 18h5M14 4v4M6 10v4M11 16v4"/>',
        'receipt'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 3l1.5 1.5L9 3l1.5 1.5L12 3l1.5 1.5L15 3l1.5 1.5L18 3v18l-1.5-1.5L15 21l-1.5-1.5L12 21l-1.5-1.5L9 21l-1.5-1.5L6 21zM9 8h6M9 12h6M9 16h3"/>',
        'logout'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5M21 12H9M12 19H6a1 1 0 01-1-1V6a1 1 0 011-1h6"/>',
        'search'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/>',
        'paw'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.5 11a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6zM10 8a1.8 1.8 0 100-3.6A1.8 1.8 0 0010 8zM14 8a1.8 1.8 0 100-3.6A1.8 1.8 0 0014 8zM18.5 11a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6zM12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/>',
    ];

    $grupos = [
        ['titulo' => null, 'items' => [
            ['label' => 'Dashboard', 'icon' => 'home', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ]],
        ['titulo' => 'GESTION', 'items' => [
            ['label' => 'Clientes',  'icon' => 'users',    'url' => route('clientes.index'), 'active' => request()->routeIs('clientes.*')],
            ['label' => 'Pacientes', 'icon' => 'paw',      'url' => route('mascotas.index'), 'active' => request()->routeIs('mascotas.*')],
            ['label' => 'Agenda',    'icon' => 'calendar', 'url' => route('citas.index'),    'active' => request()->routeIs('citas.*')],
        ]],
        ['titulo' => 'CLINICA', 'items' => [
            ['label' => 'Historia Clinica', 'icon' => 'clipboard', 'url' => route('historia.index'),     'active' => request()->routeIs('historia.*')],
            ['label' => 'Telemedicina',     'icon' => 'video',     'url' => route('telemedicina.index'), 'active' => request()->routeIs('telemedicina.*')],
            ['label' => 'Vacunaciones',     'icon' => 'shield',    'url' => route('vacunas.index'),      'active' => request()->routeIs('vacunas.*')],
            ['label' => 'Peluqueria',       'icon' => 'scissors',  'url' => route('peluqueria.index'),   'active' => request()->routeIs('peluqueria.*')],
        ]],
        ['titulo' => 'COMERCIAL', 'items' => [
            ['label' => 'Inventario',      'icon' => 'box',      'url' => route('productos.index'),    'active' => request()->routeIs('productos.*')],
            ['label' => 'Facturacion',     'icon' => 'document', 'url' => route('comprobantes.index'), 'active' => request()->routeIs('comprobantes.*')],
            ['label' => 'Control de Caja', 'icon' => 'wallet',   'url' => route('caja.index'),         'active' => request()->routeIs('caja.*')],
        ]],
        ['titulo' => 'GERENCIA', 'items' => [
            ['label' => 'Reportes y BI', 'icon' => 'chart', 'url' => route('reportes.index'), 'active' => request()->routeIs('reportes.*')],
        ]],
        ['titulo' => 'SISTEMA', 'items' => [
            ['label' => 'Usuarios',      'icon' => 'cog',     'url' => route('usuarios.index'),     'active' => request()->routeIs('usuarios.*'), 'admin' => true],
            ['label' => 'Facturacion Electronica', 'icon' => 'receipt', 'url' => route('facturacion.config.edit'), 'active' => request()->routeIs('facturacion.config.*'), 'admin' => true],
            ['label' => 'Configuracion', 'icon' => 'sliders', 'url' => route('configuracion.edit'), 'active' => request()->routeIs('configuracion.*'), 'admin' => true],
            ['label' => 'Mi Suscripcion', 'icon' => 'wallet', 'url' => route('suscripcion.index'), 'active' => request()->routeIs('suscripcion.index'), 'admin' => true],
            ['label' => 'Auditoria', 'icon' => 'clipboard', 'url' => route('auditoria.index'), 'active' => request()->routeIs('auditoria.*'), 'admin' => true],
        ]],
    ];

    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') — VetSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
            colors: { brand: { 50:'#ecfeff',100:'#cffafe',200:'#a5f3fc',300:'#67e8f9',400:'#22d3ee',500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63' } },
        }}};
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.18); }
        [x-cloak] { display: none !important; }
        @media print {
            aside, header, .no-print { display: none !important; }
            .md\:ml-64 { margin-left: 0 !important; }
            body { background: #fff !important; }
            main { padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
<div x-data="{ open: false }">

    <!-- Overlay (solo movil) -->
    <div x-show="open" x-cloak x-transition.opacity @click="open = false" class="fixed inset-0 bg-slate-900/50 z-30 md:hidden"></div>

    <!-- ===================== SIDEBAR ===================== -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-gradient-to-b from-brand-800 to-brand-900 text-white transform transition-transform duration-300 -translate-x-full md:translate-x-0"
           :class="open ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <div class="h-20 flex items-center gap-3 px-6 border-b border-white/10 shrink-0">
            <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['paw'] !!}</svg>
            </div>
            <div class="leading-tight">
                <p class="text-lg font-extrabold tracking-tight">VetSystem</p>
                <p class="text-[11px] text-brand-200 font-medium">Panel Clinico</p>
            </div>
            <button @click="open = false" class="ml-auto md:hidden text-brand-200 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-1">
            @foreach ($grupos as $grupo)
                @if ($grupo['titulo'])
                    <p class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-widest text-brand-300/80">{{ $grupo['titulo'] }}</p>
                @endif
                @foreach ($grupo['items'] as $item)
                    @if (empty($item['admin']) || $user->esAdmin())
                    <a href="{{ $item['url'] }}"
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $item['active'] ? 'bg-white text-brand-700 shadow-sm' : 'text-brand-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons[$item['icon']] ?? '' !!}</svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                    @endif
                @endforeach
            @endforeach
        </nav>

        <div class="p-3 border-t border-white/10 shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-brand-100 hover:bg-white/10 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['logout'] !!}</svg>
                    Cerrar sesion
                </button>
            </form>
        </div>
    </aside>

    <!-- ===================== CONTENIDO ===================== -->
    <div class="md:ml-64 flex flex-col min-h-screen">

        <!-- Topbar -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center gap-3 sm:gap-4 px-4 sm:px-6 lg:px-8 sticky top-0 z-20">
            <button @click="open = true" class="md:hidden -ml-1 p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="min-w-0">
                <h1 class="text-lg sm:text-xl lg:text-2xl font-extrabold text-slate-800 truncate">@yield('title', 'Panel de Control')</h1>
                <p class="text-xs sm:text-sm text-slate-400 truncate">@yield('subtitle', 'Bienvenido, '.$user->name)</p>
            </div>

            <div class="ml-auto hidden xl:flex items-center relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['search'] !!}</svg>
                <input type="text" placeholder="Buscar..." class="w-64 pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent focus:border-brand-300 focus:bg-white text-sm outline-none transition">
            </div>

            @if ($user->empresa)
                <div class="hidden lg:flex flex-col items-end leading-tight ml-auto xl:ml-2">
                    <span class="text-sm font-bold text-slate-700 truncate max-w-[160px]">{{ $user->empresa->nombre }}</span>
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-brand-50 text-brand-700 font-semibold">Plan {{ optional($user->empresa->plan)->nombre ?: 'Sin plan' }}</span>
                </div>
            @endif

            <div class="relative {{ $user->empresa ? '' : 'ml-auto' }}" x-data="{ menu: false }">
                <button @click="menu = !menu" class="flex items-center gap-2 sm:gap-3 pl-1 sm:pl-2">
                    <div class="w-10 h-10 rounded-full bg-brand-600 text-white flex items-center justify-center text-sm font-bold shrink-0">{{ $user->iniciales() }}</div>
                    <div class="text-left hidden sm:block leading-tight">
                        <p class="text-sm font-bold text-slate-800 truncate max-w-[120px]">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $user->rolLabel() }}</p>
                    </div>
                </button>
                <div x-show="menu" x-cloak @click.outside="menu = false" x-transition class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-slate-100 py-2 text-sm z-30">
                    <div class="px-4 py-2 border-b border-slate-100">
                        <p class="font-semibold text-slate-800 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                    </div>
                    <a href="{{ route('perfil.edit') }}" class="block px-4 py-2 text-slate-600 hover:bg-slate-50">Mi perfil</a>
                    @if ($user->esAdmin())
                        <a href="{{ route('configuracion.edit') }}" class="block px-4 py-2 text-slate-600 hover:bg-slate-50">Configuracion</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">Cerrar sesion</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @php $dv = $user->empresa ? $user->empresa->diasParaVencer() : null; @endphp
            @if ($dv !== null && $dv >= 0 && $dv <= 7)
                <div class="mb-5 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M10.3 3.9l-8 14A2 2 0 004 21h16a2 2 0 001.7-3l-8-14a2 2 0 00-3.4 0z"/></svg>
                    <span class="flex-1">Tu suscripcion (plan {{ optional($user->empresa->plan)->nombre ?: 'actual' }}) vence en <strong>{{ $dv }} dia(s)</strong>. Renueva para no perder el acceso.</span>
                </div>
            @endif
            @if (session('ok'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span class="flex-1">{{ session('ok') }}</span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9l-8 14A2 2 0 004 21h16a2 2 0 001.7-3l-8-14a2 2 0 00-3.4 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
