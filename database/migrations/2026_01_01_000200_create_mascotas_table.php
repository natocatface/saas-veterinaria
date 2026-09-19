<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('especie')->default('Perro');
            $table->string('raza')->nullable();
            $table->string('sexo')->nullable();
            $table->string('color')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->decimal('peso', 6, 2)->nullable();
            $table->boolean('esterilizado')->default(false);
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
