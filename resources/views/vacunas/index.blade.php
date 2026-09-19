@extends('layouts.app')

@section('title', 'Vacunaciones')
@section('subtitle', 'Control sanitario de pacientes')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">APLICADAS</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['total'] }}</p></div>
    <div class="bg-gradient-to-br from-cyan-500 to-brand-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">PROXIMAS (30d)</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['proximas'] }}</p></div>
    <div class="bg-gradient-to-br from-rose-500 to-red-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">VENCIDAS</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['vencidas'] }}</p></div>
</div>

<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Buscar vacuna o mascota..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm">
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-slate-600 px-3 py-2.5 rounded-xl border border-slate-200 cursor-pointer">
            <input type="checkbox" name="filtro" value="proximas" onchange="this.form.submit()" @checked($filtro==='proximas') class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            Solo proximas dosis
        </label>
    </form>
    <a href="{{ route('vacunas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nueva Vacuna
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">VACUNA</th>
                    <th class="px-6 py-4">MASCOTA</th>
                    <th class="px-6 py-4">APLICACION</th>
                    <th class="px-6 py-4">PROXIMA DOSIS</th>
                    <th class="px-6 py-4 text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($vacunas as $v)
                    @php $venc = $v->proxima_dosis && $v->proxima_dosis->isPast(); $prox = $v->proxima_dosis && !$venc && $v->proxima_dosis->lte(now()->addDays(30)); @endphp
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/></svg></div>
                                <div><p class="font-semibold text-slate-800">{{ $v->nombre }}</p><p class="text-xs text-slate-400">{{ $v->lote ? 'Lote '.$v->lote : '' }}</p></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-700">{{ optional($v->mascota)->nombre }}</p>
                            <p class="text-xs text-slate-400">{{ optional(optional($v->mascota)->cliente)->nombre }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $v->fecha_aplicacion->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if ($v->proxima_dosis)
                                <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $venc ? 'bg-red-100 text-red-700' : ($prox ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $v->proxima_dosis->format('d/m/Y') }}{{ $venc ? ' · vencida' : ($prox ? ' · pronto' : '') }}
                                </span>
                            @else <span class="text-slate-300">—</span> @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('vacunas.edit', $v) }}" class="p-2 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600" title="Editar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/></svg></a>
                                <form method="POST" action="{{ route('vacunas.destroy', $v) }}" onsubmit="return confirm('Eliminar este registro?')">@csrf @method('DELETE')
                                    <button class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600" title="Eliminar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a1 1 0 01-1 1H7a1 1 0 01-1-1V7"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/></svg></div>
                        <p class="font-bold text-slate-600">Sin vacunas registradas</p>
                        <p class="text-sm text-slate-400">Registra la primera vacuna.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($vacunas->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $vacunas->links() }}</div>@endif
</div>
@endsection
