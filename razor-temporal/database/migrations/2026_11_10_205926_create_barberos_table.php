<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 / HU-02 (SCRUM): Matriz Operativa de Barberos por Especialidad
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Crea la tabla física 'barberos' en MySQL enlazándola con 
     * un campo de texto para la especialidad técnica y un campo decimal para la 
     * calificación de rendimiento (0.00 a 5.00 estrellas).
     * 
     * ENFOQUE XP (Diseño Simple): Provee las columnas exactas para que el cliente 
     * filtre el personal manualmente en el frontend por sus competencias.
     */
    public function up(): void
    {
        Schema::create('barberos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('especialidad', 100); // Ej: 'Experto en Degradados', 'Estilista de Barba Clásica'
            $table->decimal('calificacion', 3, 2)->default(5.00); // Calificación estelar para la HU-02
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barberos');
    }
};
