@extends('portal.layout')
@section('title', 'Inicio')

@section('content')
<div class="bg-gradient-to-br from-brand-700 to-brand-900 text-white rounded-3xl p-6 sm:p-8 mb-6">
    <h1 class="text-2xl sm:text-3xl font-extrabold">Hola, {{ $cliente->nombre }}</h1>
    <p class="text-brand-100 mt-1">Bienvenido a tu portal. Aqui puedes seguir la salud de tus mascotas.</p>
    <div class="mt-5 flex flex-wrap gap-3">
        <div class="bg-white/10 rounded-2xl px-5 py-3"><p class="text-2xl font-extrabold">{{ $mascotas->count() }}</p><p class="text-xs text-brand-200">Mascotas</p></div>
        <div class="bg-white/10 rounded-2xl px-5 py-3"><p class="text-2xl font-extrabold">{{ $proximasCitas->count() }}</p><p class="text-xs text-brand-200">Proximas citas</p></div>
        <div class="bg-white/10 rounded-2xl px-5 py-3"><p class="text-2xl font-extrabold">{{ $proximasVacunas->count() }}</p><p class="text-xs text-brand-200">Vacunas proximas</p></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4"><h2 class="font-extrabold text-slate-800">Mis mascotas</h2><a href="{{ route('portal.mascotas') }}" class="text-sm font-semibold text-brand-600">Ver todas</a></div>
        @forelse ($mascotas->take(4) as $m)
            <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
                <div><p class="font-semibold text-slate-800">{{ $m->nombre }}</p><p class="text-xs text-slate-400">{{ $m->especie }} · {{ $m->raza ?: 'Sin raza' }}</p></div>
            </div>
        @empty
            <p class="text-sm text-slate-400 py-6 text-center">Aun no tienes mascotas registradas.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <h2 class="font-extrabold text-slate-800 mb-4">Proximas citas</h2>
        @forelse ($proximasCitas as $cita)
            <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-xs text-center">{{ $cita->fecha->format('d/m') }}</div>
                <div class="flex-1 min-w-0"><p class="font-semibold text-slate-800 truncate">{{ optional($cita->mascota)->nombre }} — {{ $cita->motivo ?: 'Consulta' }}</p><p class="text-xs text-slate-400">{{ $cita->fecha->format('H:i') }} · {{ optional($cita->veterinario)->name ?: 'Por asignar' }}</p></div>
            </div>
        @empty
            <p class="text-sm text-slate-400 py-6 text-center">No tienes citas proximas.</p>
        @endforelse
    </div>
</div>

@if ($proximasVacunas->isNotEmpty())
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 mt-6">
    <h2 class="font-extrabold text-slate-800 mb-4">Vacunas proximas</h2>
    <div class="divide-y divide-slate-50">
        @foreach ($proximasVacunas as $v)
            <div class="flex items-center gap-3 py-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/></svg></div>
                <div class="flex-1"><p class="font-semibold text-slate-800">{{ $v->nombre }} <span class="text-slate-400 font-normal">· {{ optional($v->mascota)->nombre }}</span></p></div>
                <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-semibold">{{ $v->proxima_dosis->format('d/m/Y') }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
