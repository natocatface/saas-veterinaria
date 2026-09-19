<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Comunicaciones de baja (SUNAT tipo RA). Anulan facturas ya emitidas.
 * Envio asincrono: SUNAT devuelve un ticket que luego se consulta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicaciones_baja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->index();
            $table->foreignId('comprobante_id')->constrained('comprobantes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('fecha_referencia');   // fecha de emision del documento a dar de baja
            $table->date('fecha_generacion');   // fecha de comunicacion (envio)
            $table->unsignedInteger('correlativo')->default(1);
            $table->string('motivo')->nullable();

            $table->string('sunat_estado')->default('pendiente'); // pendiente|enviado|aceptado|rechazado
            $table->string('sunat_ticket')->nullable();
            $table->string('sunat_mensaje')->nullable();
            $table->string('sunat_hash')->nullable();
            $table->string('sunat_xml_path')->nullable();
            $table->string('sunat_cdr_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicaciones_baja');
    }
};
