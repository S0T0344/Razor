<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Seguro | RAZOR Barbería</title>
    <link rel="stylesheet" href="{{ asset('css/razor.css') }}">
</head>
<body class="login-page">
    <div class="card login-card">
        <div class="brand-header">
            <h3 class="mb-0 fw-bold">RAZOR BARBERÍA</h3>
            <small>Plataforma de Gestión Integral & CRM</small>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" autocomplete="off">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="ejemplo@razor.com" autocomplete="off" required autofocus>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Contraseña</label>
                    <input type="password" name="password" id="campoClave" class="form-control" placeholder="••••••••" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn btn-danger w-100">
                    INGRESAR AL SISTEMA
                </button>
            </form>

            <div class="bg-light small">
                <div class="fw-bold mb-1">Cuentas RBAC Sembradas (Clave: <code>12345678</code>)</div>
                <ul style="margin: 5px 0 0 18px; padding: 0;">
                    <li><strong>Cliente Oro:</strong> cliente@razor.com</li>
                    <li><strong>Barbero:</strong> carlos@razor.com</li>
                    <li><strong>Administrador:</strong> admin@razor.com</li>
                </ul>
            </div>

            <div class="text-muted" style="font-size: 0.75rem; margin-top: 14px; text-align: center;">
                Política ISO/IEC 25010: Este portal utiliza cookies técnicas de sesión protegidas (HttpOnly / SameSite) y no almacena contraseñas en el navegador.
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('pageshow', function () {
            document.getElementById('campoClave').value = '';
        });
    </script>
</body>
</html>