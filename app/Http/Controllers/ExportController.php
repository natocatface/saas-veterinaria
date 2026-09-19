<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Mascota;
use App\Models\Producto;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function clientes(): StreamedResponse
    {
        $rows = Cliente::withCount('mascotas')->orderBy('nombre')->get()
            ->map(fn ($c) => [
                $c->nombre, $c->documento, $c->telefono, $c->email,
                $c->mascotas_count, $c->activo ? 'Activo' : 'Inactivo',
            ]);

        return $this->csv('clientes', ['Nombre', 'Documento', 'Telefono', 'Email', 'Mascotas', 'Estado'], $rows);
    }

    public function mascotas(): StreamedResponse
    {
        $rows = Mascota::with('cliente')->orderBy('nombre')->get()
            ->map(fn ($m) => [
                $m->nombre, $m->especie, $m->raza, $m->sexo,
                $m->peso, optional($m->cliente)->nombre, $m->activo ? 'Activo' : 'Inactivo',
            ]);

        return $this->csv('pacientes', ['Nombre', 'Especie', 'Raza', 'Sexo', 'Peso', 'Dueno', 'Estado'], $rows);
    }

    public function citas(): StreamedResponse
    {
        $rows = Cita::with('mascota.cliente', 'veterinario')->orderByDesc('fecha')->get()
            ->map(fn ($c) => [
                optional($c->fecha)->format('d/m/Y H:i'),
                optional($c->mascota)->nombre,
                optional(optional($c->mascota)->cliente)->nombre,
                $c->motivo, optional($c->veterinario)->name, ucfirst($c->estado),
            ]);

        return $this->csv('citas', ['Fecha', 'Mascota', 'Dueno', 'Motivo', 'Veterinario', 'Estado'], $rows);
    }

    public function productos(): StreamedResponse
    {
        $rows = Producto::orderBy('nombre')->get()
            ->map(fn ($p) => [
                $p->nombre, $p->categoria, $p->sku, $p->stock, $p->stock_minimo,
                number_format($p->precio, 2), number_format($p->costo, 2),
            ]);

        return $this->csv('inventario', ['Producto', 'Categoria', 'SKU', 'Stock', 'Stock minimo', 'Precio', 'Costo'], $rows);
    }

    public function comprobantes(): StreamedResponse
    {
        $rows = Comprobante::with('cliente')->orderByDesc('fecha')->get()
            ->map(fn ($c) => [
                $c->numeroFormateado(), $c->tipoLabel(), optional($c->cliente)->nombre,
                optional($c->fecha)->format('d/m/Y H:i'),
                number_format($c->subtotal, 2), number_format($c->igv, 2), number_format($c->total, 2),
                ucfirst($c->metodo_pago), ucfirst($c->estado),
            ]);

        return $this->csv('comprobantes', ['Numero', 'Tipo', 'Cliente', 'Fecha', 'Subtotal', 'IGV', 'Total', 'Pago', 'Estado'], $rows);
    }

    /**
     * Genera y descarga un CSV compatible con Excel (UTF-8 con BOM).
     */
    private function csv(string $nombre, array $headers, $rows): StreamedResponse
    {
        $archivo = $nombre.'_'.Carbon::now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM para que Excel reconozca UTF-8
            fputcsv($out, $headers, ';');
            foreach ($rows as $row) {
                fputcsv($out, $row, ';');
            }
            fclose($out);
        }, $archivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
