<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear la tabla de categorías
        Schema::create('categorias_cliente', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50); // Bronce, Plata, Oro
            $table->decimal('descuento_pos', 5, 2)->default(0.00);
            $table->string('beneficio_game')->nullable();
            $table->timestamps();
        });

        // 2. Insertar los niveles iniciales obligatorios de la barbería RAZOR
        DB::table('categorias_cliente')->insert([
            ['nombre' => 'Bronce', 'descuento_pos' => 0.00, 'beneficio_game' => 'Ninguno', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Plata', 'descuento_pos' => 5.00, 'beneficio_game' => '10 min extra gratis', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Oro', 'descuento_pos' => 10.00, 'beneficio_game' => '15 min extra gratis', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Modificar la tabla de usuarios existente para añadir la relación
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('categoria_id')->default(1)->after('id')->constrained('categorias_cliente');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
        
        Schema::dropIfExists('categorias_cliente');
    }
};