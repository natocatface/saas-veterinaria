@extends('layouts.app')

@section('title', 'Panel de Control')
@section('subtitle', 'Bienvenido, '.auth()->user()->name.' — hoy')

@section('content')
@php $moneda = 'S/'; $user = auth()->user(); @endphp

<!-- Acciones rapidas -->
<div class="flex flex-wrap items-center justify-end gap-3 mb-6">
    <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nueva Cita
    </a>
    <a href="{{ route('clientes.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 14a4 4 0 10-8 0M11 7a3 3 0 100 6 3 3 0 000-6zM19 8v6M22 11h-6"/></svg>
        Nuevo Cliente
    </a>
</div>

<!-- Tarjetas de metricas -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6">
    @php
        $cards = [
            ['CITAS HOY', $citasHoy, 'Activas ahora', 'Live', 'from-cyan-500 to-cyan-600'],
            ['PACIENTES', $totalPacientes, '+'.$pacientesMes.' este mes', 'Registrados', 'from-violet-500 to-purple-600'],
            ['INGRESOS MES', $moneda.' '.number_format($ingresosMes, 0), 'Ticket: '.$moneda.' 0', 'Anual', 'from-emerald-500 to-green-600'],
            ['INVENTARIO', $totalProductos, $stockBajo > 0 ? $stockBajo.' con stock bajo' : 'Stock OK', 'Productos activos', 'from-amber-500 to-orange-600'],
        ];
    @endphp
    @foreach ($cards as $c)
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $c[4] }} text-white p-5 sm:p-6 shadow-lg">
            <div class="absolute -right-6 -top-6 w-28 h-28 bg-white/10 rounded-full"></div>
            <div class="absolute right-6 bottom-4 w-16 h-16 bg-white/10 rounded-full"></div>
            <p class="text-xs font-bold tracking-widest text-white/80">{{ $c[0] }}</p>
            <p class="text-3xl sm:text-4xl font-extrabold mt-3">{{ $c[1] }}</p>
            <p class="text-sm text-white/80 mt-1">{{ $c[2] }}</p>
            <span class="inline-block mt-4 px-3 py-1 rounded-full bg-white/20 text-xs font-semibold">{{ $c[3] }}</span>
        </div>
    @endforeach
</div>

<!-- ===== 4 GRAFICOS ESTADISTICOS (responsivos) ===== -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 mb-6">

    <!-- 1. Ingresos ultimos 6 meses -->
    <div class="bg-white rounded-3xl border border-slate-100 p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-800">Ingresos por Mes</h2>
                <p class="text-xs sm:text-sm text-slate-400">Ultimos 6 meses</p>
            </div>
            <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8"/></svg></span>
        </div>
        <div class="relative w-full h-56 sm:h-64"><canvas id="chartIngresos"></canvas></div>
    </div>

    <!-- 2. Actividad de citas 7 dias -->
    <div class="bg-white rounded-3xl border border-slate-100 p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-800">Actividad de Citas</h2>
                <p class="text-xs sm:text-sm text-slate-400">Ultimos 7 dias</p>
            </div>
            <span class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 6h14a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg></span>
        </div>
        <div class="relative w-full h-56 sm:h-64"><canvas id="chartCitas"></canvas></div>
    </div>

    <!-- 3. Pacientes por especie -->
    <div class="bg-white rounded-3xl border border-slate-100 p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-800">Pacientes por Especie</h2>
                <p class="text-xs sm:text-sm text-slate-400">Composicion del padron</p>
            </div>
            <span class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></span>
        </div>
        <div class="relative w-full h-56 sm:h-64"><canvas id="chartEspecies"></canvas></div>
    </div>

    <!-- 4. Citas por estado -->
    <div class="bg-white rounded-3xl border border-slate-100 p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-800">Citas por Estado</h2>
                <p class="text-xs sm:text-sm text-slate-400">Distribucion de la agenda</p>
            </div>
            <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.05A9 9 0 1020.95 13H11V3.05z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 3.5A9 9 0 0120.5 10H14V3.5z"/></svg></span>
        </div>
        <div class="relative w-full h-56 sm:h-64"><canvas id="chartEstado"></canvas></div>
    </div>
</div>

<!-- ===== Proximas citas + resumen del usuario ===== -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 sm:gap-6">
    <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-100 p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-800">Proximas Citas</h2>
                <p class="text-sm text-slate-400">Agenda proxima</p>
            </div>
            <a href="{{ route('citas.index') }}" class="px-3 py-1.5 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold hover:bg-brand-100">Ver agenda</a>
        </div>
        @if ($proximasCitas->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-3"><svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 6h14a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg></div>
                <p class="font-bold text-slate-600">Dia despejado</p>
                <p class="text-sm text-slate-400">No hay citas programadas por ahora.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($proximasCitas as $cita)
                    <div class="flex items-center gap-4 py-3">
                        <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-sm shrink-0">{{ $cita->fecha->format('d') }}<span class="text-[10px] font-semibold ml-0.5">{{ $cita->fecha->isoFormat('MMM') }}</span></div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800 truncate">{{ $cita->mascota->nombre ?? 'Mascota' }} <span class="text-slate-400 font-normal">— {{ $cita->motivo ?? 'Consulta' }}</span></p>
                            <p class="text-xs text-slate-400 truncate">{{ optional($cita->mascota->cliente)->nombre }} · {{ optional($cita->veterinario)->name ?? 'Sin asignar' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-slate-700">{{ $cita->fecha->format('H:i') }}</p>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">{{ ucfirst($cita->estado) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-5 sm:p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center text-xl font-extrabold">{{ $user->iniciales() }}</div>
            <div class="min-w-0">
                <p class="text-lg font-extrabold text-slate-800 truncate">{{ $user->name }}</p>
                <p class="text-sm text-slate-400 truncate">{{ $user->email }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mt-6">
            <div class="rounded-2xl border border-slate-100 p-4 text-center"><p class="text-2xl font-extrabold text-slate-800">{{ $totalPacientes }}</p><p class="text-xs text-slate-400 font-semibold tracking-wide">PACIENTES</p></div>
            <div class="rounded-2xl border border-slate-100 p-4 text-center"><p class="text-2xl font-extrabold text-slate-800">{{ $citasHoy }}</p><p class="text-xs text-slate-400 font-semibold tracking-wide">CITAS HOY</p></div>
            <div class="rounded-2xl border border-slate-100 p-4 text-center"><p class="text-2xl font-extrabold text-slate-800">{{ $totalClientes }}</p><p class="text-xs text-slate-400 font-semibold tracking-wide">CLIENTES</p></div>
            <div class="rounded-2xl border border-slate-100 p-4 text-center"><p class="text-2xl font-extrabold text-slate-800">{{ $totalProductos }}</p><p class="text-xs text-slate-400 font-semibold tracking-wide">PRODUCTOS</p></div>
        </div>
    </div>
</div>

@if ($feStats['disponible'])
    <!-- ===== Facturacion electronica (estado SUNAT) ===== -->
    <div class="bg-white rounded-3xl border border-slate-100 p-5 sm:p-6 shadow-sm mt-5 sm:mt-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5M9 13h6M9 17h6"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">Facturacion Electronica</h2>
                    <p class="text-sm text-slate-400">Estado ante SUNAT · este mes
                        @if ($feStats['habilitada'])
                            <span class="ml-1 inline-flex items-center gap-1 text-emerald-600 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Habilitada</span>
                        @else
                            <span class="ml-1 inline-flex items-center gap-1 text-slate-400 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>Deshabilitada</span>
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ route('reportes.facturacion') }}" class="px-3 py-1.5 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold hover:bg-brand-100">Ver reporte</a>
        </div>
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $feStats['aceptados'] }}</p>
                <p class="text-xs text-emerald-600/80 font-semibold tracking-wide">ACEPTADOS</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50/50 p-4 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $feStats['pendientes'] }}</p>
                <p class="text-xs text-amber-600/80 font-semibold tracking-wide">PENDIENTES</p>
            </div>
            <div class="rounded-2xl border border-red-100 bg-red-50/50 p-4 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold text-red-600">{{ $feStats['rechazados'] }}</p>
                <p class="text-xs text-red-600/80 font-semibold tracking-wide">RECHAZADOS</p>
            </div>
        </div>
        @if (! $feStats['habilitada'])
            <a href="{{ route('facturacion.config.edit') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm text-brand-600 font-semibold hover:text-brand-700">
                Configurar facturacion electronica
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        @endif
    </div>
@endif

@push('scripts')
<script>
    const money = (v) => 'S/ ' + Number(v).toLocaleString('es-PE');
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';

    // 1) Ingresos por mes (area)
    (() => {
        const ctx = document.getElementById('chartIngresos');
        const g = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
        g.addColorStop(0, 'rgba(16,185,129,0.30)'); g.addColorStop(1, 'rgba(16,185,129,0)');
        new Chart(ctx, {
            type: 'line',
            data: { labels: @json($mesesLabels), datasets: [{ label: 'Ingresos', data: @json($ingresosMeses), borderColor: '#10b981', backgroundColor: g, fill: true, tension: 0.4, borderWidth: 3, pointBackgroundColor: '#10b981', pointRadius: 4, pointHoverRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => money(c.parsed.y) } } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { callback: (v) => 'S/ ' + v } }, x: { grid: { display: false } } } }
        });
    })();

    // 2) Actividad de citas 7 dias (bar)
    new Chart(document.getElementById('chartCitas'), {
        type: 'bar',
        data: { labels: @json($diasLabels), datasets: [{ label: 'Citas', data: @json($citasSerie), backgroundColor: '#06b6d4', hoverBackgroundColor: '#0891b2', borderRadius: 8, maxBarThickness: 34 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
    });

    // 3) Pacientes por especie (doughnut)
    (() => {
        const d = @json($especies);
        new Chart(document.getElementById('chartEspecies'), {
            type: 'doughnut',
            data: { labels: Object.keys(d), datasets: [{ data: Object.values(d), backgroundColor: ['#7c3aed','#06b6d4','#10b981','#f59e0b','#ef4444','#6366f1'], borderWidth: 0, hoverOffset: 8 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '64%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 14, usePointStyle: true } } } }
        });
    })();

    // 4) Citas por estado (doughnut)
    (() => {
        const d = @json($citasEstado);
        const labels = Object.keys(d).map(x => x.charAt(0).toUpperCase() + x.slice(1));
        new Chart(document.getElementById('chartEstado'), {
            type: 'doughnut',
            data: { labels, datasets: [{ data: Object.values(d), backgroundColor: ['#f59e0b','#06b6d4','#10b981','#ef4444','#6366f1'], borderWidth: 0, hoverOffset: 8 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '64%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 14, usePointStyle: true } } } }
        });
    })();
</script>
@endpush
@endsection
