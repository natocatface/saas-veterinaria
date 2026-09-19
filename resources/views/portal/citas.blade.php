@extends('portal.layout')
@section('title', 'Mis Citas')

@section('content')
<h1 class="text-2xl font-extrabold text-slate-800 mb-6">Mis citas</h1>
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                <th class="px-6 py-4">FECHA</th><th class="px-6 py-4">MASCOTA</th><th class="px-6 py-4">MOTIVO</th><th class="px-6 py-4">VETERINARIO</th><th class="px-6 py-4 text-center">ESTADO</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($citas as $c)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4"><p class="font-semibold text-slate-800">{{ $c->fecha->format('d/m/Y') }}</p><p class="text-xs text-slate-400">{{ $c->fecha->format('H:i') }}</p></td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($c->mascota)->nombre }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $c->motivo ?: 'Consulta general' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($c->veterinario)->name ?: 'Por asignar' }}</td>
                        <td class="px-6 py-4 text-center"><span class="text-xs px-2.5 py-1 rounded-full font-semibold @if($c->estado==='atendida') bg-emerald-100 text-emerald-700 @elseif($c->estado==='cancelada') bg-red-100 text-red-700 @else bg-amber-100 text-amber-700 @endif">{{ ucfirst($c->estado) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center text-sm text-slate-400">No tienes citas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($citas->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $citas->links() }}</div>@endif
</div>
@endsection
