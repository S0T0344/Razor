<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 / HU-03, HU-04, HU-05 (SCRUM): Núcleo Operativo de Citas
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Estructura la tabla 'citas' en MySQL conectando al 
     * cliente (users), al profesional (barberos) y al servicio (servicios).
     * 
     * ENFOQUE XP (Integridad de Datos): Soporta el marcado booleano de 'Horas Valle'
     * (-20%), el método de pago (Efectivo/QR) y los estados en tiempo real del flujo.
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('barbero_id')->constrained('barberos')->onDelete('cascade');
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');

            $table->date('fecha');
            $table->time('hora');
            $table->boolean('es_hora_valle')->default(false); // HU-03: Identifica descuento del 20%

            $table->enum('metodo_pago', ['efectivo', 'qr'])->default('efectivo'); // HU-04: Pasarela simulada
            $table->decimal('precio_final', 10, 2); // Monto calculado tras aplicar regla antifraude

            // HU-05 & HU-10: Flujo de estados operativos entre Cliente y Barbero
            $table->enum('estado', ['en_espera', 'en_atencion', 'finalizado', 'cancelado'])->default('en_espera');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
