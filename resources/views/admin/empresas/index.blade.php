@extends('layouts.admin')

@section('title', 'Empresas')
@section('subtitle', 'Clinicas registradas en la plataforma')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[220px] max-w-md">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Buscar por nombre o RUC..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 focus:ring-2 focus:ring-indigo-100 outline-none text-sm">
        </div>
        <select name="estado" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-iris-500">
            <option value="">Todos los estados</option>
            @foreach ($estados as $e)<option value="{{ $e }}" @selected($estado===$e)>{{ ucfirst($e) }}</option>@endforeach
        </select>
    </form>
    <a href="{{ route('admin.empresas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-iris-600 text-white font-semibold hover:bg-iris-700 shadow-lg shadow-indigo-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nueva Empresa
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">EMPRESA</th>
                    <th class="px-6 py-4">PLAN</th>
                    <th class="px-6 py-4 text-center">USUARIOS</th>
                    <th class="px-6 py-4">VENCIMIENTO</th>
                    <th class="px-6 py-4 text-center">ESTADO</th>
                    <th class="px-6 py-4 text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($empresas as $e)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-iris-50 text-iris-700 flex items-center justify-center font-bold">{{ mb_strtoupper(mb_substr($e->nombre,0,1)) }}</div>
                                <div><p class="font-semibold text-slate-800">{{ $e->nombre }}</p><p class="text-xs text-slate-400">{{ $e->ruc ?: 'Sin RUC' }}</p></div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ optional($e->plan)->nombre ?: 'Sin plan' }}</span></td>
                        <td class="px-6 py-4 text-center text-slate-600">{{ $e->usuarios_count }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ optional($e->fecha_vencimiento)->format('d/m/Y') ?: '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                @if($e->estado==='activa') bg-emerald-100 text-emerald-700
                                @elseif($e->estado==='suspendida') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">{{ $e->estadoLabel() }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.empresas.show', $e) }}" class="p-2 rounded-lg text-slate-400 hover:bg-iris-50 hover:text-iris-600" title="Ver"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                <a href="{{ route('admin.empresas.edit', $e) }}" class="p-2 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600" title="Editar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/></svg></a>
                                @if($e->estado !== 'suspendida')
                                    <form method="POST" action="{{ route('admin.empresas.estado', [$e, 'suspendida']) }}" onsubmit="return confirm('Suspender esta empresa? Sus usuarios no podran ingresar.')">@csrf @method('PATCH')
                                        <button class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600" title="Suspender"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6M14 9v6M5 5h14l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 5zM8 5V3h8v2"/></svg></button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.empresas.estado', [$e, 'activa']) }}">@csrf @method('PATCH')
                                        <button class="p-2 rounded-lg text-slate-400 hover:bg-emerald-50 hover:text-emerald-600" title="Activar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14"/></svg></div>
                        <p class="font-bold text-slate-600">Sin empresas</p>
                        <p class="text-sm text-slate-400">Registra la primera clinica de la plataforma.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($empresas->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $empresas->links() }}</div>@endif
</div>
@endsection
