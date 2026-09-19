<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioCita;
use App\Models\Cita;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatoriosCitas extends Command
{
    protected $signature = 'citas:recordatorio';

    protected $description = 'Envia por correo los recordatorios de las citas del dia siguiente';

    public function handle(): int
    {
        $manana = Carbon::tomorrow();

        $citas = Cita::query()
            ->with('mascota.cliente', 'veterinario')
            ->whereDate('fecha', $manana)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->get();

        $enviados = 0;
        foreach ($citas as $cita) {
            $email = optional(optional($cita->mascota)->cliente)->email;
            if (! $email) {
                continue;
            }
            Mail::to($email)->send(new RecordatorioCita($cita));
            $enviados++;
        }

        $this->info("Recordatorios enviados: {$enviados} (citas del {$manana->format('d/m/Y')}).");

        return self::SUCCESS;
    }
}
