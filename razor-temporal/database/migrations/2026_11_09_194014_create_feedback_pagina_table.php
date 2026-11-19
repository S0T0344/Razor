<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * METODOLOGÍA XP: Práctica de Feedback Continuo (Evaluación del Usuario)
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Crea la estructura en MySQL para almacenar las reseñas
     * de la plataforma, permitiendo auditoría cualitativa y cuantitativa.
     */
    public function up(): void
    {
        Schema::create('feedback_pagina', function (Blueprint $table) {
            $table->id();
            // Relacionamos con la tabla de usuarios (saber quién comenta)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Campo booleano: true = Me gusta, false = No me gusta
            $table->boolean('me_gusta')->default(true);
            
            // Caja de comentarios (campo de texto largo opcional)
            $table->text('comentario')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_pagina');
    }
};