<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment('Cuidar a los animales es cuidar el mundo.');
})->purpose('Frase inspiradora');

// Suspende automaticamente las suscripciones vencidas (cada dia a las 00:15)
Schedule::command('suscripciones:verificar')->dailyAt('00:15');

// Recordatorios de citas del dia siguiente (cada dia a las 08:00)
Schedule::command('citas:recordatorio')->dailyAt('08:00');

// Consulta en SUNAT los tickets pendientes de resumenes (RC) y bajas (RA)
Schedule::command('facturacion:sync-tickets')->everyFifteenMinutes()->withoutOverlapping();
