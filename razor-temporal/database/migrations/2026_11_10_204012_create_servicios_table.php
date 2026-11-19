<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 / HU-01 (SCRUM): Estructura del Catálogo de Servicios
     * -------------------------------------------------------------------------
     */
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->decimal('precio', 10, 2); // Precio en Bs. para el POS
            $table->integer('duracion_minutos'); // Tiempo estimado para la agenda
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
