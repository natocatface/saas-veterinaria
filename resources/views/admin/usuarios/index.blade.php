@extends('layouts.admin')

@section('title', 'Usuarios')
@section('subtitle', 'Todos los usuarios de la plataforma')

@section('content')
<form method="GET" class="max-w-md mb-6">
    <div class="relative">
        <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
        <input name="q" value="{{ $q }}" placeholder="Buscar por nombre o correo..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none text-sm">
    </div>
</form>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">USUARIO</th>
                    <th class="px-6 py-4">EMPRESA</th>
                    <th class="px-6 py-4">ROL</th>
                    <th class="px-6 py-4 text-center">ESTADO</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($usuarios as $u)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-iris-600 text-white flex items-center justify-center font-bold text-xs">{{ $u->iniciales() }}</div>
                                <div><p class="font-semibold text-slate-800">{{ $u->name }}</p><p class="text-xs text-slate-400">{{ $u->email }}</p></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($u->empresa)->nombre ?: '—' }}</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $u->rolLabel() }}</span></td>
                        <td class="px-6 py-4 text-center">
                            @if($u->activo)<span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Activo</span>
                            @else<span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">Inactivo</span>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-16 text-center text-sm text-slate-400">Sin usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($usuarios->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $usuarios->links() }}</div>@endif
</div>
@endsection
