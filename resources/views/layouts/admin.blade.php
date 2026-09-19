@php
    $u = auth()->user();
    $nav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => request()->routeIs('admin.dashboard'),
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>'],
        ['label' => 'Empresas', 'route' => 'admin.empresas.index', 'active' => request()->routeIs('admin.empresas.*'),
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M9 13h.01M9 17h.01M15 9h.01M15 13h.01M15 17h.01"/>'],
        ['label' => 'Planes', 'route' => 'admin.planes.index', 'active' => request()->routeIs('admin.planes.*'),
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 7l2 13a1 1 0 001 1h12a1 1 0 001-1l2-13M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>'],
        ['label' => 'Suscripciones', 'route' => 'admin.suscripciones.index', 'active' => request()->routeIs('admin.suscripciones.*'),
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h4M6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"/>'],
        ['label' => 'Usuarios', 'route' => 'admin.usuarios.index', 'active' => request()->routeIs('admin.usuarios.*'),
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 10-8 0M12 7a3 3 0 100 6 3 3 0 000-6zM3 20c0-2.5 2-4 5-4M21 20c0-2.5-2-4-5-4"/>'],
    ];
@endphp
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Plataforma') — VetSystem SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] },
            colors: { ink: { 700:'#312e81',800:'#1e1b4b',900:'#14122e' }, iris: { 50:'#eef2ff',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca' } }
        }}};
    </script>
    <style>body{font-family:'Inter',sans-serif} [x-cloak]{display:none!important} ::-webkit-scrollbar{width:8px} ::-webkit-scrollbar-thumb{background:#c7d2fe;border-radius:8px}</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
<div x-data="{ open: false }">

    <div x-show="open" x-cloak x-transition.opacity @click="open = false" class="fixed inset-0 bg-slate-900/50 z-30 md:hidden"></div>

    <aside class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-gradient-to-b from-ink-800 to-ink-900 text-white transform transition-transform duration-300 -translate-x-full md:translate-x-0"
           :class="open ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <div class="h-20 flex items-center gap-3 px-6 border-b border-white/10 shrink-0">
            <div class="w-11 h-11 rounded-xl bg-iris-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
            </div>
            <div class="leading-tight">
                <p class="text-lg font-extrabold tracking-tight">VetSystem</p>
                <p class="text-[11px] text-iris-400 font-semibold tracking-wide">PLATAFORMA SaaS</p>
            </div>
            <button @click="open = false" class="ml-auto md:hidden text-iris-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <p class="px-3 pt-2 pb-1 text-[10px] font-bold tracking-widest text-indigo-300/70">ADMINISTRACION</p>
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $item['active'] ? 'bg-white text-iris-700 shadow-sm' : 'text-indigo-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="p-3 border-t border-white/10 shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-indigo-100 hover:bg-white/10 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5M21 12H9M12 19H6a1 1 0 01-1-1V6a1 1 0 011-1h6"/></svg>
                    Cerrar sesion
                </button>
            </form>
        </div>
    </aside>

    <div class="md:ml-64 flex flex-col min-h-screen">
        <header class="h-20 bg-white border-b border-slate-200 flex items-center gap-3 sm:gap-4 px-4 sm:px-6 lg:px-8 sticky top-0 z-20">
            <button @click="open = true" class="md:hidden -ml-1 p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="min-w-0">
                <h1 class="text-lg sm:text-xl lg:text-2xl font-extrabold text-slate-800 truncate">@yield('title', 'Plataforma')</h1>
                <p class="text-xs sm:text-sm text-slate-400 truncate">@yield('subtitle', 'Panel de super administrador')</p>
            </div>
            <div class="ml-auto flex items-center gap-2 sm:gap-3">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-iris-50 text-iris-700 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/></svg>
                    Super Admin
                </span>
                <a href="{{ route('perfil.edit') }}" class="flex items-center gap-3 hover:opacity-80" title="Mi perfil">
                    <div class="w-10 h-10 rounded-full bg-iris-600 text-white flex items-center justify-center text-sm font-bold shrink-0">{{ $u->iniciales() }}</div>
                    <div class="hidden sm:block leading-tight text-left">
                        <p class="text-sm font-bold text-slate-800 truncate max-w-[140px]">{{ $u->name }}</p>
                        <p class="text-xs text-slate-400 truncate max-w-[140px]">{{ $u->email }}</p>
                    </div>
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @if (session('ok'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span class="flex-1">{{ session('ok') }}</span>
                    <button @click="show = false" class="text-emerald-500">&times;</button>
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
