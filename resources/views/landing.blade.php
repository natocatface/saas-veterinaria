<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VetSystem — Gestiona tu clinica veterinaria de forma inteligente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] },
            colors: { brand: { 50:'#ecfeff',100:'#cffafe',200:'#a5f3fc',300:'#67e8f9',400:'#22d3ee',500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63' } }
        }}};
    </script>
    <style>
        body{font-family:'Inter',sans-serif}
        .grad-text{background:linear-gradient(90deg,#67e8f9,#a5f3fc,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent}
        .hero-glow{background:radial-gradient(60% 55% at 75% 15%, rgba(34,211,238,.28), transparent 60%), radial-gradient(50% 50% at 15% 90%, rgba(16,185,129,.18), transparent 60%)}
        [x-cloak]{display:none!important}
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
@php
    $icons = [
        'paw' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.5 11a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6zM10 8a1.8 1.8 0 100-3.6A1.8 1.8 0 0010 8zM14 8a1.8 1.8 0 100-3.6A1.8 1.8 0 0014 8zM18.5 11a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6zM12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/>',
    ];
    $features = [
        ['Pacientes y clientes','Fichas completas de cada mascota y su dueno, con historial a la mano.','<path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 10-8 0M12 7a3 3 0 100 6 3 3 0 000-6zM3 20c0-2.5 2-4 5-4M21 20c0-2.5-2-4-5-4"/>'],
        ['Agenda de citas','Programa citas por veterinario, con estados y recordatorios por correo.','<path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 6h14a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/>'],
        ['Historia clinica','Consultas, diagnostico, tratamiento y evolucion de cada paciente.','<path stroke-linecap="round" stroke-linejoin="round" d="M9 4h6v2H9zM7 4H6a1 1 0 00-1 1v15a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-1-1h-1"/>'],
        ['Facturacion y caja','Boletas, facturas y tickets con IGV, arqueo de caja e ingresos.','<path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5M9 13h6M9 17h6"/>'],
        ['Inventario y farmacia','Control de stock con alertas y ajuste rapido de productos.','<path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 00-1-1.7l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.7l7 4a2 2 0 002 0l7-4A2 2 0 0021 16zM3.3 7L12 12l8.7-5"/>'],
        ['Reportes y BI','Indicadores del negocio, ingresos por mes y productos mas vendidos.','<path stroke-linecap="round" stroke-linejoin="round" d="M5 20V10M12 20V4M19 20v-6M3 20h18"/>'],
        ['Portal del cliente','Los duenos ven sus mascotas, citas y vacunas desde su cuenta.','<path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/>'],
        ['Vacunaciones y peluqueria','Calendario de vacunas con alertas y servicios de grooming.','<path stroke-linecap="round" stroke-linejoin="round" d="M6 9a3 3 0 100-6 3 3 0 000 6zM6 21a3 3 0 100-6 3 3 0 000 6zM8.5 8.5L20 20M8.5 15.5L20 4"/>'],
    ];
    $destacado = $planes->count() ? intdiv($planes->count() - 1, 2) : 0;
@endphp

<!-- ===================== NAV ===================== -->
<header class="absolute top-0 inset-x-0 z-30" x-data="{ open:false }">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 h-20 flex items-center justify-between">
        <a href="#" class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['paw'] !!}</svg>
            </div>
            <span class="text-xl font-extrabold tracking-tight">VetSystem</span>
        </a>
        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-300">
            <a href="#funciones" class="hover:text-white transition">Funciones</a>
            <a href="#precios" class="hover:text-white transition">Precios</a>
            <a href="{{ route('login') }}" class="hover:text-white transition">Iniciar sesion</a>
            <a href="{{ route('registro.show') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-brand-500 to-brand-400 text-white font-bold shadow-lg shadow-brand-500/30 hover:from-brand-600 hover:to-brand-500 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/></svg>
                Prueba gratis
            </a>
        </nav>
        <button @click="open=!open" class="md:hidden p-2 text-slate-200"><svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
    </div>
    <div x-show="open" x-cloak class="md:hidden mx-5 mb-2 rounded-2xl bg-slate-900/95 border border-white/10 p-4 space-y-2 text-sm font-semibold">
        <a href="#funciones" class="block px-3 py-2 rounded-lg text-slate-200 hover:bg-white/5">Funciones</a>
        <a href="#precios" class="block px-3 py-2 rounded-lg text-slate-200 hover:bg-white/5">Precios</a>
        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-slate-200 hover:bg-white/5">Iniciar sesion</a>
        <a href="{{ route('registro.show') }}" class="block px-3 py-2 rounded-lg bg-brand-500 text-white text-center">Prueba gratis</a>
    </div>
</header>

<!-- ===================== HERO ===================== -->
<section class="relative overflow-hidden bg-gradient-to-br from-brand-900 via-brand-800 to-slate-950">
    <div class="absolute inset-0 hero-glow"></div>
    <div class="absolute -top-32 -right-24 w-[32rem] h-[32rem] bg-brand-500/10 rounded-full blur-3xl"></div>
    <div class="relative max-w-5xl mx-auto px-5 sm:px-8 pt-40 pb-24 text-center">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/10 text-sm font-semibold text-brand-100 backdrop-blur">
            <span>🐾</span> Plataforma SaaS #1 para veterinarias
        </span>
        <h1 class="mt-8 text-5xl sm:text-6xl md:text-7xl font-black leading-[1.05] tracking-tight">
            Gestiona tu clinica<br>
            <span class="grad-text">de forma inteligente</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-slate-300 max-w-2xl mx-auto">
            Todo lo que necesitas para administrar pacientes, citas, historias clinicas, inventario y facturacion. Sin complicaciones, desde cualquier dispositivo.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('registro.show') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-400 text-white font-bold text-lg shadow-xl shadow-brand-500/30 hover:from-brand-600 hover:to-brand-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/></svg>
                Comenzar gratis — 15 dias
            </a>
            <a href="#precios" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-white/10 border border-white/15 text-white font-bold text-lg hover:bg-white/15 transition backdrop-blur">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a2 2 0 011.4.6l7 7a2 2 0 010 2.8l-5.2 5.2a2 2 0 01-2.8 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                Ver precios
            </a>
        </div>
        <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-8 max-w-3xl mx-auto">
            @foreach ([['500+','Clinicas activas'],['80k+','Mascotas gestionadas'],['99.9%','Uptime garantizado'],['15 dias','Prueba gratuita']] as $stat)
                <div>
                    <p class="text-3xl sm:text-4xl font-black text-white">{{ $stat[0] }}</p>
                    <p class="text-sm text-slate-400 mt-1">{{ $stat[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <div class="h-16 bg-gradient-to-b from-transparent to-slate-950"></div>
</section>

<!-- ===================== FUNCIONES ===================== -->
<section id="funciones" class="bg-slate-950 py-24">
    <div class="max-w-7xl mx-auto px-5 sm:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-brand-400 font-bold tracking-wide">FUNCIONES</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-black text-white">Todo tu consultorio en una sola plataforma</h2>
            <p class="mt-4 text-slate-400">Un sistema completo que crece con tu veterinaria, de la atencion clinica a la administracion del negocio.</p>
        </div>
        <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($features as $f)
                <div class="group rounded-3xl bg-slate-900/60 border border-white/5 p-6 hover:border-brand-500/40 hover:bg-slate-900 transition">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500/20 to-brand-400/10 border border-brand-500/20 flex items-center justify-center text-brand-300 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $f[2] !!}</svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-white">{{ $f[0] }}</h3>
                    <p class="mt-2 text-sm text-slate-400 leading-relaxed">{{ $f[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===================== PRECIOS ===================== -->
<section id="precios" class="bg-gradient-to-b from-slate-950 to-brand-900/40 py-24">
    <div class="max-w-6xl mx-auto px-5 sm:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-brand-400 font-bold tracking-wide">PRECIOS</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-black text-white">Planes para cada tamano de clinica</h2>
            <p class="mt-4 text-slate-400">Empieza gratis por 15 dias. Sin tarjeta. Cambia o cancela cuando quieras.</p>
        </div>
        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            @forelse ($planes as $plan)
                @php $feat = $loop->index === $destacado; @endphp
                <div class="relative rounded-3xl p-7 {{ $feat ? 'bg-white text-slate-800 shadow-2xl shadow-brand-500/20 md:-mt-3 ring-2 ring-brand-400' : 'bg-slate-900/60 border border-white/10 text-slate-200' }}">
                    @if ($feat)
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-gradient-to-r from-brand-500 to-brand-400 text-white text-xs font-bold shadow">Mas popular</span>
                    @endif
                    <h3 class="text-xl font-extrabold {{ $feat ? 'text-slate-900' : 'text-white' }}">{{ $plan->nombre }}</h3>
                    <p class="mt-4">
                        <span class="text-4xl font-black {{ $feat ? 'text-slate-900' : 'text-white' }}">S/ {{ number_format($plan->precio, 0) }}</span>
                        <span class="text-sm {{ $feat ? 'text-slate-400' : 'text-slate-400' }}">/{{ $plan->periodo === 'anual' ? 'ano' : 'mes' }}</span>
                    </p>
                    <ul class="mt-6 space-y-3 text-sm {{ $feat ? 'text-slate-600' : 'text-slate-300' }}">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $plan->limiteUsuarios() }} usuarios</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $plan->limitePacientes() }} pacientes</li>
                        @foreach (array_slice(array_filter(array_map('trim', explode("\n", (string) $plan->caracteristicas))), 0, 4) as $c)
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $c }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('registro.show') }}" class="mt-7 block text-center px-5 py-3 rounded-2xl font-bold transition {{ $feat ? 'bg-gradient-to-r from-brand-500 to-brand-400 text-white hover:from-brand-600 hover:to-brand-500 shadow-lg shadow-brand-500/25' : 'bg-white/10 text-white hover:bg-white/15 border border-white/10' }}">
                        Comenzar gratis
                    </a>
                </div>
            @empty
                <div class="md:col-span-3 text-center text-slate-400 py-10">Pronto publicaremos nuestros planes.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- ===================== CTA FINAL ===================== -->
<section class="bg-brand-900/40 py-20">
    <div class="max-w-4xl mx-auto px-5 sm:px-8">
        <div class="rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 p-10 sm:p-14 text-center relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full"></div>
            <h2 class="relative text-3xl sm:text-4xl font-black text-white">Lleva tu veterinaria al siguiente nivel</h2>
            <p class="relative mt-3 text-brand-100 max-w-xl mx-auto">Unete a las clinicas que ya digitalizaron su gestion con VetSystem. Configuralo en minutos.</p>
            <a href="{{ route('registro.show') }}" class="relative inline-flex items-center gap-2 mt-8 px-8 py-4 rounded-2xl bg-white text-brand-700 font-bold text-lg hover:bg-brand-50 transition shadow-xl">
                Crear mi cuenta gratis
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </div>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="bg-slate-950 border-t border-white/5 py-10">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['paw'] !!}</svg></div>
            <span class="font-extrabold">VetSystem</span>
        </div>
        <div class="flex items-center gap-6 text-sm text-slate-400">
            <a href="{{ route('login') }}" class="hover:text-white">Iniciar sesion</a>
            <a href="{{ route('portal.login') }}" class="hover:text-white">Portal del cliente</a>
            <a href="{{ route('registro.show') }}" class="hover:text-white">Prueba gratis</a>
        </div>
        <p class="text-xs text-slate-500">&copy; {{ date('Y') }} VetSystem. Todos los derechos reservados.</p>
    </div>
</footer>
</body>
</html>
