<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 2) & PROGRAMACIÓN EXTREMA (XP)
// MODELO DE DATOS (ORM): CONTROL DE RESERVACIONES DE CONSOLAS
// =========================================================================

class ReservaGame extends Model
{
    use HasFactory;

    /**
     * ENFOQUE XP (Diseño Simple): Mapeo explícito a la tabla física.
     * Indica que este modelo controlará el flujo de transacciones de turnos.
     */
    protected $table = 'reservas_game';

    /**
     * PROCESO TÉCNICO: Atributos asignables de forma masiva.
     * Almacena las variables clave de tiempos, ID de usuario y montos finales.
     */
    protected $fillable = [
        'user_id',
        'estacion_id',
        'fecha',
        'hora_inicio',
        'minutos_rentados',
        'monto_pago',
        'estado_pago',
    ];

    /**
     * RELACIÓN RELACIONAL (Scrum - Zona Game):
     * Define que una reservación pertenece estrictamente a un cliente (User).
     * Conecta el control de turnos con los datos maestros del usuario registrado.
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function estacion()
    {
        return $this->belongsTo(EstacionGame::class, 'estacion_id');
    }
}
