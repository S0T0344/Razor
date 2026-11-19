<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 6) & PROGRAMACIÓN EXTREMA (XP)
// SEGURIDAD ISO/IEC 25010: RATE LIMITING, SANITIZACIÓN Y CONTROL DE SESIÓN
// =========================================================================

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validación estricta de entrada (bloquea inyección de caracteres raros)
        $credentials = $request->validate([
            'email'    => 'required|email:filter|max:120',
            'password' => 'required|string|min:6|max:64',
        ]);

        // 2. Llave única por correo + IP para limitar intentos de fuerza bruta
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $segundos = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Demasiados intentos fallidos. Por seguridad, espera {$segundos} segundos antes de reintentar.",
            ])->onlyInput('email');
        }

        // 3. Verificación segura contra hash Bcrypt en MySQL
        if (Auth::attempt($credentials, false)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            $slug = Auth::user()->rol->slug;

            if ($slug === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($slug === 'barbero') {
                return redirect('/barbero/agenda');
            } else {
                return redirect('/cliente/portal');
            }
        }

        // Registrar intento fallido (bloquea por 60 segundos al llegar a 5)
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Credenciales incorrectas. Verifica tu correo y contraseña.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
