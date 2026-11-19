<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal del Cliente | RAZOR Barbería</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/razor.css') }}">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-razor px-4 py-3 mb-4">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-scissors text-danger"></i> RAZOR BARBERÍA</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark fs-6">
                <i class="fas fa-crown"></i> Nivel {{ $cliente->categoria->nombre ?? 'Bronce' }} ({{ $cliente->categoria->descuento_porcentaje ?? 0 }}% Desc.)
            </span>
            <span class="badge bg-info text-dark fs-6">
                <i class="fas fa-coins"></i> {{ $cliente->puntos_razor }} Puntos Razor
            </span>
            <span class="text-white small fw-bold d-none d-md-inline">{{ $cliente->name }}</span>
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

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-razor p-4 mb-4">
                    <h4 class="fw-bold mb-3 text-dark"><i class="fas fa-calendar-check text-danger"></i> Agendamiento Inteligente en 4 Pasos</h4>
                    <!-- Catálogo Visual Interactivo de Cortes y Estilos -->
                    <p class="text-muted small mb-2">Selecciona visualmente el estilo que deseas (clic en la tarjeta):</p>
                    <div class="catalogo-grid">
                        @foreach($servicios as $serv)
                            <div class="item-visual-card" onclick="seleccionarCorteVisual({{ $serv->id }}, this)">
                                <div class="item-icono-box">✂️</div>
                                <div class="item-titulo">{{ $serv->nombre }}</div>
                                <div class="item-desc">{{ $serv->descripcion ?? 'Corte profesional + perfilado y acabado limpio (' . $serv->duracion_minutos . ' min).' }}</div>
                                <span class="item-precio-tag">Bs. {{ number_format($serv->precio, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <!-- Vitrina Visual de Productos de Barbería Disponibles en Sucursal -->
                <div class="card card-razor p-4 border-top border-4 border-primary mb-4">
                    <h4 class="fw-bold mb-1">Vitrina de Productos RAZOR (Disponibles en Sucursal)</h4>
                    <p class="text-muted small mb-3">Solicita cualquiera de estos productos a tu barbero durante tu atención o en caja:</p>
                    <div class="catalogo-grid" style="margin-bottom: 0;">
                        @forelse($productos as $prod)
                            <div class="item-visual-card" style="cursor: default;">
                                <div class="item-icono-box item-icono-producto">★</div>
                                <div class="item-titulo">{{ $prod->nombre }}</div>
                                <div class="item-desc">{{ $prod->descripcion ?? 'Categoría: ' . $prod->categoria . ' | Stock: ' . $prod->stock_actual . ' und.' }}</div>
                                <span class="item-precio-tag" style="background-color: #2980b9;">Bs. {{ number_format($prod->precio_venta, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-muted small">No hay productos disponibles en vitrina en este momento.</p>
                        @endforelse
                    </div>
                </div>
                    <form method="POST" action="{{ route('cliente.agendar') }}" id="formCita">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">1. Selecciona tu Servicio</label>
                                <select name="servicio_id" id="selectServicio" class="form-select" required onchange="calcularResumen()">
                                    @foreach($servicios as $serv)
                                        <option value="{{ $serv->id }}" data-precio="{{ $serv->precio }}" data-duracion="{{ $serv->duracion_minutos }}">
                                            {{ $serv->nombre }} — Bs. {{ $serv->precio }} ({{ $serv->duracion_minutos }} min)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">2. Elige Barbero o Asignación Rápida</label>
                                <select name="barbero_id" class="form-select" required>
                                    <option value="auto">⚡ Asignación Automática (Menor tiempo de espera)</option>
                                    @foreach($barberos as $barb)
                                        <option value="{{ $barb->id }}">
                                            {{ $barb->nombre }} — ⭐ {{ $barb->calificacion }} ({{ $barb->especialidad }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">3. Fecha de Atención</label>
                                <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">4. Bloque Horario (<span class="text-success">Horas Valle -20%</span>)</label>
                                <select name="hora" id="selectHora" class="form-select" required onchange="calcularResumen()">
                                    <option value="09:00">09:00 AM — Tarifa Normal</option>
                                    <option value="10:00" class="hora-valle-tag">10:00 AM — 🌿 HORA VALLE (-20% Desc.)</option>
                                    <option value="11:00" class="hora-valle-tag">11:00 AM — 🌿 HORA VALLE (-20% Desc.)</option>
                                    <option value="12:00" class="hora-valle-tag">12:00 PM — 🌿 HORA VALLE (-20% Desc.)</option>
                                    <option value="15:00">03:00 PM — Hora Pico (Tarifa Normal)</option>
                                    <option value="16:00">04:00 PM — Hora Pico (Tarifa Normal)</option>
                                    <option value="17:00">05:00 PM — Hora Pico (Tarifa Normal)</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Método de Pago</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="pagoEfectivo" value="efectivo" checked onclick="toggleQR(false)">
                                        <label class="form-check-label" for="pagoEfectivo"><i class="fas fa-money-bill-wave text-success"></i> Efectivo en Sucursal</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="pagoQR" value="qr" onclick="toggleQR(true)">
                                        <label class="form-check-label" for="pagoQR"><i class="fas fa-qrcode text-primary"></i> Pago QR Nacional (Simulado)</label>
                                    </div>
                                </div>
                                <div id="boxQR" class="alert alert-info mt-2 d-none small">
                                    <i class="fas fa-qrcode fa-2x float-start me-3"></i>
                                    <strong>QR Nacional Generado:</strong> Escanea desde tu banca móvil boliviana (BNB / Unión / Mercantil). El comprobante se validará automáticamente al confirmar.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check bg-light p-2 rounded">
                                <input class="form-check-input" type="checkbox" name="canjear_corte_gratis" id="checkCanjeCorte" value="1" {{ $cliente->puntos_razor < 300 ? 'disabled' : '' }}>
                                <label class="form-check-label fw-bold text-primary" for="checkCanjeCorte">
                                    Canjear 300 Puntos Razor por este Corte Gratis (Tienes {{ $cliente->puntos_razor }} pts)
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger fw-bold mt-4 w-100 py-2">
                            <i class="fas fa-check"></i> CONFIRMAR RESERVA DE CITA
                        </button>
                    </form>
                </div>
                <div class="card card-razor p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-history text-primary"></i> Mis Reservas e Historial Unificado</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha / Hora</th>
                                    <th>Servicio</th>
                                    <th>Barbero</th>
                                    <th>Pago</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($misCitas as $cita)
                                    <tr>
                                        <td>{{ $cita->fecha }} <span class="badge bg-secondary">{{ substr($cita->hora, 0, 5) }}</span></td>
                                        <td>
                                            {{ $cita->servicio->nombre }}
                                            @if($cita->es_hora_valle)
                                                <span class="badge bg-success">Hora Valle</span>
                                            @endif
                                        </td>
                                        <td>{{ $cita->barbero->nombre }}</td>
                                        <td class="text-uppercase">{{ $cita->metodo_pago }}</td>
                                        <td class="fw-bold">Bs. {{ number_format($cita->precio_final, 2) }}</td>
                                        <td>
                                            @if($cita->estado === 'en_espera')
                                                <span class="badge bg-warning text-dark">En Espera</span>
                                            @elseif($cita->estado === 'en_atencion')
                                                <span class="badge bg-primary">En Atención</span>
                                            @else
                                                <span class="badge bg-success">Finalizado</span><br>
                                                <a href="{{ route('cliente.recibo', $cita->id) }}" class="btn btn-dark btn-sm" style="text-decoration: none; display: inline-block; margin-top: 4px; white-space: nowrap;">Ver Recibo</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-3">Aún no tienes citas registradas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <h6 class="fw-bold mt-4 mb-2 text-primary">Mis Turnos en Zona Gamer</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead>
                                <tr>
                                    <th>Fecha / Hora</th>
                                    <th>Estación</th>
                                    <th>Duración</th>
                                    <th>Costo / Canje</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($misReservasGame as $game)
                                    <tr>
                                        <td>{{ $game->fecha }} <span class="badge bg-secondary">{{ substr($game->hora_inicio, 0, 5) }}</span></td>
                                        <td class="fw-bold">{{ $game->estacion->nombre ?? 'Consola' }}</td>
                                        <td>{{ $game->minutos_rentados }} min</td>
                                        <td>
                                            @if($game->monto_pago == 0)
                                                <span class="badge bg-info">Canje 150 Pts</span>
                                            @else
                                                <strong>Bs. {{ number_format($game->monto_pago, 2) }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            @if($game->estado_pago === 'pagado')
                                               <span class="badge bg-success">Pagado</span>
                                            @else
                                               <span class="badge bg-warning text-dark">Pendiente en Caja</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">Aún no tienes reservas en la Zona Gamer.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-razor p-4 mb-4 border-top border-4 border-danger">
                    <h5 class="fw-bold mb-3"><i class="fas fa-receipt"></i> TU CITA (Cálculo en Vivo)</h5>
                    <ul class="list-group list-group-flush mb-3 small">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Precio Base Servicio:</span>
                            <strong id="resPrecioBase">Bs. 0.00</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Descuento Categoría ({{ $cliente->categoria->nombre ?? 'Bronce' }}):</span>
                            <span>{{ $cliente->categoria->descuento_porcentaje ?? 0 }}%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Descuento Hora Valle:</span>
                            <span id="resDescValle">0%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span class="fw-bold text-success">Descuento Aplicado (Regla Max):</span>
                            <strong class="text-success" id="resDescFinal">0%</strong>
                        </li>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold">TOTAL A PAGAR:</span>
                        <span class="fs-4 fw-bold text-danger" id="resTotalFinal">Bs. 0.00</span>
                    </div>
                </div>

                <div class="card card-razor p-4 border-top border-4 border-primary">
                    <h5 class="fw-bold mb-2"><i class="fas fa-gamepad text-primary"></i> Zona Gamer (Espera Activa)</h5>
                    <p class="small text-muted mb-3">
                        Canjea <strong>150 Puntos Razor</strong> por 30 min gratis (+{{ $cliente->categoria->minutos_extra_game ?? 0 }} min extra por tu Tier).
                    </p>
                    <form method="POST" action="{{ route('cliente.game') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Estación</label>
                            <select name="estacion_id" class="form-select form-select-sm" required>
                                @foreach($estaciones as $est)
                                    <option value="{{ $est->id }}" {{ $est->estado === 'mantenimiento' ? 'disabled' : '' }}>
                                        {{ $est->nombre }} — Bs. {{ $est->precio_hora }}/h {{ $est->estado === 'mantenimiento' ? '(MANTENIMIENTO)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Fecha de Reserva</label>
                            <input type="date" name="fecha" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Hora Exacta</label>
                            <select name="hora_inicio" class="form-select form-select-sm" required>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                                <option value="17:00">05:00 PM</option>
                                <option value="18:00">06:00 PM</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Duración Base</label>
                            <select name="duracion" class="form-select form-select-sm" required>
                                <option value="30">30 Minutos</option>
                                <option value="60">1 Hora (60 Minutos)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Modalidad de Pago</label>
                            <select name="modalidad" class="form-select form-select-sm">
                                <option value="canje_puntos">Canjear 150 Puntos Razor (30 min Gratis)</option>
                                <option value="pago_directo">Pago Directo en Sucursal</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                            Agendar Turno en Zona Gamer
                        </button>
                    </form>
                </div>
                <div class="card card-razor p-4 border-top border-4 border-success">
                    <h5 class="fw-bold mb-2">Tu Opinión Importa (Reseña)</h5>
                    <p class="small text-muted mb-3">Cuéntanos qué te pareció la atención y la plataforma web.</p>
                    <form method="POST" action="{{ route('cliente.feedback') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Calificación del Servicio</label>
                            <select name="me_gusta" class="form-select form-select-sm" required>
                                <option value="1">👍 Excelente / Me gusta</option>
                                <option value="0">👎 Puede mejorar</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Comentario o Sugerencia</label>
                            <textarea name="comentario" rows="2" class="form-control form-control-sm" placeholder="Escribe aquí tu experiencia..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                            Enviar Reseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://jsdelivr.net"></script>
    <script>
        const descCategoria = {{ $cliente->categoria->porcentaje_descuento ?? 0 }};
        const promoActiva = {{ ($promoConfig && $promoConfig->activo) ? 'true' : 'false' }};
        const promoPorcentaje = {{ $promoConfig->porcentaje_descuento ?? 0 }};
        const promoHoraInicio = {{ $promoConfig->hora_inicio ?? 10 }};
        const promoHoraFin = {{ $promoConfig->hora_fin ?? 13 }};
        const promoFechaInicio = "{{ $promoConfig->fecha_inicio ?? '' }}";
        const promoFechaFin = "{{ $promoConfig->fecha_fin ?? '' }}";

        function seleccionarCorteVisual(servicioId, tarjetaEl) {
            const selectServ = document.getElementById('selectServicio');
            selectServ.value = servicioId;
            document.querySelectorAll('.item-visual-card').forEach(el => el.classList.remove('seleccionado'));
            tarjetaEl.classList.add('seleccionado');
            calcularResumen();
        }

        function calcularResumen() {
            const selectServ = document.getElementById('selectServicio');
            const precioBase = parseFloat(selectServ.options[selectServ.selectedIndex].dataset.precio || 0);
            const horaStr = document.getElementById('selectHora').value;
            const horaInt = parseInt(horaStr.split(':'));
            const fechaSel = document.querySelector('input[name="fecha"]').value;

            const enRangoFecha = promoActiva && (!promoFechaInicio || fechaSel >= promoFechaInicio) && (!promoFechaFin || fechaSel <= promoFechaFin);
            const descValle = (enRangoFecha && horaInt >= promoHoraInicio && horaInt < promoHoraFin) ? promoPorcentaje : 0;
            const descAplicado = Math.max(descCategoria, descValle);
            const total = precioBase * (1 - (descAplicado / 100));

            document.getElementById('resPrecioBase').innerText = 'Bs. ' + precioBase.toFixed(2);
            document.getElementById('resDescValle').innerText = descValle + '%';
            document.getElementById('resDescFinal').innerText = descAplicado + '%';
            document.getElementById('resTotalFinal').innerText = 'Bs. ' + total.toFixed(2);
        }

        function toggleQR(mostrar) {
            document.getElementById('boxQR').classList.toggle('d-none', !mostrar);
        }

        calcularResumen();
    </script>
</body>
</html>