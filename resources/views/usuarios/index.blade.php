@extends('layouts.app')

@section('title', 'Usuarios')
@section('subtitle', 'Personal y accesos al sistema')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex-1 max-w-md">
        <div class="relative">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Buscar por nombre o correo..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm">
        </div>
    </form>
    <a href="{{ route('usuarios.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nuevo Usuario
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">USUARIO</th>
                    <th class="px-6 py-4">ROL</th>
                    <th class="px-6 py-4">CONTACTO</th>
                    <th class="px-6 py-4 text-center">ESTADO</th>
                    <th class="px-6 py-4 text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($usuarios as $u)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-xs">{{ $u->iniciales() }}</div>
                                <div><p class="font-semibold text-slate-800">{{ $u->name }}</p><p class="text-xs text-slate-400">{{ $u->email }}</p></div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold">{{ $u->rolLabel() }}</span></td>
                        <td class="px-6 py-4 text-slate-500">{{ $u->telefono ?: '—' }}<span class="block text-xs text-slate-400">{{ $u->cargo }}</span></td>
                        <td class="px-6 py-4 text-center">
                            @if ($u->activo)<span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Activo</span>
                            @else<span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">Inactivo</span>@endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('usuarios.edit', $u) }}" class="p-2 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600" title="Editar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/></svg></a>
                                <form method="POST" action="{{ route('usuarios.estado', $u) }}">@csrf @method('PATCH')
                                    <button class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600" title="Activar/Desactivar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.4 12a6.4 6.4 0 11-3-5.4M18 4v4h-4"/></svg></button>
                                </form>
                                <form method="POST" action="{{ route('usuarios.destroy', $u) }}" onsubmit="return confirm('Eliminar este usuario?')">@csrf @method('DELETE')
                                    <button class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600" title="Eliminar"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a1 1 0 01-1 1H7a1 1 0 01-1-1V7"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($usuarios->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $usuarios->links() }}</div>@endif
</div>
@endsection
