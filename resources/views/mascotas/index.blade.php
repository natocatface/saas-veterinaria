@extends('layouts.app')

@section('title', 'Pacientes')
@section('subtitle', 'Mascotas registradas')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[220px] max-w-md">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Buscar por mascota, raza o dueno..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm">
        </div>
        <select name="especie" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
            <option value="">Todas las especies</option>
            @foreach ($especies as $e)<option value="{{ $e }}" @selected($especie===$e)>{{ $e }}</option>@endforeach
        </select>
        <button class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold hover:bg-slate-200">Filtrar</button>
    </form>
    <a href="{{ route('export.mascotas') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg><span class="hidden sm:inline">Exportar</span></a>
                    <a href="{{ route('mascotas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nueva Mascota
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">MASCOTA</th>
                    <th class="px-6 py-4">ESPECIE / RAZA</th>
                    <th class="px-6 py-4">DUENO</th>
                    <th class="px-6 py-4 text-center">PESO</th>
                    <th class="px-6 py-4 text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($mascotas as $m)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-violet-50 text-violet-600 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $m->nombre }}</p>
                                    <p class="text-xs text-slate-400">{{ $m->sexo ?: '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500"><span class="px-2.5 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold">{{ $m->especie }}</span> <span class="text-slate-400">{{ $m->raza }}</span></td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($m->cliente)->nombre ?: '—' }}</td>
                        <td class="px-6 py-4 text-center text-slate-600">{{ $m->peso ? $m->peso.' kg' : '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('mascotas.show', $m) }}" title="Ver" class="p-2 rounded-lg text-slate-400 hover:bg-brand-50 hover:text-brand-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                <a href="{{ route('mascotas.edit', $m) }}" title="Editar" class="p-2 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/></svg></a>
                                <form method="POST" action="{{ route('mascotas.destroy', $m) }}" onsubmit="return confirm('Eliminar esta mascota?')">@csrf @method('DELETE')
                                    <button title="Eliminar" class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a1 1 0 01-1 1H7a1 1 0 01-1-1V7"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
                        <p class="font-bold text-slate-600">Sin mascotas</p>
                        <p class="text-sm text-slate-400">No se encontraron registros.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($mascotas->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $mascotas->links() }}</div>@endif
</div>
@endsection
