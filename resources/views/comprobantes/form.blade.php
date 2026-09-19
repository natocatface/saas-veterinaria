@extends('layouts.app')

@section('title', 'Nuevo Comprobante')
@section('subtitle', 'Emision de venta')

@section('content')
@php
    $productosJson = $productos->map(fn ($p) => ['id' => $p->id, 'nombre' => $p->nombre, 'precio' => (float) $p->precio, 'stock' => $p->stock])->values();
@endphp
<div class="max-w-4xl" x-data="facturador({{ (float) $config->igv_porcentaje }}, {{ $productosJson->toJson() }})">
    <a href="{{ route('comprobantes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a facturacion
    </a>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-4">
            <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('comprobantes.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tipo *</label>
                        <select name="tipo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white">
                            <option value="ticket">Ticket</option>
                            <option value="boleta">Boleta</option>
                            <option value="factura">Factura</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Cliente</label>
                        <select name="cliente_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white">
                            <option value="">Cliente varios</option>
                            @foreach ($clientes as $cl)<option value="{{ $cl->id }}">{{ $cl->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Metodo de pago *</label>
                        <select name="metodo_pago" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="yape">Yape / Plin</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Notas</label>
                        <input name="notas" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-slate-800">Detalle</h3>
                    <button type="button" @click="agregar()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-brand-50 text-brand-700 text-sm font-semibold hover:bg-brand-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                        Agregar item
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, idx) in items" :key="idx">
                        <div class="grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-12 sm:col-span-5">
                                <input :name="`items[${idx}][descripcion]`" x-model="item.descripcion" list="prod-list" placeholder="Descripcion / producto"
                                       @change="autocompletar(item)" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-brand-500 outline-none text-sm">
                                <input type="hidden" :name="`items[${idx}][producto_id]`" x-model="item.producto_id">
                            </div>
                            <div class="col-span-4 sm:col-span-2">
                                <input :name="`items[${idx}][cantidad]`" x-model.number="item.cantidad" type="number" step="0.01" min="0.01" placeholder="Cant." class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-brand-500 outline-none text-sm text-center">
                            </div>
                            <div class="col-span-4 sm:col-span-2">
                                <input :name="`items[${idx}][precio_unitario]`" x-model.number="item.precio_unitario" type="number" step="0.01" min="0" placeholder="P. Unit." class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-brand-500 outline-none text-sm text-right">
                            </div>
                            <div class="col-span-3 sm:col-span-2 text-right text-sm font-semibold text-slate-700" x-text="'S/ ' + (item.cantidad * item.precio_unitario || 0).toFixed(2)"></div>
                            <div class="col-span-1 text-right">
                                <button type="button" @click="quitar(idx)" class="p-1.5 rounded-lg text-slate-300 hover:bg-red-50 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                            </div>
                        </div>
                    </template>
                    <p x-show="items.length === 0" class="text-sm text-slate-400 text-center py-4">Agrega productos o servicios al comprobante.</p>
                </div>
                <datalist id="prod-list">
                    <template x-for="p in productos" :key="p.id"><option :value="p.nombre"></option></template>
                </datalist>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sticky top-24">
                <h3 class="font-extrabold text-slate-800 mb-4">Resumen</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-500"><span>Subtotal</span><span x-text="'S/ ' + subtotal.toFixed(2)"></span></div>
                    <div class="flex justify-between text-slate-500"><span x-text="`IGV (${igv}%)`"></span><span x-text="'S/ ' + montoIgv.toFixed(2)"></span></div>
                    <div class="border-t border-slate-100 pt-3 flex justify-between text-lg font-extrabold text-slate-800"><span>Total</span><span x-text="'S/ ' + total.toFixed(2)"></span></div>
                </div>
                <button type="submit" class="w-full mt-5 py-3 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/25">Emitir comprobante</button>
                <a href="{{ route('comprobantes.index') }}" class="block text-center mt-2 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 text-sm">Cancelar</a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function facturador(igv, productos) {
        return {
            igv,
            productos,
            items: [{ descripcion: '', producto_id: '', cantidad: 1, precio_unitario: 0 }],
            agregar() { this.items.push({ descripcion: '', producto_id: '', cantidad: 1, precio_unitario: 0 }); },
            quitar(i) { this.items.splice(i, 1); },
            autocompletar(item) {
                const p = this.productos.find(x => x.nombre === item.descripcion);
                if (p) { item.producto_id = p.id; item.precio_unitario = p.precio; }
                else { item.producto_id = ''; }
            },
            get subtotal() { return this.items.reduce((s, i) => s + (i.cantidad * i.precio_unitario || 0), 0); },
            get montoIgv() { return this.subtotal * (this.igv / 100); },
            get total() { return this.subtotal + this.montoIgv; },
        };
    }
</script>
@endpush
@endsection
