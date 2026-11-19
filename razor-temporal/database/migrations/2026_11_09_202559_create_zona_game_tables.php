<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * METODOLOGÍA SCRUM (SPRINT 2) & XP: Módulo Automatizado Zona Game
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Crea las tablas relacionales para el control físico de 
     * las estaciones de entretenimiento (consolas PS5/Xbox) y sus transacciones.
     */
    public function up(): void
    {
        // 1. Catálogo de Consolas / Estaciones de Juego de la barbería RAZOR
        Schema::create('estaciones_game', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50); // Ej: 'PlayStation 5 #1', 'Xbox Series X'
            $table->decimal('precio_hora', 10, 2);
            $table->enum('estado', ['disponible', 'ocupado', 'mantenimiento'])->default('disponible');
            $table->timestamps();
        });

        // 2. Registro de Reservaciones y Tiempos de Alquiler por Cliente
        Schema::create('reservas_game', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Cliente que alquila
            $table->foreignId('estacion_id')->constrained('estaciones_game')->onDelete('cascade'); // Consola asignada
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->integer('minutos_rentados'); // Duración del turno (Ej: 30, 60, 90 minutos)
            $table->decimal('monto_pago', 10, 2); // Precio calculado automáticamente
            $table->enum('estado_pago', ['pendiente', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas_game');
        Schema::dropIfExists('estaciones_game');
    }
};