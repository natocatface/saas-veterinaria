<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Resumenes diarios de boletas (SUNAT tipo RC). Reportan en lote las boletas
 * de una fecha. El envio es asincrono: SUNAT devuelve un ticket que luego se
 * consulta para obtener el CDR.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumenes_diarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('fecha_referencia');   // dia de las boletas resumidas
            $table->date('fecha_generacion');   // dia de envio del resumen
            $table->unsignedInteger('correlativo')->default(1);
            $table->unsignedInteger('cantidad')->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Seguimiento SUNAT (asincrono via ticket)
            $table->string('sunat_estado')->default('pendiente'); // pendiente|enviado|aceptado|rechazado
            $table->string('sunat_ticket')->nullable();
            $table->string('sunat_mensaje')->nullable();
            $table->string('sunat_hash')->nullable();
            $table->string('sunat_xml_path')->nullable();
            $table->string('sunat_cdr_path')->nullable();

            $table->timestamps();
        });

        Schema::table('comprobantes', function (Blueprint $table) {
            $table->foreignId('resumen_id')->nullable()->after('sunat_cdr_path');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->dropColumn('resumen_id');
        });

        Schema::dropIfExists('resumenes_diarios');
    }
};
