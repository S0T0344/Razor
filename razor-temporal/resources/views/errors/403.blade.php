<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>403 Acceso Denegado | RAZOR Barbería</title>
    <link rel="stylesheet" href="{{ asset('css/razor.css') }}">
</head>
<body class="login-page">
    <div class="card login-card" style="text-align: center;">
        <div class="brand-header">
            <h1 style="color: #e74c3c; margin: 0; font-size: 3rem;">403</h1>
            <h3 class="mb-0">ACCESO DENEGADO (POLÍTICA RBAC)</h3>
        </div>
        <div class="card-body">
            <p class="text-muted" style="font-size: 0.95rem; margin-bottom: 20px;">
                Tu rol de usuario no cuenta con autorización de seguridad (Norma ISO/IEC 25010) para visualizar este módulo operativo.
            </p>
            <a href="{{ url('/login') }}" class="btn btn-danger" style="text-decoration: none; display: inline-block;">
                Volver a Mi Panel Autorizado
            </a>
        </div>
    </div>
</body>
</html>