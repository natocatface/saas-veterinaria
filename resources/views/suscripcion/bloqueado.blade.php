<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suscripcion suspendida — VetSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden text-center">
        <div class="bg-gradient-to-br from-rose-500 to-red-600 text-white p-8">
            <div class="w-16 h-16 rounded-2xl bg-white/15 flex items-center justify-center mx-auto mb-4">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9l-8 14A2 2 0 004 21h16a2 2 0 001.7-3l-8-14a2 2 0 00-3.4 0z"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold">Suscripcion suspendida</h1>
            <p class="text-white/90 mt-1">{{ optional($empresa)->nombre }}</p>
        </div>
        <div class="p-8">
            <p class="text-slate-600">Tu periodo de acceso ha finalizado o tu suscripcion fue suspendida. Para reactivar el servicio, regulariza tu pago con el administrador de la plataforma.</p>
            @if (optional($empresa)->fecha_vencimiento)
                <p class="mt-3 text-sm text-slate-400">Vencio el {{ $empresa->fecha_vencimiento->format('d/m/Y') }}</p>
            @endif
            <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-100 p-4 text-sm text-slate-500">
                Contacta a soporte: <span class="font-semibold text-slate-700">soporte@vetsystem.pe</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button class="w-full py-3 rounded-xl bg-slate-800 text-white font-semibold hover:bg-slate-900">Cerrar sesion</button>
            </form>
        </div>
    </div>
</body>
</html>
