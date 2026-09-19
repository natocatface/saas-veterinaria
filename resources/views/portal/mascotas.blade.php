@extends('portal.layout')
@section('title', 'Mis Mascotas')

@section('content')
<h1 class="text-2xl font-extrabold text-slate-800 mb-6">Mis mascotas</h1>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse ($mascotas as $m)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center"><svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
                <div><p class="text-lg font-extrabold text-slate-800">{{ $m->nombre }}</p><p class="text-sm text-slate-400">{{ $m->especie }} · {{ $m->raza ?: 'Sin raza' }}</p></div>
            </div>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-400">Sexo</dt><dd class="font-semibold text-slate-700">{{ $m->sexo ?: '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Peso</dt><dd class="font-semibold text-slate-700">{{ $m->peso ? $m->peso.' kg' : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Nacimiento</dt><dd class="font-semibold text-slate-700">{{ optional($m->fecha_nacimiento)->format('d/m/Y') ?: '—' }}</dd></div>
            </dl>
        </div>
    @empty
        <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-3xl border border-slate-100 shadow-sm p-16 text-center">
            <p class="font-bold text-slate-600">Sin mascotas</p>
            <p class="text-sm text-slate-400">Tu clinica registrara aqui a tus mascotas.</p>
        </div>
    @endforelse
</div>
@endsection
