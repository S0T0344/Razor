<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 5) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: GESTIÓN FINANCIERA Y CÁLCULO DE UTILIDAD NETA
// =========================================================================

class GastoOperativo extends Model
{
    use HasFactory;

    protected $table = 'gasto_operativos';

    protected $fillable = [
        'user_id',
        'concepto',
        'categoria',
        'monto',
        'fecha',
    ];

    /**
     * RELACIÓN: Un gasto operativo es registrado por un Administrador (User).
     */
    public function administrador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
