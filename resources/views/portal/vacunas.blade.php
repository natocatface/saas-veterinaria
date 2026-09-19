@extends('portal.layout')
@section('title', 'Vacunas')

@section('content')
<h1 class="text-2xl font-extrabold text-slate-800 mb-6">Historial de vacunas</h1>
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                <th class="px-6 py-4">VACUNA</th><th class="px-6 py-4">MASCOTA</th><th class="px-6 py-4">APLICACION</th><th class="px-6 py-4">PROXIMA DOSIS</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($vacunas as $v)
                    @php $venc = $v->proxima_dosis && $v->proxima_dosis->isPast(); @endphp
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $v->nombre }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($v->mascota)->nombre }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $v->fecha_aplicacion->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">@if($v->proxima_dosis)<span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $venc ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">{{ $v->proxima_dosis->format('d/m/Y') }}</span>@else<span class="text-slate-300">—</span>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-16 text-center text-sm text-slate-400">Sin vacunas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($vacunas->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $vacunas->links() }}</div>@endif
</div>
@endsection
