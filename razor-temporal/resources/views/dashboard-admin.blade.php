<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gerencial | RAZOR Barbería</title>
    <link rel="stylesheet" href="{{ asset('css/razor.css') }}">
    <script src="{{ asset('js/chart.min.js') }}"></script>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin px-4 py-3 mb-4">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-shield-halved text-danger"></i> RAZOR — CONSOLA GERENCIAL</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="badge bg-danger fs-6"><i class="fas fa-cloud"></i> Panel Remoto Cloud</span>
            <span class="text-white small fw-bold">{{ $admin->name }}</span>
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

        <!-- HU-15: KPIs Principales -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-admin p-3 border-start border-4 border-success">
                    <div class="text-muted small fw-bold">INGRESOS HOY (SERVICIOS)</div>
                    <h3 class="fw-bold text-success mb-0">Bs. {{ number_format($ingresosServiciosHoy, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-admin p-3 border-start border-4 border-primary">
                    <div class="text-muted small fw-bold">INGRESOS HOY (PRODUCTOS)</div>
                    <h3 class="fw-bold text-primary mb-0">Bs. {{ number_format($ingresosProductosHoy, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-admin p-3 border-start border-4 border-warning">
                    <div class="text-muted small fw-bold">RESERVAS TOTALES</div>
                    <h3 class="fw-bold mb-0">{{ $reservasTotales }} Citas</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-admin p-3 border-start border-4 border-danger">
                    <div class="text-muted small fw-bold">ALERTAS DE STOCK BAJO</div>
                    <h3 class="fw-bold text-danger mb-0">{{ $alertasStockCount }} Alertas</h3>
                </div>
            </div>
        </div>

        <!-- Cierre Capítulo I: Balance Financiero (Gastos / Utilidad Neta) y Reseñas -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card card-admin p-4 border-top border-4 border-danger h-100">
                    <h5 class="fw-bold mb-2">Balance Financiero y Gastos Operativos</h5>
                    <div class="d-flex justify-content-between bg-light p-3 rounded mb-3">
                        <div>
                            <span class="text-muted small fw-bold">GASTOS HOY</span>
                            <h5 class="text-danger mb-0">Bs. {{ number_format($gastosHoy, 2) }}</h5>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold">UTILIDAD NETA HOY</span>
                            <h5 class="text-success mb-0">Bs. {{ number_format($utilidadNetaHoy, 2) }}</h5>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.gasto.store') }}" class="row">
                        @csrf
                        <div class="col-md-6">
                            <input type="text" name="concepto" class="form-control" placeholder="Concepto (Ej: Luz, Navajas)" required>
                        </div>
                        <div class="col-md-6">
                            <select name="categoria" class="form-select" required>
                                <option value="Insumos">Insumos de Barbería</option>
                                <option value="Servicios Básicos">Servicios Básicos</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="monto" class="form-control" placeholder="Monto (Bs.)" required>
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-danger w-100">Registrar Egreso Operativo</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-admin p-4 border-top border-4 border-success h-100">
                    <h5 class="fw-bold mb-3">Auditoría de Reseñas y Satisfacción</h5>
                    <table class="table table-sm small">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Valoración</th>
                                <th>Comentario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resenasClientes as $res)
                                <tr>
                                    <td class="fw-bold">{{ $res->cliente->name ?? 'Cliente' }}</td>
                                    <td>
                                        @if($res->me_gusta)
                                            <span class="badge bg-success">Positiva</span>
                                        @else
                                            <span class="badge bg-warning text-dark">A mejorar</span>
                                        @endif
                                    </td>
                                    <td>{{ $res->comentario ?? 'Sin comentario adicional' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">Aún no hay reseñas registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Control Dinámico de Horas Valle y Promociones -->
        <div class="card card-admin p-4 border-top border-4 border-warning mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Control Dinámico de Horas Valle y Promociones</h5>
                @if($promoConfig && $promoConfig->activo)
                    <span class="badge bg-success">PROMOCIÓN ACTIVA ({{ $promoConfig->porcentaje_descuento }}% DESC.)</span>
                @else
                    <span class="badge bg-danger">PROMOCIÓN DESACTIVADA (TARIFA NORMAL)</span>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.promocion.update') }}" class="row align-items-end">
                @csrf
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px 0; font-weight: bold; cursor: pointer;">
                        <input type="checkbox" name="activo" value="1" {{ ($promoConfig && $promoConfig->activo) ? 'checked' : '' }}>
                        Habilitar
                    </label>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Descuento (%)</label>
                    <input type="number" step="1" min="5" max="80" name="porcentaje_descuento" class="form-control" value="{{ $promoConfig->porcentaje_descuento ?? 20 }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hora Inicio (24h)</label>
                    <input type="number" min="7" max="21" name="hora_inicio" class="form-control" value="{{ $promoConfig->hora_inicio ?? 10 }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hora Fin (24h)</label>
                    <input type="number" min="8" max="22" name="hora_fin" class="form-control" value="{{ $promoConfig->hora_fin ?? 13 }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $promoConfig->fecha_inicio ?? date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ $promoConfig->fecha_fin ?? date('Y-m-d') }}" required>
                </div>
                <div class="col-12" style="text-align: right; margin-top: 5px;">
                    <button type="submit" class="btn btn-warning">Guardar Regla de Horas Valle</button>
                </div>
            </form>
        </div>

        <!-- HU-16 & HU-17: Gráficos Analíticos Chart.js -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card card-admin p-4 h-100">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar text-primary"></i> Comparativa Semanal: Servicios vs. Productos (Bs.)</h6>
                    <canvas id="chartServiciosVsProductos" height="140"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-admin p-4 h-100">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-line text-danger"></i> Mapa de Flujo: Horas Pico y Horas Valle (09:00 - 18:00)</h6>
                    <canvas id="chartHorasPico" height="140"></canvas>
                </div>
            </div>
        </div>

        <!-- Ranking de Barberos y Top Productos Más Vendidos -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card card-admin p-4 h-100">
                    <h6 class="fw-bold mb-3"><i class="fas fa-medal text-warning"></i> Ranking de Barberos por Atención</h6>
                    <table class="table table-sm align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>Pos.</th>
                                <th>Barbero</th>
                                <th>Especialidad</th>
                                <th>Calificación</th>
                                <th>Completadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rankingBarberos as $idx => $b)
                                <tr>
                                    <td class="fw-bold">#{{ $idx + 1 }}</td>
                                    <td>{{ $b->nombre }}</td>
                                    <td class="text-muted">{{ $b->especialidad }}</td>
                                    <td><span class="badge bg-warning text-dark">⭐ {{ $b->calificacion }}</span></td>
                                    <td><span class="badge bg-success">{{ $b->citas_completadas }} citas</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-admin p-4 h-100">
                    <h6 class="fw-bold mb-3"><i class="fas fa-fire text-danger"></i> Productos Más Vendidos (POS)</h6>
                    <table class="table table-sm align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Unidades Vendidas</th>
                                <th>Ingreso Generado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productosMasVendidos as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->producto->nombre ?? 'Producto' }}</td>
                                    <td>{{ $item->total_unidades }} u.</td>
                                    <td class="text-success fw-bold">Bs. {{ number_format($item->total_dinero, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">Aún no se registran ventas de productos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- HU-14: Logística de Inventario y Reabastecimiento -->
        <div class="card card-admin p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-boxes-stacked text-primary"></i> Inventario Logístico y Reabastecimiento</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio Venta</th>
                            <th>Stock Actual</th>
                            <th>Stock Mínimo</th>
                            <th>Estado</th>
                            <th>Orden de Reabastecimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $prod)
                            @php $esStockBajo = $prod->stock_actual <= $prod->stock_minimo; @endphp
                            <tr class="{{ $esStockBajo ? 'table-danger' : '' }}">
                                <td class="fw-bold">{{ $prod->nombre }}</td>
                                <td>{{ $prod->categoria }}</td>
                                <td>Bs. {{ number_format($prod->precio_venta, 2) }}</td>
                                <td class="fw-bold">{{ $prod->stock_actual }} u.</td>
                                <td>{{ $prod->stock_minimo }} u.</td>
                                <td>
                                    @if($esStockBajo)
                                        <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Stock Bajo</span>
                                    @else
                                        <span class="badge bg-success">Óptimo</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.inventario.reabastecer', $prod->id) }}" class="d-flex gap-2" style="max-width: 220px;">
                                        @csrf
                                        <input type="number" name="cantidad_ingreso" value="10" min="1" class="form-control form-control-sm">
                                        <button type="submit" class="btn btn-dark btn-sm">Sumar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- HU-13: Control del Sector de Entretenimiento -->
        <div class="card card-admin p-4">
            <h5 class="fw-bold mb-3">Control de Zona de Entretenimiento (Tiempo Real)</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Estación</th>
                            <th>Tarifa / Hora</th>
                            <th>Estado Actual</th>
                            <th>Cambiar Estado Operativo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estaciones as $est)
                            <tr>
                                <td class="fw-bold">{{ $est->nombre }}</td>
                                <td>Bs. {{ number_format($est->precio_hora, 2) }}</td>
                                <td>
                                    @if($est->estado === 'disponible')
                                        <span class="badge bg-success">Disponible</span>
                                    @elseif($est->estado === 'ocupado')
                                        <span class="badge bg-primary">Ocupada</span>
                                    @else
                                        <span class="badge bg-warning text-dark">En Mantenimiento</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.estacion.estado', $est->id) }}" class="d-flex gap-2">
                                        @csrf
                                        <select name="estado" class="form-select form-select-sm">
                                            <option value="disponible" {{ $est->estado === 'disponible' ? 'selected' : '' }}>Disponible</option>
                                            <option value="ocupado" {{ $est->estado === 'ocupado' ? 'selected' : '' }}>Ocupada</option>
                                            <option value="mantenimiento" {{ $est->estado === 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary btn-sm">Aplicar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        new Chart(document.getElementById('chartServiciosVsProductos'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($fechasSemana) !!},
                datasets: [
                    { label: 'Servicios (Bs.)', data: {!! json_encode($serieServicios) !!}, backgroundColor: '#2ecc71' },
                    { label: 'Productos (Bs.)', data: {!! json_encode($serieProductos) !!}, backgroundColor: '#3498db' }
                ]
            }
        });

        new Chart(document.getElementById('chartHorasPico'), {
            type: 'line',
            data: {
                labels: {!! json_encode($bloquesHoras) !!},
                datasets: [{
                    label: 'Flujo de Citas por Bloque Horario',
                    data: {!! json_encode($flujoPorHora) !!},
                    borderColor: '#e74c3c'
                }]
            }
        });
    </script>
</body>
</html>