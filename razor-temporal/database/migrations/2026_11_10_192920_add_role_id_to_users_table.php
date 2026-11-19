<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 / MATRIZ RBAC: Enlace Estricto de Usuarios y Roles
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Modifica la tabla 'users' existente añadiendo una restricción 
     * de clave foránea ('foreignId'). Apunta a la tabla 'roles' que creamos antes.
     * 
     * ENFOQUE METODOLÓGICO (XP - Seguridad de Datos): El campo se posiciona al 
     * inicio de la tabla ('after id') y se configura con un valor restrictivo para 
     * evitar que existan usuarios en el sistema sin un rol asignado de forma legal.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->constrained('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
