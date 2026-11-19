<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientePortalController;
use App\Http\Controllers\BarberoPanelController;
use App\Http\Controllers\AdminDashboardController;


// Redirección inicial al Login real
Route::get('/', function () {
    return redirect('/login');
});

// Rutas públicas de Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// MATRIZ RBAC (ISO/IEC 25010): RUTAS PROTEGIDAS POR ROL REAL EN SERVIDOR
// =========================================================================

// 1. Sector Exclusivo del Cliente
Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/cliente/portal', [ClientePortalController::class, 'index'])->name('cliente.portal');
    Route::post('/cliente/agendar', [ClientePortalController::class, 'agendarCita'])->name('cliente.agendar');
    Route::post('/cliente/game', [ClientePortalController::class, 'reservarGame'])->name('cliente.game');
    Route::post('/cliente/feedback', [ClientePortalController::class, 'enviarFeedback'])->name('cliente.feedback');
    Route::get('/cliente/recibo/{id}', [ClientePortalController::class, 'verRecibo'])->name('cliente.recibo');
});

// 2. Sector Exclusivo del Barbero
Route::middleware(['auth', 'role:barbero'])->group(function () {
    Route::get('/barbero/agenda', [BarberoPanelController::class, 'index'])->name('barbero.agenda');
    Route::post('/barbero/cita/{id}/estado', [BarberoPanelController::class, 'cambiarEstadoCita'])->name('barbero.cita.estado');
    Route::post('/barbero/venta-express', [BarberoPanelController::class, 'ventaExpress'])->name('barbero.venta.express');
});

// 3. Sector Exclusivo del Administrador / Gerente
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/inventario/{id}/reabastecer', [AdminDashboardController::class, 'reabastecer'])->name('admin.inventario.reabastecer');
    Route::post('/admin/estacion/{id}/estado', [AdminDashboardController::class, 'cambiarEstadoEstacion'])->name('admin.estacion.estado');
    Route::post('/admin/gasto', [AdminDashboardController::class, 'registrarGasto'])->name('admin.gasto.store');
    Route::post('/admin/promocion', [AdminDashboardController::class, 'actualizarPromocion'])->name('admin.promocion.update');
});
