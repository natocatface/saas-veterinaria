<?php

namespace App\Console\Commands;

use App\Models\ComunicacionBaja;
use App\Models\FacturacionConfig;
use App\Models\ResumenDiario;
use App\Services\Facturacion\FacturacionManager;
use Illuminate\Console\Command;

/**
 * Consulta automaticamente el estado de los envios asincronos (resumenes RC y
 * comunicaciones de baja RA) que quedaron con ticket pendiente en SUNAT.
 * Al aceptarse, el Manager aplica el efecto (marca boletas / anula factura).
 *
 * Pensado para ejecutarse cada pocos minutos desde el scheduler.
 */
class SincronizarTicketsSunat extends Command
{
    protected $signature = 'facturacion:sync-tickets';

    protected $description = 'Consulta en SUNAT los tickets pendientes de resumenes (RC) y bajas (RA)';

    public function handle(): int
    {
        $configs = FacturacionConfig::withoutGlobalScope('empresa')
            ->where('habilitada', true)
            ->where('driver', 'greenter')
            ->get()
            ->keyBy('empresa_id');

        if ($configs->isEmpty()) {
            $this->info('No hay empresas con facturacion Greenter habilitada.');

            return self::SUCCESS;
        }

        $procesados = 0;
        $procesados += $this->procesarResumenes($configs);
        $procesados += $this->procesarBajas($configs);

        $this->info($procesados.' documento(s) consultado(s) en SUNAT.');

        return self::SUCCESS;
    }

    private function procesarResumenes($configs): int
    {
        $pendientes = ResumenDiario::withoutGlobalScope('empresa')
            ->whereIn('sunat_estado', ['enviado', 'pendiente'])
            ->whereNotNull('sunat_ticket')
            ->get();

        $n = 0;
        foreach ($pendientes as $resumen) {
            $config = $configs->get($resumen->empresa_id);
            if (! $config) {
                continue;
            }
            $resultado = (new FacturacionManager($config))->consultarResumen($resumen);
            $this->line("RC {$resumen->identificador()}: {$resultado->estado} - {$resultado->mensaje}");
            $n++;
        }

        return $n;
    }

    private function procesarBajas($configs): int
    {
        $pendientes = ComunicacionBaja::withoutGlobalScope('empresa')
            ->whereIn('sunat_estado', ['enviado', 'pendiente'])
            ->whereNotNull('sunat_ticket')
            ->get();

        $n = 0;
        foreach ($pendientes as $baja) {
            $config = $configs->get($baja->empresa_id);
            if (! $config) {
                continue;
            }
            $resultado = (new FacturacionManager($config))->consultarBaja($baja);
            $this->line("RA {$baja->identificador()}: {$resultado->estado} - {$resultado->mensaje}");
            $n++;
        }

        return $n;
    }
}
