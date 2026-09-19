<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suscripcion_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('planes')->nullOnDelete();
            $table->decimal('monto', 10, 2)->default(0);
            $table->string('periodo')->default('mensual');
            $table->string('metodo')->default('transferencia');
            $table->string('referencia')->nullable();
            $table->date('fecha_pago');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado')->default('pagado');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suscripcion_pagos');
    }
};
