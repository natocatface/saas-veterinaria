@extends('layouts.app')

@section('title', $cliente->exists ? 'Editar Cliente' : 'Nuevo Cliente')
@section('subtitle', 'Datos del dueno de la mascota')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('clientes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a clientes
    </a>

    <form method="POST" action="{{ $cliente->exists ? route('clientes.update', $cliente) : route('clientes.store') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($cliente->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre completo *</label>
                <input name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Documento (DNI/RUC)</label>
                <input name="documento" value="{{ old('documento', $cliente->documento) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Telefono</label>
                <input name="telefono" value="{{ old('telefono', $cliente->telefono) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo electronico</label>
                <input name="email" type="email" value="{{ old('email', $cliente->email) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Direccion</label>
                <input name="direccion" value="{{ old('direccion', $cliente->direccion) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Notas</label>
                <textarea name="notas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">{{ old('notas', $cliente->notas) }}</textarea>
            </div>
            <div class="md:col-span-2 border-t border-slate-100 pt-5" x-data="{ portal: {{ old('acceso_portal', $cliente->acceso_portal ?? false) ? 'true' : 'false' }} }">
                <label class="flex items-center gap-2.5 text-sm text-slate-600 select-none">
                    <input type="checkbox" name="acceso_portal" value="1" x-model="portal" @checked(old('acceso_portal', $cliente->acceso_portal ?? false)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-5 h-5">
                    Habilitar acceso al <strong>portal del cliente</strong> (para que vea sus mascotas, citas y vacunas)
                </label>
                <div x-show="portal" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Contrasena del portal @if($cliente->exists)<span class="text-slate-400 font-normal">(dejar en blanco para no cambiar)</span>@endif</label>
                        <input name="password" type="password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Confirmar contrasena</label>
                        <input name="password_confirmation" type="password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                    </div>
                </div>
                <p x-show="portal" x-cloak class="text-xs text-slate-400 mt-2">El cliente ingresa en <span class="font-mono">/portal/login</span> con su correo y esta contrasena.</p>
            </div>

            <label class="md:col-span-2 flex items-center gap-2.5 text-sm text-slate-600 select-none">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $cliente->exists ? $cliente->activo : true)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-5 h-5">
                Cliente activo
            </label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">{{ $cliente->exists ? 'Guardar cambios' : 'Registrar cliente' }}</button>
            <a href="{{ route('clientes.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
