<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('precio', 10, 2)->default(0);
            $table->string('periodo')->default('mensual'); // mensual | anual
            $table->integer('max_usuarios')->default(0);   // 0 = ilimitado
            $table->integer('max_pacientes')->default(0);  // 0 = ilimitado
            $table->text('caracteristicas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};
