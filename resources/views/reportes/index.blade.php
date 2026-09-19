@extends('layouts.app')

@section('title', 'Reportes y BI')
@section('subtitle', 'Indicadores del negocio')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6 no-print">
    <p class="text-sm text-slate-400">Indicadores actualizados al {{ now()->format('d/m/Y H:i') }}</p>
    <div class="flex items-center gap-2">
        <a href="{{ route('reportes.facturacion') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5M9 13h6M9 17h6"/></svg>
            Facturacion Electronica
        </a>
        <a href="{{ route('export.comprobantes') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
            Exportar ventas (Excel)
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V4h12v5M6 18H4a1 1 0 01-1-1v-5a1 1 0 011-1h16a1 1 0 011 1v5a1 1 0 01-1 1h-2M6 14h12v6H6z"/></svg>
            Imprimir / PDF
        </button>
    </div>
</div>
<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-white/80">INGRESOS DEL MES</p>
        <p class="text-3xl font-extrabold mt-2">S/ {{ number_format($ingresosMes, 2) }}</p>
        <p class="text-sm text-white/80 mt-1">{{ $ventasMes }} comprobantes</p>
    </div>
    <div class="bg-gradient-to-br from-cyan-500 to-brand-600 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-white/80">TICKET PROMEDIO</p>
        <p class="text-3xl font-extrabold mt-2">S/ {{ number_format($ticketPromedio, 2) }}</p>
        <p class="text-sm text-white/80 mt-1">por venta</p>
    </div>
    <div class="bg-gradient-to-br from-violet-500 to-purple-600 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-white/80">PACIENTES</p>
        <p class="text-3xl font-extrabold mt-2">{{ $totalPacientes }}</p>
        <p class="text-sm text-white/80 mt-1">activos</p>
    </div>
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-white/80">CLIENTES</p>
        <p class="text-3xl font-extrabold mt-2">{{ $totalClientes }}</p>
        <p class="text-sm text-white/80 mt-1">registrados</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Ingresos 6 meses -->
    <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-800">Ingresos por mes</h2>
        <p class="text-sm text-slate-400 mb-4">Ultimos 6 meses</p>
        <div class="h-72"><canvas id="chartIngresos"></canvas></div>
    </div>

    <!-- Metodo de pago -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-800">Por metodo de pago</h2>
        <p class="text-sm text-slate-400 mb-4">Distribucion de ingresos</p>
        <div class="h-72"><canvas id="chartMetodo"></canvas></div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
    <!-- Top productos -->
    <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100"><h2 class="text-lg font-extrabold text-slate-800">Productos y servicios mas vendidos</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-3">DESCRIPCION</th><th class="px-6 py-3 text-center">UNIDADES</th><th class="px-6 py-3 text-right">TOTAL</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($topProductos as $p)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 font-semibold text-slate-700">{{ $p->descripcion }}</td>
                            <td class="px-6 py-3 text-center text-slate-600">{{ rtrim(rtrim(number_format($p->unidades, 2), '0'), '.') }}</td>
                            <td class="px-6 py-3 text-right font-bold text-slate-800">S/ {{ number_format($p->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-10 text-center text-sm text-slate-400">Aun no hay ventas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top clientes -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <h2 class="text-lg font-extrabold text-slate-800 mb-4">Top clientes</h2>
        @forelse ($topClientes as $i => $tc)
            <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-sm">{{ $i + 1 }}</div>
                <div class="flex-1 min-w-0"><p class="font-semibold text-slate-700 truncate">{{ optional($tc->cliente)->nombre ?: 'Cliente' }}</p><p class="text-xs text-slate-400">{{ $tc->compras }} compras</p></div>
                <p class="font-bold text-slate-800 text-sm">S/ {{ number_format($tc->total, 0) }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-400 py-6 text-center">Sin datos de facturacion.</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-800">Citas por estado</h2>
        <p class="text-sm text-slate-400 mb-4">Distribucion de la agenda</p>
        <div class="h-64"><canvas id="chartCitas"></canvas></div>
    </div>
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-800">Pacientes por especie</h2>
        <p class="text-sm text-slate-400 mb-4">Composicion del padron</p>
        <div class="h-64"><canvas id="chartEspecies"></canvas></div>
    </div>
</div>

@push('scripts')
<script>
    const brand = ['#06b6d4','#7c3aed','#10b981','#f59e0b','#ef4444','#6366f1','#ec4899','#14b8a6'];

    new Chart(document.getElementById('chartIngresos'), {
        type: 'bar',
        data: { labels: @json($mesesLabels), datasets: [{ label: 'Ingresos S/', data: @json($mesesData), backgroundColor: '#06b6d4', borderRadius: 8 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
    });

    const metodo = @json($porMetodo);
    new Chart(document.getElementById('chartMetodo'), {
        type: 'doughnut',
        data: { labels: Object.keys(metodo), datasets: [{ data: Object.values(metodo), backgroundColor: brand, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } } } }
    });

    const citas = @json($citasEstado);
    new Chart(document.getElementById('chartCitas'), {
        type: 'bar',
        data: { labels: Object.keys(citas).map(x => x.charAt(0).toUpperCase()+x.slice(1)), datasets: [{ data: Object.values(citas), backgroundColor: '#7c3aed', borderRadius: 8 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
    });

    const especies = @json($especies);
    new Chart(document.getElementById('chartEspecies'), {
        type: 'pie',
        data: { labels: Object.keys(especies), datasets: [{ data: Object.values(especies), backgroundColor: brand, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } } } }
    });
</script>
@endpush
@endsection
