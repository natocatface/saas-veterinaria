<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesion — VetSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] },
            colors: { brand: { 50:'#ecfeff',100:'#cffafe',200:'#a5f3fc',400:'#22d3ee',500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63' } }
        }}};
    </script>
    <style>body{font-family:'Inter',sans-serif}[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100">
<div class="min-h-screen flex">

    <!-- Panel izquierdo (branding) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-brand-700 via-brand-800 to-brand-900 text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-32 -left-16 w-96 h-96 bg-white/5 rounded-full"></div>
        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.5 11a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6zM10 8a1.8 1.8 0 100-3.6A1.8 1.8 0 0010 8zM14 8a1.8 1.8 0 100-3.6A1.8 1.8 0 0014 8zM18.5 11a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6zM12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold">VetSystem</p>
                    <p class="text-sm text-brand-200">Panel Clinico Veterinario</p>
                </div>
            </div>
            <div class="max-w-md">
                <h2 class="text-4xl font-extrabold leading-tight">Gestiona tu clinica veterinaria en un solo lugar.</h2>
                <p class="mt-4 text-brand-100/90 text-lg">Pacientes, citas, historias clinicas, inventario y facturacion. Todo el cuidado de las mascotas, organizado.</p>
                <div class="mt-8 grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-white/10 rounded-xl px-4 py-3"><p class="font-bold text-lg">Agenda</p><p class="text-brand-200">Citas y recordatorios</p></div>
                    <div class="bg-white/10 rounded-xl px-4 py-3"><p class="font-bold text-lg">Historia</p><p class="text-brand-200">Fichas clinicas</p></div>
                    <div class="bg-white/10 rounded-xl px-4 py-3"><p class="font-bold text-lg">Inventario</p><p class="text-brand-200">Farmacia y stock</p></div>
                    <div class="bg-white/10 rounded-xl px-4 py-3"><p class="font-bold text-lg">Caja</p><p class="text-brand-200">Ingresos y cierre</p></div>
                </div>
            </div>
            <p class="text-sm text-brand-200/70">&copy; {{ date('Y') }} VetSystem. Todos los derechos reservados.</p>
        </div>
    </div>

    <!-- Panel derecho (formulario) -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md" x-data="{ show:false, email:'{{ old('email') }}', password:'', fill(e,p){ this.email=e; this.password=p; } }">
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-11 h-11 rounded-xl bg-brand-600 text-white flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg>
                </div>
                <p class="text-xl font-extrabold text-slate-800">VetSystem</p>
            </div>

            <h1 class="text-3xl font-extrabold text-slate-800">Bienvenido de nuevo</h1>
            <p class="mt-2 text-slate-400">Ingresa tus credenciales para acceder al panel.</p>

            @if ($errors->any())
                <div class="mt-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9l-8 14A2 2 0 004 21h16a2 2 0 001.7-3l-8-14a2 2 0 00-3.4 0z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo electronico</label>
                    <div class="relative">
                        <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1zM4 7l8 6 8-6"/></svg>
                        <input name="email" type="email" x-model="email" autofocus required placeholder="admin@example.com"
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Contrasena</label>
                    <div class="relative">
                        <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 11h14v9a1 1 0 01-1 1H6a1 1 0 01-1-1z"/></svg>
                        <input name="password" :type="show ? 'text' : 'password'" x-model="password" required placeholder="Tu contrasena"
                               class="w-full pl-11 pr-11 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                        <button type="button" @click="show = !show" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-500 select-none">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        Recordarme
                    </label>
                    <a href="{{ route('password.request') }}" class="text-brand-600 hover:text-brand-700 font-semibold">Olvidaste tu contrasena?</a>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold hover:from-brand-700 hover:to-brand-600 shadow-lg shadow-brand-500/25 transition">
                    Ingresar al panel
                </button>
            </form>

            <!-- Acceso rapido (cuentas de demostracion) -->
            <div class="mt-8">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-px bg-slate-200 flex-1"></div>
                    <span class="text-xs font-semibold text-slate-400">Cuentas de demostracion</span>
                    <div class="h-px bg-slate-200 flex-1"></div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 divide-y divide-slate-200 overflow-hidden">
                    @php
                        $demos = [
                            ['superadmin@example.com','Super Admin','bg-indigo-100 text-indigo-700'],
                            ['admin@example.com','Administrador','bg-brand-100 text-brand-700'],
                            ['vet@example.com','Veterinario','bg-emerald-100 text-emerald-700'],
                            ['recepcion@example.com','Recepcion','bg-amber-100 text-amber-700'],
                        ];
                    @endphp
                    @foreach ($demos as $d)
                        <button type="button" @click="fill('{{ $d[0] }}','password')"
                                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 text-sm hover:bg-white transition text-left">
                            <span class="font-mono text-slate-600 truncate">{{ $d[0] }}</span>
                            <span class="shrink-0 text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $d[2] }}">{{ $d[1] }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-slate-400 text-center">Haz clic en una cuenta para autocompletar (contrasena: <span class="font-mono">password</span>).</p>
            </div>

            <p class="mt-6 text-center text-sm text-slate-500">
                No tienes cuenta?
                <a href="{{ route('registro.show') }}" class="font-semibold text-brand-600 hover:text-brand-700">Registra tu clinica gratis</a>
            </p>
            <p class="mt-2 text-center text-sm text-slate-400">
                Eres dueno de una mascota?
                <a href="{{ route('portal.login') }}" class="font-semibold text-slate-600 hover:text-brand-700">Entra al portal del cliente</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
