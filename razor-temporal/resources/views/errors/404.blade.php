<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>404 Página No Encontrada | RAZOR Barbería</title>
    <link rel="stylesheet" href="{{ asset('css/razor.css') }}">
</head>
<body class="login-page">
    <div class="card login-card" style="text-align: center;">
        <div class="brand-header">
            <h1 style="color: #f1c40f; margin: 0; font-size: 3rem;">404</h1>
            <h3 class="mb-0">RUTA NO ENCONTRADA</h3>
        </div>
        <div class="card-body">
            <p class="text-muted" style="font-size: 0.95rem; margin-bottom: 20px;">
                La dirección solicitada no existe o ha sido reubicada dentro de la plataforma RAZOR Barbería.
            </p>
            <a href="{{ url('/login') }}" class="btn btn-primary" style="text-decoration: none; display: inline-block;">
                Ir al Inicio de Sesión
            </a>
        </div>
    </div>
</body>
</html>