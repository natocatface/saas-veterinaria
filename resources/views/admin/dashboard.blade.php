@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Vision general de la plataforma')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    <div class="bg-gradient-to-br from-iris-500 to-iris-700 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-white/80">EMPRESAS</p>
        <p class="text-4xl font-extrabold mt-2">{{ $totalEmpresas }}</p>
        <p class="text-sm text-white/80 mt-1">{{ $activas }} activas · {{ $prueba }} en prueba</p>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-white/80">MRR ESTIMADO</p>
        <p class="text-4xl font-extrabold mt-2">S/ {{ number_format($mrr, 0) }}</p>
        <p class="text-sm text-white/80 mt-1">ingreso recurrente mensual</p>
    </div>
    <div class="bg-gradient-to-br from-cyan-500 to-blue-600 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-white/80">USUARIOS</p>
        <p class="text-4xl font-extrabold mt-2">{{ $totalUsuarios }}</p>
        <p class="text-sm text-white/80 mt-1">en toda la plataforma</p>
    </div>
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-white/80">PACIENTES</p>
        <p class="text-4xl font-extrabold mt-2">{{ $totalPacientes }}</p>
        <p class="text-sm text-white/80 mt-1">mascotas registradas</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-800">Empresas recientes</h2>
            <a href="{{ route('admin.empresas.index') }}" class="text-sm font-semibold text-iris-600 hover:text-iris-700">Ver todas</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-3">EMPRESA</th><th class="px-6 py-3">PLAN</th><th class="px-6 py-3 text-center">USUARIOS</th><th class="px-6 py-3 text-center">ESTADO</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($empresasRecientes as $e)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-6 py-3"><a href="{{ route('admin.empresas.show', $e) }}" class="font-semibold text-slate-800 hover:text-iris-600">{{ $e->nombre }}</a><p class="text-xs text-slate-400">{{ $e->ruc ?: 'Sin RUC' }}</p></td>
                            <td class="px-6 py-3 text-slate-600">{{ optional($e->plan)->nombre ?: '—' }}</td>
                            <td class="px-6 py-3 text-center text-slate-600">{{ $e->usuarios_count }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                    @if($e->estado==='activa') bg-emerald-100 text-emerald-700
                                    @elseif($e->estado==='suspendida') bg-red-100 text-red-700
                                    @else bg-amber-100 text-amber-700 @endif">{{ $e->estadoLabel() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">Aun no hay empresas registradas. <a href="{{ route('admin.empresas.create') }}" class="text-iris-600 font-semibold">Crear la primera</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-800">Empresas por plan</h2>
        <p class="text-sm text-slate-400 mb-4">Distribucion de suscripciones</p>
        <div class="h-64"><canvas id="chartPlanes"></canvas></div>
        <div class="mt-4 flex items-center justify-between text-sm">
            <span class="text-slate-400">Planes activos</span><span class="font-bold text-slate-700">{{ $totalPlanes }}</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const porPlan = @json($porPlan);
    new Chart(document.getElementById('chartPlanes'), {
        type: 'doughnut',
        data: { labels: Object.keys(porPlan), datasets: [{ data: Object.values(porPlan), backgroundColor: ['#6366f1','#06b6d4','#10b981','#f59e0b','#ef4444','#a855f7'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } } } }
    });
</script>
@endpush
@endsection
