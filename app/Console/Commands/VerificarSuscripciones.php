<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class VerificarSuscripciones extends Command
{
    protected $signature = 'suscripciones:verificar';

    protected $description = 'Suspende las empresas cuya suscripcion haya vencido';

    public function handle(): int
    {
        $vencidas = Empresa::whereIn('estado', ['activa', 'prueba'])
            ->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<', Carbon::today())
            ->get();

        foreach ($vencidas as $empresa) {
            $empresa->update(['estado' => 'suspendida']);
            $this->line("Suspendida: {$empresa->nombre}");
        }

        $this->info($vencidas->count().' empresa(s) suspendida(s) por vencimiento.');

        return self::SUCCESS;
    }
}
