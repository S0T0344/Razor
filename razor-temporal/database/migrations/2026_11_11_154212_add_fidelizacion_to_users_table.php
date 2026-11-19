<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 2 / HU-07 (SCRUM): Motor de Puntos Razor y Tiers de Lealtad
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Añade la columna 'puntos_razor' para acumular recompensas
     * y enlaza la clave foránea 'categoria_id' hacia la tabla 'categorias_cliente'.
     * 
     * ENFOQUE XP (Integración de Datos): Centraliza el nivel de lealtad en el
     * perfil del usuario para aplicarlo tanto en citas como en la Zona Gamer.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('puntos_razor')->default(0)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('puntos_razor');
        });
    }
};
