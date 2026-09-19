@extends('layouts.app')

@section('title', 'Agenda de Citas')
@section('subtitle', 'Programacion de la clinica')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">CITAS HOY</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['hoy'] }}</p></div>
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">PENDIENTES</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['pendientes'] }}</p></div>
    <div class="bg-gradient-to-br from-violet-500 to-purple-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">ESTA SEMANA</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['semana'] }}</p></div>
</div>

<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
        <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
        <select name="estado" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
            <option value="">Todos los estados</option>
            @foreach ($estados as $e)<option value="{{ $e }}" @selected($estado===$e)>{{ ucfirst($e) }}</option>@endforeach
        </select>
        @if($fecha || $estado)<a href="{{ route('citas.index') }}" class="text-sm text-slate-400 hover:text-brand-600">Limpiar</a>@endif
    </form>
    <a href="{{ route('export.citas') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg><span class="hidden sm:inline">Exportar</span></a>
                    <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nueva Cita
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">FECHA / HORA</th>
                    <th class="px-6 py-4">MASCOTA / DUENO</th>
                    <th class="px-6 py-4">MOTIVO</th>
                    <th class="px-6 py-4">VETERINARIO</th>
                    <th class="px-6 py-4 text-center">ESTADO</th>
                    <th class="px-6 py-4 text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($citas as $cita)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">{{ $cita->fecha->format('d/m/Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $cita->fecha->format('H:i') }} hrs</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">{{ optional($cita->mascota)->nombre ?: '—' }}</p>
                            <p class="text-xs text-slate-400">{{ optional(optional($cita->mascota)->cliente)->nombre }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $cita->motivo ?: 'Consulta general' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($cita->veterinario)->name ?: 'Sin asignar' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                @if($cita->estado==='atendida') bg-emerald-100 text-emerald-700
                                @elseif($cita->estado==='cancelada') bg-red-100 text-red-700
                                @elseif($cita->estado==='confirmada') bg-brand-100 text-brand-700
                                @else bg-amber-100 text-amber-700 @endif">{{ ucfirst($cita->estado) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5" x-data="{ open:false }">
                                @if($cita->estado !== 'atendida')
                                    <form method="POST" action="{{ route('citas.cambiar-estado', [$cita, 'atendida']) }}">@csrf @method('PATCH')
                                        <button title="Marcar atendida" class="p-2 rounded-lg text-slate-400 hover:bg-emerald-50 hover:text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></button>
                                    </form>
                                @endif
                                <a href="{{ route('citas.edit', $cita) }}" title="Editar" class="p-2 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/></svg></a>
                                <form method="POST" action="{{ route('citas.destroy', $cita) }}" onsubmit="return confirm('Eliminar esta cita?')">@csrf @method('DELETE')
                                    <button title="Eliminar" class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a1 1 0 01-1 1H7a1 1 0 01-1-1V7"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 6h14a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg></div>
                        <p class="font-bold text-slate-600">Sin citas</p>
                        <p class="text-sm text-slate-400">No hay citas con los filtros seleccionados.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($citas->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $citas->links() }}</div>@endif
</div>
@endsection
