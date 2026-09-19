@extends('layouts.app')

@section('title', 'Auditoria')
@section('subtitle', 'Bitacora de actividad del sistema')

@section('content')
<form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
    <select name="accion" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
        <option value="">Todas las acciones</option>
        <option value="creo" @selected($accion==='creo')>Creaciones</option>
        <option value="edito" @selected($accion==='edito')>Ediciones</option>
        <option value="elimino" @selected($accion==='elimino')>Eliminaciones</option>
    </select>
    <select name="modelo" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
        <option value="">Todos los modulos</option>
        @foreach ($modelos as $m)<option value="{{ $m }}" @selected($modelo===$m)>{{ $m }}</option>@endforeach
    </select>
    @if($accion || $modelo)<a href="{{ route('auditoria.index') }}" class="text-sm text-slate-400 hover:text-brand-600">Limpiar</a>@endif
</form>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">FECHA</th>
                    <th class="px-6 py-4">USUARIO</th>
                    <th class="px-6 py-4">ACCION</th>
                    <th class="px-6 py-4">REGISTRO</th>
                    <th class="px-6 py-4">DETALLE</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($registros as $r)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-3 text-slate-500 whitespace-nowrap">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-3 text-slate-700 font-semibold">{{ optional($r->usuario)->name ?: 'Sistema' }}</td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                @if($r->accion==='creo') bg-emerald-100 text-emerald-700
                                @elseif($r->accion==='elimino') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">{{ $r->accionLabel() }}</span>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $r->modelo }} <span class="text-slate-300">#{{ $r->modelo_id }}</span></td>
                        <td class="px-6 py-3 text-slate-500 truncate max-w-xs">{{ $r->descripcion }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
                        <p class="font-bold text-slate-600">Sin actividad registrada</p>
                        <p class="text-sm text-slate-400">Las acciones del personal apareceran aqui.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($registros->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $registros->links() }}</div>@endif
</div>
@endsection
