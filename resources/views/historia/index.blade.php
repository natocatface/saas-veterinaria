@extends('layouts.app')

@section('title', 'Historia Clinica')
@section('subtitle', 'Consultas y evolucion de pacientes')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex-1 max-w-md">
        <div class="relative">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Buscar por mascota o dueno..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm">
        </div>
    </form>
    <a href="{{ route('historia.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nueva Consulta
    </a>
</div>

<div class="space-y-3">
    @forelse ($consultas as $c)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-wrap items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-700 flex flex-col items-center justify-center font-bold text-xs leading-none">
                <span class="text-base">{{ $c->fecha->format('d') }}</span>{{ $c->fecha->isoFormat('MMM') }}
            </div>
            <div class="flex-1 min-w-[200px]">
                <p class="font-semibold text-slate-800">{{ optional($c->mascota)->nombre }} <span class="text-slate-400 font-normal text-sm">· {{ optional(optional($c->mascota)->cliente)->nombre }}</span></p>
                <p class="text-sm text-slate-500">{{ $c->motivo ?: 'Consulta general' }}{{ $c->diagnostico ? ' — Dx: '.\Illuminate\Support\Str::limit($c->diagnostico, 60) : '' }}</p>
            </div>
            <div class="text-sm text-slate-400 hidden sm:block">{{ optional($c->veterinario)->name ?: 'Sin asignar' }}</div>
            <div class="flex items-center gap-1.5">
                <a href="{{ route('historia.show', $c) }}" class="p-2 rounded-lg text-slate-400 hover:bg-brand-50 hover:text-brand-600" title="Ver"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg></a>
                <a href="{{ route('historia.edit', $c) }}" class="p-2 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600" title="Editar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/></svg></a>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 4h6v2H9zM7 4H6a1 1 0 00-1 1v15a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-1-1h-1"/></svg></div>
            <p class="font-bold text-slate-600">Sin consultas registradas</p>
            <p class="text-sm text-slate-400">Registra la primera consulta clinica.</p>
        </div>
    @endforelse
</div>
@if ($consultas->hasPages())<div class="mt-5">{{ $consultas->links() }}</div>@endif
@endsection
