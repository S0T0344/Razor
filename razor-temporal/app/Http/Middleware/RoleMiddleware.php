<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM & PROGRAMACIÓN EXTREMA (XP - SEGURIDAD CORE)
// CUMPLIMIENTO ISO/IEC 25010: CONTROL DE ACCESO BASADO EN ROLES (RBAC)
// =========================================================================

class RoleMiddleware
{
    /**
     * Valida que el usuario autenticado posea estrictamente el rol requerido
     * por la ruta antes de permitirle visualizar cualquier módulo.
     */
    public function handle(Request $request, Closure $next, string $roleSlug): Response
    {
        // 1. Verificar que exista una sesión activa real en el servidor
        if (!Auth::check()) {
            return redirect('/login')->withErrors([
                'email' => 'Debes iniciar sesión para acceder a esta sección.',
            ]);
        }

        $user = Auth::user();

        // 2. Validar el slug del rol guardado en MySQL contra el rol exigido por la ruta
        if (!$user->rol || $user->rol->slug !== $roleSlug) {
            abort(403, 'ACCESO DENEGADO: No tienes permisos para ingresar al módulo de ' . strtoupper($roleSlug) . '.');
        }

        return $next($request);
    }
}
