<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_promocions', function (Blueprint $table) {
            $table->id();
            $table->boolean('activo')->default(true);
            $table->decimal('porcentaje_descuento', 5, 2)->default(20.00);
            $table->integer('hora_inicio')->default(10);
            $table->integer('hora_fin')->default(13);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
        });

        DB::table('configuracion_promocions')->insert([
            'activo'               => true,
            'porcentaje_descuento' => 20.00,
            'hora_inicio'          => 10,
            'hora_fin'             => 13,
            'fecha_inicio'         => date('Y-m-d'),
            'fecha_fin'            => date('Y-m-d', strtotime('+30 days')),
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_promocions');
    }
};
