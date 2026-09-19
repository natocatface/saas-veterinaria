@extends('layouts.admin')

@section('title', $empresa->exists ? 'Editar Empresa' : 'Nueva Empresa')
@section('subtitle', 'Datos de la clinica y suscripcion')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.empresas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-iris-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a empresas
    </a>

    <form method="POST" action="{{ $empresa->exists ? route('admin.empresas.update', $empresa) : route('admin.empresas.store') }}" class="space-y-6">
        @csrf
        @if ($empresa->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
            <h3 class="font-extrabold text-slate-800 mb-4">Datos de la empresa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre de la clinica *</label>
                    <input name="nombre" value="{{ old('nombre', $empresa->nombre) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">RUC</label>
                    <input name="ruc" value="{{ old('ruc', $empresa->ruc) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Telefono</label>
                    <input name="telefono" value="{{ old('telefono', $empresa->telefono) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo</label>
                    <input name="email" type="email" value="{{ old('email', $empresa->email) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Direccion</label>
                    <input name="direccion" value="{{ old('direccion', $empresa->direccion) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
            <h3 class="font-extrabold text-slate-800 mb-4">Suscripcion</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Plan</label>
                    <select name="plan_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none bg-white">
                        <option value="">Sin plan</option>
                        @foreach ($planes as $p)<option value="{{ $p->id }}" @selected(old('plan_id', $empresa->plan_id) == $p->id)>{{ $p->nombre }} — S/ {{ number_format($p->precio,2) }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Estado *</label>
                    <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none bg-white">
                        @foreach ($estados as $e)<option value="{{ $e }}" @selected(old('estado', $empresa->estado) === $e)>{{ ucfirst($e) }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Vence</label>
                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', optional($empresa->fecha_vencimiento)->format('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                </div>
            </div>
        </div>

        @unless ($empresa->exists)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
                <h3 class="font-extrabold text-slate-800 mb-1">Usuario administrador</h3>
                <p class="text-sm text-slate-400 mb-4">Se creara la cuenta principal con la que la clinica accede al sistema.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre *</label>
                        <input name="admin_name" value="{{ old('admin_name') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo *</label>
                        <input name="admin_email" type="email" value="{{ old('admin_email') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Contrasena *</label>
                        <input name="admin_password" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Confirmar contrasena *</label>
                        <input name="admin_password_confirmation" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
                    </div>
                </div>
            </div>
        @endunless

        <div class="flex items-center gap-3">
            <button class="px-6 py-2.5 rounded-xl bg-iris-600 text-white font-semibold hover:bg-iris-700 shadow-lg shadow-indigo-500/25">{{ $empresa->exists ? 'Guardar cambios' : 'Crear empresa' }}</button>
            <a href="{{ route('admin.empresas.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
