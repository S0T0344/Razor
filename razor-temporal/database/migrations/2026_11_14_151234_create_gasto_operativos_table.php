<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 5 / CIERRE CAPÍTULO I (SCRUM): Control de Gastos Operativos
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Crea la tabla 'gastos_operativos' enlazada al usuario
     * administrador que registra el egreso (servicios básicos, insumos, alquiler)
     * para calcular la Utilidad Neta (Ingresos Totales - Gastos Operativos).
     */
    public function up(): void
    {
        Schema::create('gasto_operativos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('concepto', 120); // Ej: 'Pago de Energía Eléctrica', 'Compra de Navajas'
            $table->string('categoria', 60); // Ej: 'Servicios Básicos', 'Insumos', 'Mantenimiento'
            $table->decimal('monto', 10, 2); // Monto del egreso en Bs.
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto_operativos');
    }
};
