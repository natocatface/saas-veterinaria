@extends('layouts.app')

@section('title', $mascota->nombre)
@section('subtitle', 'Ficha del paciente')

@section('content')
<a href="{{ route('mascotas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver a pacientes
</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center"><svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800">{{ $mascota->nombre }}</h2>
                <p class="text-sm text-slate-400">{{ $mascota->especie }} · {{ $mascota->raza ?: 'Sin raza' }}</p>
            </div>
        </div>
        <dl class="mt-6 space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">Dueno</dt><dd class="font-semibold text-slate-700">{{ optional($mascota->cliente)->nombre ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Sexo</dt><dd class="font-semibold text-slate-700">{{ $mascota->sexo ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Color</dt><dd class="font-semibold text-slate-700">{{ $mascota->color ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Nacimiento</dt><dd class="font-semibold text-slate-700">{{ optional($mascota->fecha_nacimiento)->format('d/m/Y') ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Peso</dt><dd class="font-semibold text-slate-700">{{ $mascota->peso ? $mascota->peso.' kg' : '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Esterilizado</dt><dd class="font-semibold text-slate-700">{{ $mascota->esterilizado ? 'Si' : 'No' }}</dd></div>
        </dl>
        @if ($mascota->notas)<p class="mt-4 text-sm text-slate-500 bg-slate-50 rounded-xl p-3">{{ $mascota->notas }}</p>@endif
        <div class="mt-6 flex gap-2">
            <a href="{{ route('mascotas.edit', $mascota) }}" class="flex-1 text-center px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200">Editar</a>
            <a href="{{ route('citas.create', ['mascota_id' => $mascota->id]) }}" class="flex-1 text-center px-4 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700">Agendar cita</a>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-extrabold text-slate-800 mb-4">Historial de citas</h3>
        @if ($mascota->citas->isEmpty())
            <p class="text-sm text-slate-400 py-8 text-center">Sin citas registradas para esta mascota.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($mascota->citas->sortByDesc('fecha') as $cita)
                    <div class="flex items-center gap-4 py-3">
                        <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-xs text-center">{{ $cita->fecha->format('d/m') }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 truncate">{{ $cita->motivo ?: 'Consulta' }}</p>
                            <p class="text-xs text-slate-400">{{ $cita->fecha->format('H:i') }} · {{ optional($cita->veterinario)->name ?: 'Sin asignar' }}</p>
                        </div>
                        <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold
                            @if($cita->estado==='atendida') bg-emerald-100 text-emerald-700
                            @elseif($cita->estado==='cancelada') bg-red-100 text-red-700
                            @else bg-amber-100 text-amber-700 @endif">{{ ucfirst($cita->estado) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
