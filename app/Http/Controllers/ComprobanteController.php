<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Configuracion;
use App\Models\FacturacionConfig;
use App\Models\Producto;
use App\Services\Facturacion\FacturacionManager;
use App\Services\Facturacion\NumeroALetras;
use App\Services\Facturacion\QrGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComprobanteController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->get('tipo');
        $estado = $request->get('estado');
        $fecha = $request->get('fecha');

        $comprobantes = Comprobante::query()
            ->with('cliente', 'usuario')
            ->when($tipo, fn ($q) => $q->where('tipo', $tipo))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($fecha, fn ($q) => $q->whereDate('fecha', $fecha))
            ->orderByDesc('fecha')
            ->paginate(12)
            ->withQueryString();

        $inicioMes = Carbon::now()->startOfMonth();
        $resumen = [
            'mes' => (float) Comprobante::where('estado', 'emitido')->where('fecha', '>=', $inicioMes)->sum('total'),
            'hoy' => (float) Comprobante::where('estado', 'emitido')->whereDate('fecha', Carbon::today())->sum('total'),
            'cantidad' => Comprobante::where('estado', 'emitido')->where('fecha', '>=', $inicioMes)->count(),
        ];

        return view('comprobantes.index', compact('comprobantes', 'tipo', 'estado', 'fecha', 'resumen'));
    }

    public function create()
    {
        return view('comprobantes.form', [
            'clientes' => Cliente::where('activo', true)->orderBy('nombre')->get(),
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(),
            'config' => Configuracion::actual(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'tipo' => ['required', 'in:ticket,boleta,factura'],
            'metodo_pago' => ['required', 'string', 'max:30'],
            'notas' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.descripcion' => ['required', 'string', 'max:200'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0.01'],
            'items.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'items.*.producto_id' => ['nullable', 'exists:productos,id'],
        ], [
            'items.required' => 'Agrega al menos un item al comprobante.',
        ]);

        $config = Configuracion::actual();

        $comprobante = DB::transaction(function () use ($data, $config, $request) {
            $subtotal = 0;
            foreach ($data['items'] as $it) {
                $subtotal += round($it['cantidad'] * $it['precio_unitario'], 2);
            }
            $igv = round($subtotal * ((float) $config->igv_porcentaje / 100), 2);
            $total = round($subtotal + $igv, 2);

            $serie = match ($data['tipo']) {
                'boleta' => $config->serie_boleta,
                'factura' => $config->serie_factura,
                default => 'T001',
            };
            $numero = (int) Comprobante::where('serie', $serie)->max('numero') + 1;

            $comprobante = Comprobante::create([
                'cliente_id' => $data['cliente_id'] ?? null,
                'user_id' => $request->user()->id,
                'tipo' => $data['tipo'],
                'serie' => $serie,
                'numero' => $numero,
                'fecha' => Carbon::now(),
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
                'metodo_pago' => $data['metodo_pago'],
                'estado' => 'emitido',
                'notas' => $data['notas'] ?? null,
            ]);

            foreach ($data['items'] as $it) {
                $comprobante->items()->create([
                    'producto_id' => $it['producto_id'] ?? null,
                    'descripcion' => $it['descripcion'],
                    'cantidad' => $it['cantidad'],
                    'precio_unitario' => $it['precio_unitario'],
                    'importe' => round($it['cantidad'] * $it['precio_unitario'], 2),
                ]);

                if (! empty($it['producto_id'])) {
                    Producto::where('id', $it['producto_id'])->decrement('stock', (int) ceil($it['cantidad']));
                }
            }

            // Registrar ingreso en caja abierta
            $caja = Caja::where('estado', 'abierta')->first();
            if ($caja) {
                $caja->movimientos()->create([
                    'user_id' => $request->user()->id,
                    'tipo' => 'ingreso',
                    'concepto' => 'Venta '.$comprobante->numeroFormateado(),
                    'monto' => $total,
                    'comprobante_id' => $comprobante->id,
                ]);
            }

            return $comprobante;
        });

        // Emision electronica automatica ante SUNAT (si esta habilitada)
        $mensajeFe = '';
        $fe = new \App\Services\Facturacion\FacturacionManager();
        if ($comprobante->tipo !== 'ticket' && $fe->config()->estaHabilitada() && $fe->config()->emitir_automatico) {
            $resultadoFe = $fe->emitir($comprobante);
            $mensajeFe = ' '.$resultadoFe->mensaje;
        }

        return redirect()->route('comprobantes.show', $comprobante)->with('ok', 'Comprobante emitido: '.$comprobante->numeroFormateado().$mensajeFe);
    }

    public function show(Comprobante $comprobante)
    {
        $comprobante->load('items.producto', 'cliente', 'usuario', 'notasCredito', 'comunicacionesBaja');

        return view('comprobantes.show', [
            'comprobante' => $comprobante,
            'config' => Configuracion::actual(),
        ]);
    }

    public function anular(Comprobante $comprobante)
    {
        if ($comprobante->estado === 'anulado') {
            return back()->with('error', 'El comprobante ya esta anulado.');
        }

        DB::transaction(function () use ($comprobante) {
            foreach ($comprobante->items as $item) {
                if ($item->producto_id) {
                    Producto::where('id', $item->producto_id)->increment('stock', (int) ceil($item->cantidad));
                }
            }
            $comprobante->update(['estado' => 'anulado']);
        });

        return back()->with('ok', 'Comprobante anulado y stock restituido.');
    }

    public function destroy(Comprobante $comprobante)
    {
        $comprobante->delete();

        return redirect()->route('comprobantes.index')->with('ok', 'Comprobante eliminado.');
    }

    /** Emite o reenvia el comprobante a SUNAT con el driver configurado. */
    public function emitirSunat(Comprobante $comprobante)
    {
        if ($comprobante->tipo === 'ticket') {
            return back()->with('error', 'Los tickets internos no se envian a SUNAT.');
        }
        if ($comprobante->estado === 'anulado') {
            return back()->with('error', 'No se puede emitir un comprobante anulado.');
        }

        $manager = new FacturacionManager();
        if (! $manager->config()->estaHabilitada()) {
            return back()->with('error', 'Facturacion electronica deshabilitada. Actívala en Facturacion Electronica.');
        }

        $resultado = $manager->emitir($comprobante);

        return back()->with($resultado->ok ? 'ok' : 'error', 'SUNAT: '.$resultado->mensaje);
    }

    public function descargarXml(Comprobante $comprobante)
    {
        abort_unless($comprobante->sunat_xml_path && Storage::disk('local')->exists($comprobante->sunat_xml_path), 404);

        return Storage::disk('local')->download($comprobante->sunat_xml_path, $comprobante->numeroFormateado().'.xml');
    }

    public function descargarCdr(Comprobante $comprobante)
    {
        abort_unless($comprobante->sunat_cdr_path && Storage::disk('local')->exists($comprobante->sunat_cdr_path), 404);

        return Storage::disk('local')->download($comprobante->sunat_cdr_path, 'CDR-'.$comprobante->numeroFormateado().'.zip');
    }

    /** Representacion impresa (PDF) del comprobante con QR de SUNAT. */
    public function pdf(Comprobante $comprobante)
    {
        $comprobante->load('items.producto', 'cliente', 'usuario');
        $fe = FacturacionConfig::actual();
        $config = Configuracion::actual();

        $qrTexto = $this->textoQr($comprobante, $fe);
        $qrImg = QrGenerator::dataUri($qrTexto);
        $enLetras = NumeroALetras::convertir((float) $comprobante->total, 'SOLES');

        $pdf = Pdf::loadView('comprobantes.pdf', compact(
            'comprobante', 'config', 'fe', 'qrTexto', 'qrImg', 'enLetras'
        ))->setPaper('a4');

        return $pdf->stream($comprobante->numeroFormateado().'.pdf');
    }

    /** Cadena del QR segun especificacion SUNAT. */
    private function textoQr(Comprobante $comprobante, FacturacionConfig $fe): string
    {
        $tipoDoc = $comprobante->tipo === 'factura' ? '01' : '03';
        $fecha = ($comprobante->fecha instanceof Carbon ? $comprobante->fecha : Carbon::parse($comprobante->fecha))->format('Y-m-d');

        $doc = $comprobante->cliente->documento ?? '0';
        $tipoDocCli = $comprobante->tipo === 'factura'
            ? '6'
            : (strlen((string) $doc) === 8 ? '1' : (strlen((string) $doc) === 11 ? '6' : '0'));

        return implode('|', [
            $fe->ruc,
            $tipoDoc,
            $comprobante->serie,
            $comprobante->numero,
            number_format((float) $comprobante->igv, 2, '.', ''),
            number_format((float) $comprobante->total, 2, '.', ''),
            $fecha,
            $tipoDocCli,
            $doc ?: '0',
            $comprobante->sunat_hash ?: '',
        ]);
    }
}
