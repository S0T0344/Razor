<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 3 & 4 / HU-08, HU-09, HU-11, HU-14 (SCRUM): Inventario y POS
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Crea las tablas 'productos' y 'ventas' con integridad
     * referencial en MySQL para soportar alertas de stock bajo y ventas express
     * desde el sillón del barbero.
     */
    public function up(): void
    {
        // 1. Catálogo Logístico de Productos (HU-08 & HU-14)
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('categoria', 60); // Ej: 'Fijadores', 'Cuidado Capilar', 'Barba'
            $table->decimal('precio_venta', 10, 2); // Precio en Bs.
            $table->integer('stock_actual');
            $table->integer('stock_minimo')->default(10); // Umbral para alerta roja
            $table->timestamps();
        });

        // 2. Registro de Ventas Express y POS (HU-09 & HU-11)
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('barbero_id')->nullable()->constrained('barberos')->onDelete('set null');
            $table->foreignId('cita_id')->nullable()->constrained('citas')->onDelete('set null');
            $table->integer('cantidad');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('productos');
    }
};
