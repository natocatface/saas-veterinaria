<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Configuracion de Facturacion Electronica (SUNAT - Peru, UBL 2.1).
 * Una fila por empresa (patron singleton via FacturacionConfig::actual()).
 * Tambien agrega columnas de seguimiento de emision a la tabla comprobantes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->index();

            // Estado y modo
            $table->boolean('habilitada')->default(false);
            $table->boolean('emitir_automatico')->default(true);
            $table->string('driver')->default('ninguno');   // ninguno | beta
            $table->string('entorno')->default('beta');      // beta | produccion

            // Datos del emisor
            $table->string('ruc', 15)->nullable();
            $table->string('razon_social')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('direccion_fiscal')->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();

            // Credenciales SUNAT
            $table->string('sol_usuario')->nullable();
            $table->text('sol_clave')->nullable();           // encriptado (cast 'encrypted')
            $table->string('certificado_path')->nullable();

            // Series
            $table->string('serie_boleta', 6)->default('B001');
            $table->string('serie_factura', 6)->default('F001');
            $table->string('serie_nota_credito', 6)->default('FC01');

            $table->timestamps();
        });

        Schema::table('comprobantes', function (Blueprint $table) {
            $table->string('sunat_estado')->nullable()->after('estado');   // pendiente | generado | aceptado | rechazado | anulado
            $table->string('sunat_mensaje')->nullable()->after('sunat_estado');
            $table->string('sunat_hash')->nullable()->after('sunat_mensaje');
            $table->string('sunat_ticket')->nullable()->after('sunat_hash');
            $table->string('sunat_xml_path')->nullable()->after('sunat_ticket');
            $table->string('sunat_cdr_path')->nullable()->after('sunat_xml_path');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->dropColumn([
                'sunat_estado', 'sunat_mensaje', 'sunat_hash',
                'sunat_ticket', 'sunat_xml_path', 'sunat_cdr_path',
            ]);
        });

        Schema::dropIfExists('facturacion_configs');
    }
};
