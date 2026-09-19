<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('fecha_apertura');
            $table->decimal('monto_apertura', 10, 2)->default(0);
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_cierre', 10, 2)->nullable();
            $table->string('estado')->default('abierta');
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo'); // ingreso | egreso
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->foreignId('comprobante_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
        Schema::dropIfExists('cajas');
    }
};
