@extends('layouts.app')

@section('title', 'Consulta clinica')
@section('subtitle', optional($consulta->mascota)->nombre.' · '.$consulta->fecha->format('d/m/Y H:i'))

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('historia.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('historia.edit', $consulta) }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Editar</a>
            <form method="POST" action="{{ route('historia.destroy', $consulta) }}" onsubmit="return confirm('Eliminar esta consulta?')">@csrf @method('DELETE')
                <button class="px-4 py-2 rounded-xl bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100">Eliminar</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-br from-brand-700 to-brand-900 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold">{{ optional($consulta->mascota)->nombre }}</h2>
                    <p class="text-brand-100">{{ optional($consulta->mascota)->especie }} · {{ optional(optional($consulta->mascota)->cliente)->nombre }}</p>
                </div>
                <div class="text-right text-sm">
                    <p class="font-bold">{{ $consulta->fecha->format('d/m/Y') }}</p>
                    <p class="text-brand-200">{{ $consulta->fecha->format('H:i') }} hrs</p>
                </div>
            </div>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-400 font-semibold">VETERINARIO</p><p class="font-semibold text-slate-700 text-sm mt-0.5">{{ optional($consulta->veterinario)->name ?: '—' }}</p></div>
                <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-400 font-semibold">MOTIVO</p><p class="font-semibold text-slate-700 text-sm mt-0.5">{{ $consulta->motivo ?: '—' }}</p></div>
                <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-400 font-semibold">PESO</p><p class="font-semibold text-slate-700 text-sm mt-0.5">{{ $consulta->peso ? $consulta->peso.' kg' : '—' }}</p></div>
                <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-400 font-semibold">TEMP.</p><p class="font-semibold text-slate-700 text-sm mt-0.5">{{ $consulta->temperatura ? $consulta->temperatura.' C' : '—' }}</p></div>
            </div>
            @foreach (['Sintomas'=>'sintomas','Diagnostico'=>'diagnostico','Tratamiento'=>'tratamiento','Observaciones'=>'observaciones'] as $label => $campo)
                <div>
                    <p class="text-sm font-bold text-slate-700 mb-1">{{ $label }}</p>
                    <p class="text-sm text-slate-500 bg-slate-50 rounded-xl p-3 whitespace-pre-line">{{ $consulta->$campo ?: 'Sin registro' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
