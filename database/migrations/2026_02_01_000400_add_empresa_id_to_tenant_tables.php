<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tablas = [
        'clientes', 'mascotas', 'citas', 'productos', 'consultas',
        'vacunas', 'comprobantes', 'cajas', 'groomings', 'teleconsultas', 'configuraciones',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            if (! Schema::hasColumn($tabla, 'empresa_id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->foreignId('empresa_id')->nullable()->after('id')->index();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            if (Schema::hasColumn($tabla, 'empresa_id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->dropColumn('empresa_id');
                });
            }
        }
    }
};
