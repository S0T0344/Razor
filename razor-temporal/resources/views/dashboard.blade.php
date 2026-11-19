<!-- 
=========================================================================
METODOLOGÍA ÁGILE: SCRUM (BACKLOG INTEGRAL DEL SISTEMA) & XP
PROYECTO DE GRADO: PLATAFORMA INTEGRAL "RAZOR BARBERÍA"
VISTA MAESTRA COMPLETA: CLIENTE, BARBERO Y ADMINISTRADOR INTERACTIVOS
=========================================================================
-->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAZOR Barbería - Sistema de Gestión Integral</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --primary: #2c3e50; --accent: #e74c3c; --success: #2ecc71; --warning: #f1c40f; --bg: #f4f6f9; --dark: #1e272e; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--bg); margin: 0; padding: 0; color: #333; }
        
        /* Barra de Navegación e Identificación de Sesión (Control de Roles XP) */
        .navbar { background-color: var(--primary); padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .navbar-brand { font-size: 1.4rem; font-weight: bold; letter-spacing: 1px; }
        .session-info { display: flex; align-items: center; gap: 15px; }
        .role-badge { background-color: var(--accent); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        
        /* Selectores de Roles Interactivos */
        .role-selector button { background: #34495e; border: none; color: white; padding: 8px 14px; margin-left: 5px; border-radius: 4px; cursor: pointer; font-size: 0.85rem; transition: 0.3s; }
        .role-selector button.active { background: var(--accent); font-weight: bold; }
        
        /* Contenedores y Cajas del Sistema */
        .container { padding: 25px; max-width: 1400px; margin: 0 auto; }
        .role-section { display: none; }
        .role-section.active { display: block; }
        
        .section-title { border-bottom: 2px solid #bdc3c7; padding-bottom: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .section-title h2 { margin: 0; color: var(--primary); font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
        
        .kpi-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .kpi-card { background: white; padding: 18px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--primary); }
        .kpi-card.success { border-left-color: var(--success); }
        .kpi-card.warning { border-left-color: var(--warning); }
        .kpi-card.danger { border-left-color: var(--accent); }
        .kpi-data h3 { margin: 0; font-size: 1.6rem; color: var(--dark); }
        .kpi-data p { margin: 2px 0 0 0; font-size: 0.85rem; color: #7f8c8d; font-weight: 600; }
        .kpi-icon { font-size: 2rem; color: #b2bec3; }
        
        .grid-layout { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .grid-layout-wide { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 25px; }
        @media(max-width: 900px) { .grid-layout-wide { grid-template-columns: 1fr; } }
        
        .box { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-top: 3px solid var(--primary); margin-bottom: 15px; }
        .box.accent { border-top-color: var(--accent); }
        .box h3 { margin-top: 0; margin-bottom: 15px; font-size: 1.1rem; color: var(--primary); border-bottom: 1px solid #eee; padding-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        
        /* Controles de Formularios */
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: bold; margin-bottom: 5px; color: #555; }
        .form-control { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; }
        .btn { background-color: var(--primary); color: white; border: none; padding: 10px 14px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; font-size: 0.9rem; transition: 0.2s; }
        .btn:hover { opacity: 0.9; }
        .btn-success { background-color: var(--success); }
        .btn-accent { background-color: var(--accent); }
        
        /* Tablas */
        .table-responsive { overflow-x: auto; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem; }
        th { background-color: #f8f9fa; padding: 10px; font-weight: bold; color: #444; border-bottom: 2px solid #dee2e6; }
        td { padding: 10px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
        tr:hover { background-color: #fcfcfc; }
        
        /* Badges de Estado */
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; display: inline-block; }
        .badge-waiting { background-color: #ffeaa7; color: #d63031; }
        .badge-process { background-color: #74b9ff; color: #0984e3; }
        .badge-done { background-color: #55efc4; color: #00b894; }
        .badge-danger { background-color: #ff7675; color: #d63031; }
    </style>
</head>
<body>

    <!-- NAVBAR CENTRAL CON LOGIN/CONTROL DE ROLES SIMULADO -->
    <nav class="navbar">
        <div class="navbar-brand"><i class="fas fa-scissors"></i> RAZOR BARBERÍA</div>
        <div class="session-info">
            <div class="role-selector">
                <button id="btnNavCliente" class="active" onclick="activarModulo('cliente')"><i class="fas fa-user"></i> Cliente</button>
                <button id="btnNavBarbero" onclick="activarModulo('barbero')"><i class="fas fa-chair"></i> Barbero</button>
                <button id="btnNavAdmin" onclick="activarModulo('admin')"><i class="fas fa-shield-halved"></i> Administrador</button>
            </div>
            <div id="userLogged" class="role-badge">Rol: Cliente Oro</div>
        </div>
    </nav>

    <div class="container">

        <!-- =========================================================================
             MÓDULO 1: INTERFAZ DEL CLIENTE (Reservas, Tienda y Estado General)
             ========================================================================= -->
        <div id="modulo-cliente" class="role-section active">
            <div class="section-title">
                <h2><i class="fas fa-user-circle"></i> Portal del Cliente: Gustavo (Nivel Oro)</h2>
                <span class="badge badge-done" style="font-size: 0.9rem; padding: 6px 12px;"><i class="fas fa-crown"></i> Beneficio Oro: 15 min extra gratis en Zona Game</span>
            </div>

            <div class="grid-layout-wide">
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- Reserva de Citas -->
                    <div class="box">
                        <h3><i class="fas fa-calendar-plus"></i> Sistema de Reserva de Citas Anti-Choques (XP)</h3>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                            <div class="form-group">
                                <label>1. Seleccionar Barbero</label>
                                <select class="form-control"><option>Carlos Barber (Principal)</option><option>Alejandro Estilista</option></select>
                            </div>
                            <div class="form-group">
                                <label>2. Elegir Servicio</label>
                                <select class="form-control"><option>Corte de Cabello + Barba (Bs. 50)</option><option>Corte Degradado Urbano (Bs. 40)</option></select>
                            </div>
                            <div class="form-group">
                                <label>3. Fecha y Hora Disponible</label>
                                <input type="datetime-local" class="form-control" value="2026-11-10T14:30">
                            </div>
                        </div>
                        <button class="btn btn-success">Confirmar Reserva de Cita</button>
                    </div>

                    <!-- Vitrina de Tienda / Cotizador POS -->
                    <div class="box">
                        <h3><i class="fas fa-store"></i> Tienda de Productos Disponibles y Cotización</h3>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th>Producto</th><th>Precio Unitario</th><th>Stock</th><th>Cantidad a Comprar</th><th>Subtotal estimado</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>Cera Modeladora Pomada Razor</td><td>Bs. 45.00</td><td>50 unidades</td><td><input type="number" value="1" class="form-control" style="width:70px;"></td><td>Bs. 45.00</td></tr>
                                    <tr><td>Champú Anticaída Premium</td><td>Bs. 80.00</td><td>30 unidades</td><td><input type="number" value="0" class="form-control" style="width:70px;"></td><td>Bs. 0.00</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="text-align: right; margin-top: 15px; font-weight: bold; font-size: 1.1rem; color: var(--primary);">Total Estimado Compra: Bs. 45.00</div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 20px;">