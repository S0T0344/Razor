<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 3) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-09 & HU-11 (VENTAS EXPRESS DESDE EL SILLÓN / POS)
// =========================================================================

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'producto_id',
        'barbero_id',
        'cita_id',
        'cantidad',
        'total',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function barbero()
    {
        return $this->belongsTo(Barbero::class, 'barbero_id');
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }
}
