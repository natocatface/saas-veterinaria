<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registra tu clinica — VetSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] },
            colors: { brand: { 50:'#ecfeff',100:'#cffafe',400:'#22d3ee',500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63' } }
        }}};
    </script>
    <style>body{font-family:'Inter',sans-serif} [x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
<div class="min-h-screen">
    <!-- Hero -->
    <div class="bg-gradient-to-br from-brand-700 via-brand-800 to-brand-900 text-white">
        <header class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
                <span class="text-xl font-extrabold">VetSystem</span>
            </div>
            <a href="{{ route('login') }}" class="text-sm font-semibold text-brand-100 hover:text-white">Ya tengo cuenta →</a>
        </header>
        <div class="max-w-6xl mx-auto px-6 pt-6 pb-16 text-center">
            <span class="inline-block px-3 py-1 rounded-full bg-white/10 text-xs font-semibold text-brand-100">Prueba gratis por 15 dias · Sin tarjeta</span>
            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold leading-tight">Gestiona tu clinica veterinaria<br class="hidden sm:block"> desde un solo lugar</h1>
            <p class="mt-3 text-brand-100/90 max-w-xl mx-auto">Pacientes, citas, historias clinicas, inventario y facturacion. Crea tu cuenta en menos de un minuto.</p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 -mt-10 pb-16" x-data="{ plan: '{{ old('plan_id', optional($planes->first())->id) }}' }">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm max-w-3xl mx-auto">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registro.store') }}" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            @csrf

            <!-- Planes -->
            <div class="lg:col-span-3">
                <h2 class="text-lg font-extrabold text-slate-800 mb-3">1. Elige tu plan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach ($planes as $p)
                        <label class="cursor-pointer">
                            <input type="radio" name="plan_id" value="{{ $p->id }}" x-model="plan" class="sr-only">
                            <div class="h-full rounded-2xl border-2 bg-white p-5 transition"
                                 :class="plan == '{{ $p->id }}' ? 'border-brand-500 ring-2 ring-brand-100 shadow-lg' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <p class="font-extrabold text-slate-800">{{ $p->nombre }}</p>
                                    <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                          :class="plan == '{{ $p->id }}' ? 'border-brand-500' : 'border-slate-300'">
                                        <span x-show="plan == '{{ $p->id }}'" class="w-2 h-2 rounded-full bg-brand-500"></span>
                                    </span>
                                </div>
                                <p class="mt-2"><span class="text-2xl font-extrabold text-slate-800">S/ {{ number_format($p->precio, 0) }}</span><span class="text-xs text-slate-400">/{{ $p->periodo === 'anual' ? 'ano' : 'mes' }}</span></p>
                                <ul class="mt-3 space-y-1.5 text-xs text-slate-500">
                                    <li>{{ $p->limiteUsuarios() }} usuarios</li>
                                    <li>{{ $p->limitePacientes() }} pacientes</li>
                                    @foreach (array_slice(array_filter(array_map('trim', explode("\n", (string) $p->caracteristicas))), 0, 2) as $c)<li>{{ $c }}</li>@endforeach
                                </ul>
                            </div>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-400 mt-3">Comienzas con 15 dias de prueba. Puedes cambiar de plan cuando quieras.</p>
            </div>

            <!-- Datos -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-lg font-extrabold text-slate-800 mb-4">2. Crea tu cuenta</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1">Nombre de la clinica *</label>
                            <input name="empresa" value="{{ old('empresa') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-1">RUC</label>
                                <input name="ruc" value="{{ old('ruc') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-1">Telefono</label>
                                <input name="telefono" value="{{ old('telefono') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                            </div>
                        </div>
                        <div class="pt-2 border-t border-slate-100">
                            <label class="block text-sm font-semibold text-slate-600 mb-1">Tu nombre *</label>
                            <input name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1">Correo *</label>
                            <input name="email" type="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-1">Contrasena *</label>
                                <input name="password" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-1">Confirmar *</label>
                                <input name="password_confirmation" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold hover:from-brand-700 hover:to-brand-600 shadow-lg shadow-brand-500/25 transition">Crear mi clinica</button>
                        <p class="text-xs text-slate-400 text-center">Al registrarte aceptas los terminos del servicio.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
