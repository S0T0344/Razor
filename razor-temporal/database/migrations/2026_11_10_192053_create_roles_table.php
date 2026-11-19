<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 / INTEGRACIÓN CORE RBAC: Matriz de Control de Acceso por Roles
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Crea la tabla física 'roles' en MySQL para registrar 
     * jerárquicamente las entidades de acceso base del sistema.
     * 
     * ENFOQUE CUMPLIMIENTO ISO/IEC 25010: Define el campo 'slug' como un índice 
     * único estandarizado para la validación de seguridad a nivel de Middlewares, 
     * garantizando mantenibilidad y alta seguridad en el control de accesos.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50); // Ej: 'Administrador / Gerente', 'Barbero (Panel Operativo)'
            $table->string('slug', 30)->unique(); // Código único para código (ej: 'admin', 'barbero', 'cliente')
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
