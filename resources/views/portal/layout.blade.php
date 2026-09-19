@php $c = auth('cliente')->user(); @endphp
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') — VetSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','ui-sans-serif']},colors:{brand:{50:'#ecfeff',100:'#cffafe',500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63'}}}}};</script>
    <style>body{font-family:'Inter',sans-serif}[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">
@php
    $nav = [
        ['Inicio', 'portal.dashboard'],
        ['Mis Mascotas', 'portal.mascotas'],
        ['Mis Citas', 'portal.citas'],
        ['Vacunas', 'portal.vacunas'],
    ];
@endphp
<header class="bg-white border-b border-slate-200 sticky top-0 z-20" x-data="{ open: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center gap-4">
        <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
            <div class="leading-tight"><p class="font-extrabold text-slate-800">VetSystem</p><p class="text-[11px] text-slate-400">Portal del cliente</p></div>
        </a>
        <nav class="hidden md:flex items-center gap-1 ml-6">
            @foreach ($nav as [$label, $route])
                <a href="{{ route($route) }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs($route) ? 'bg-brand-50 text-brand-700' : 'text-slate-500 hover:bg-slate-100' }}">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="ml-auto flex items-center gap-3">
            <div class="hidden sm:block text-right leading-tight"><p class="text-sm font-bold text-slate-800 truncate max-w-[160px]">{{ $c->nombre }}</p><p class="text-xs text-slate-400">Cliente</p></div>
            <div class="w-9 h-9 rounded-full bg-brand-600 text-white flex items-center justify-center text-sm font-bold">{{ mb_strtoupper(mb_substr($c->nombre,0,1)) }}</div>
            <form method="POST" action="{{ route('portal.logout') }}">@csrf<button class="text-slate-400 hover:text-red-600 p-1" title="Cerrar sesion"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5M21 12H9M12 19H6a1 1 0 01-1-1V6a1 1 0 011-1h6"/></svg></button></form>
            <button @click="open=!open" class="md:hidden p-1 text-slate-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        </div>
    </div>
    <div x-show="open" x-cloak class="md:hidden border-t border-slate-100 px-4 py-2 space-y-1">
        @foreach ($nav as [$label, $route])
            <a href="{{ route($route) }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs($route) ? 'bg-brand-50 text-brand-700' : 'text-slate-600' }}">{{ $label }}</a>
        @endforeach
    </div>
</header>

<main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8">
    @if (session('ok'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">{{ session('ok') }}</div>
    @endif
    @yield('content')
</main>

<footer class="py-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} VetSystem · Portal del cliente</footer>
</body>
</html>
