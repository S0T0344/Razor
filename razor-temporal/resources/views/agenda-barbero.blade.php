<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Operativo del Barbero | RAZOR Barbería</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <link rel="stylesheet" href="https://cloudflare.com">
    <link rel="stylesheet" href="{{ asset('css/razor.css') }}">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-barber px-4 py-3 mb-4">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-chair text-warning"></i> RAZOR — PANEL DEL BARBERO</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark fs-6">
                <i class="fas fa-star"></i> {{ $barbero->calificacion }} ({{ $barbero->especialidad }})
            </span>
            <span class="text-white small fw-bold">{{ $usuario->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt"></i> Salir</button>
            </form>
        </div>
    </nav>

    <div class="container pb-5">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-barber p-3 border-start border-4 border-primary">
                    <div class="text-muted small fw-bold">CORTES FINALIZADOS HOY</div>
                    <h3 class="fw-bold mb-0">Bs. {{ number_format($totalServiciosFinalizados, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-barber p-3 border-start border-4 border-info">
                    <div class="text-muted small fw-bold">PRODUCTOS VENDIDOS HOY</div>
                    <h3 class="fw-bold mb-0">Bs. {{ number_format($ventasHoy->sum('total'), 2) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-barber p-3 border-start border-4 border-success">
                    <div class="text-muted small fw-bold">MI COMISIÓN DIARIA (15%)</div>
                    <h3 class="fw-bold text-success mb-0">Bs. {{ number_format($comisionDiaria, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-barber p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-calendar-day text-primary"></i> Mi Agenda del Día ({{ date('d/m/Y') }})</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Hora</th>
                                    <th>Cliente y Nivel CRM</th>
                                    <th>Servicio + Extras</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Flujo Operativo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($citasHoy as $cita)
                                    @php
                                        $totalExtras = $cita->ventas->sum('total');
                                        $totalCuenta = $cita->precio_final + $totalExtras;
                                    @endphp
                                    <tr>
                                        <td><span class="badge bg-dark">{{ substr($cita->hora, 0, 5) }}</span></td>
                                        <td>
                                            <strong>{{ $cita->cliente->name }}</strong><br>
                                            <span class="badge bg-warning text-dark">Tier {{ $cita->cliente->categoria->nombre ?? 'Bronce' }}</span>
                                        </td>
                                        <td>
                                            {{ $cita->servicio->nombre }}
                                            @if($totalExtras > 0)
                                                <div class="text-info fw-bold">+ Bs. {{ number_format($totalExtras, 2) }} (Productos)</div>
                                            @endif
                                        </td>
                                        <td class="fw-bold">Bs. {{ number_format($totalCuenta, 2) }}</td>
                                        <td>
                                            @if($cita->estado === 'en_espera')
                                                <span class="badge bg-warning text-dark">En Espera</span>
                                            @elseif($cita->estado === 'en_atencion')
                                                <span class="badge bg-primary">En Atención</span>
                                            @else
                                                <span class="badge bg-success">Finalizado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('barbero.cita.estado', $cita->id) }}" class="d-flex gap-1">
                                                @csrf
                                                @if($cita->estado === 'en_espera')
                                                    <input type="hidden" name="estado" value="en_atencion">
                                                    <button type="submit" class="btn btn-primary btn-sm">Atender</button>
                                                @elseif($cita->estado === 'en_atencion')
                                                    <input type="hidden" name="estado" value="finalizado">
                                                    <button type="submit" class="btn btn-success btn-sm">Finalizar</button>
                                                @else
                                                    <span class="text-success fw-bold"><i class="fas fa-check-double"></i> Listo</span>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No tienes citas programadas para hoy.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-barber p-4 border-top border-4 border-warning">
                    <h5 class="fw-bold mb-3"><i class="fas fa-bolt text-warning"></i> Venta Express en Sillón</h5>
                    <form method="POST" action="{{ route('barbero.venta.express') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Producto del Inventario</label>
                            <select name="producto_id" class="form-select form-select-sm" required>
                                @foreach($productosDisponibles as $prod)
                                    <option value="{{ $prod->id }}">
                                        {{ $prod->nombre }} — Bs. {{ $prod->precio_venta }} (Stock: {{ $prod->stock_actual }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control form-control-sm" value="1" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Vincular a Cita Activa (Opcional)</label>
                            <select name="cita_id" class="form-select form-select-sm">
                                <option value="">-- Venta directa sin cita --</option>
                                @foreach($citasHoy->whereIn('estado', ['en_espera', 'en_atencion']) as $cActiva)
                                    <option value="{{ $cActiva->id }}">
                                        {{ substr($cActiva->hora, 0, 5) }} — {{ $cActiva->cliente->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning fw-bold w-100">
                            <i class="fas fa-cash-register"></i> Registrar Venta y Descontar Stock
                        </button>