<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionPromocion extends Model
{
    use HasFactory;

    protected $table = 'configuracion_promocions';

    protected $fillable = [
        'activo',
        'porcentaje_descuento',
        'hora_inicio',
        'hora_fin',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
