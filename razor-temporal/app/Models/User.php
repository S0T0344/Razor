<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 2) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-06 (HISTORIAL UNIFICADO) & HU-07 (PUNTOS RAZOR)
// MODELO DE DATOS CORE: ENTIDAD DE USUARIOS Y FIDELIZACIÓN
// =========================================================================

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atributos asignables de forma masiva (Mass Assignment).
     * Incluye el rol de seguridad (RBAC), puntos acumulados y tier de lealtad.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'puntos_razor',
        'categoria_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * RELACIÓN 1 (Matriz RBAC): Un usuario pertenece a un Rol de acceso.
     */
    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * RELACIÓN 2 (Sprint 2 - HU-07): Un cliente pertenece a una Categoría de Lealtad
     * (Bronce 0%, Plata 5%, Oro 10%).
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriasCliente::class, 'categoria_id');
    }

    /**
     * RELACIÓN 3 (Sprint 2 - HU-06): Historial Unificado del Cliente.
     * Devuelve todas las citas del usuario sin importar qué barbero lo atendió.
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'user_id');
    }
}
