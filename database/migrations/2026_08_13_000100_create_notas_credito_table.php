<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notas de credito electronicas (SUNAT tipo 07). Anulan o corrigen un
 * comprobante ya emitido (documento afectado).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_credito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->index();
            $table->foreignId('comprobante_id')->constrained('comprobantes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('serie', 6)->default('FC01');
            $table->unsignedInteger('numero')->nullable();
            $table->dateTime('fecha');
            $table->string('cod_motivo', 2)->default('01'); // Catalogo 09 SUNAT
            $table->string('motivo')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Seguimiento SUNAT
            $table->string('sunat_estado')->nullable();
            $table->string('sunat_mensaje')->nullable();
            $table->string('sunat_hash')->nullable();
            $table->string('sunat_ticket')->nullable();
            $table->string('sunat_xml_path')->nullable();
            $table->string('sunat_cdr_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_credito');
    }
};
