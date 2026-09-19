@extends('layouts.app')

@section('title', 'Telemedicina')
@section('subtitle', 'Consultas veterinarias en linea')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-cyan-500 to-brand-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">HOY</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['hoy'] }}</p></div>
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">PROGRAMADAS</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['programadas'] }}</p></div>
    <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">REALIZADAS</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['realizadas'] }}</p></div>
</div>

<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex-1">
        <select name="estado" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
            <option value="">Todos los estados</option>
            @foreach ($estados as $e)<option value="{{ $e }}" @selected($estado===$e)>{{ ucfirst(str_replace('_',' ',$e)) }}</option>@endforeach
        </select>
    </form>
    <a href="{{ route('telemedicina.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nueva Teleconsulta
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">FECHA</th>
                    <th class="px-6 py-4">MASCOTA</th>
                    <th class="px-6 py-4">VETERINARIO</th>
                    <th class="px-6 py-4 text-center">ESTADO</th>
                    <th class="px-6 py-4 text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($teleconsultas as $t)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4"><p class="font-semibold text-slate-800">{{ $t->fecha->format('d/m/Y') }}</p><p class="text-xs text-slate-400">{{ $t->fecha->format('H:i') }}</p></td>
                        <td class="px-6 py-4"><p class="font-semibold text-slate-700">{{ optional($t->mascota)->nombre }}</p><p class="text-xs text-slate-400">{{ $t->motivo }}</p></td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($t->veterinario)->name ?: 'Sin asignar' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                @if($t->estado==='realizada') bg-emerald-100 text-emerald-700
                                @elseif($t->estado==='cancelada') bg-red-100 text-red-700
                                @elseif($t->estado==='en_curso') bg-brand-100 text-brand-700
                                @else bg-amber-100 text-amber-700 @endif">{{ ucfirst(str_replace('_',' ',$t->estado)) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($t->enlace && in_array($t->estado, ['programada','en_curso']))
                                    <a href="{{ $t->enlace }}" target="_blank" rel="noopener" class="p-2 rounded-lg text-brand-500 hover:bg-brand-50" title="Unirse a la videollamada"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.5-2.5v9L15 14M4 6h9a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg></a>
                                @endif
                                @if($t->estado !== 'realizada')
                                    <form method="POST" action="{{ route('telemedicina.cambiar-estado', [$t, 'realizada']) }}">@csrf @method('PATCH')
                                        <button class="p-2 rounded-lg text-slate-400 hover:bg-emerald-50 hover:text-emerald-600" title="Marcar realizada"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></button>
                                    </form>
                                @endif
                                <a href="{{ route('telemedicina.edit', $t) }}" class="p-2 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600" title="Editar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/></svg></a>
                                <form method="POST" action="{{ route('telemedicina.destroy', $t) }}" onsubmit="return confirm('Eliminar esta teleconsulta?')">@csrf @method('DELETE')
                                    <button class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600" title="Eliminar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a1 1 0 01-1 1H7a1 1 0 01-1-1V7"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.5-2.5v9L15 14M4 6h9a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg></div>
                        <p class="font-bold text-slate-600">Sin teleconsultas</p>
                        <p class="text-sm text-slate-400">Programa la primera consulta en linea.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($teleconsultas->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $teleconsultas->links() }}</div>@endif
</div>
@endsection
