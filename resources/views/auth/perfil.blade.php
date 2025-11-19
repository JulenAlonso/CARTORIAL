<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">
    <title>Perfil de Usuario — AutoControl</title>

    <link rel="stylesheet" href="{{ asset('assets/style/perfil/perfil.css') }}">

    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <meta name="km-data-url-template" content="{{ url('/vehiculos/__ID__/km/data') }}">
</head>

<body>
    <aside>
        <div class="profile-pic">
            <img src="{{ $avatarPath }}" alt="Usuario">
        </div>
        <!-- Información del perfil del usuario -->
        <div class="user-info">
            <p><strong>Nombre de usuario:</strong> {{ Auth::user()->user_name }}</p>
            <p><strong>Nombre:</strong> {{ Auth::user()->nombre }}</p>
            <p><strong>Apellidos:</strong> {{ Auth::user()->apellidos }}</p>
            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p><strong>Teléfono:</strong> {{ Auth::user()->telefono }}</p>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('editarPerfil.create') }}" class="btn-sidebar">👤 Editar Perfil</a>
            <a href="{{ route('vehiculo.create') }}" class="btn-sidebar">➕ Añadir Vehículo</a>
            <a href="{{ route('editarVehiculo.create') }}" class="btn-sidebar">🛠️ Editar Vehiculo</a>
            <a href="{{ route('ayuda') }}" class="btn-sidebar">❓ Ayuda</a>
            <p></p>
            @if (auth()->user()?->admin == 1)
                <a href="{{ route('admin.dashboard') }}" class="btn-sidebar-adminzone">
                    ⚙️ Admin Zone
                </a>
            @endif
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout">Cerrar Sesión</button>
        </form>
    </aside>

    <main>
        <h1>Mi Perfil</h1>
        @php
            $vehiculos = $vehiculos ?? collect();
            $totalVehiculos = $totalVehiculos ?? ($vehiculos->count() ?? 0);
            $valorTotal = $valorTotal ?? ($vehiculos->sum('precio') ?? 0);
            $kmTotal = $kmTotal ?? ($vehiculos->sum('km') ?? 0);
            $gastosTotales = $gastosTotales ?? 0;
        @endphp

        <div class="cards">
            {{--  --}}
            {{--  --}}
            {{--  --}}
            {{--  --}}
            <!-- 🚗 Vehículos -->
            <div class="card" id="card-vehiculos-registrados" role="button" tabindex="0" aria-expanded="false"
                aria-controls="seccion-mis-vehiculos">
                <h3>🚗 Vehículos</h3>

                @if ($vehiculos->isEmpty())
                    <p class="text-muted">Aún no has registrado ningún vehículo.</p>
                @else
                    <div class="vehiculos-lista">
                        @foreach ($vehiculos as $v)
                            <div class="vehiculo-mini">
                                <div>
                                    <strong>{{ $v->marca }} {{ $v->modelo }}</strong>
                                    ({{ $v->anio_matriculacion }})
                                    <br>
                                    <span style="font-size:0.9rem;color:#666;">Matrícula: {{ $v->matricula }}</span>
                                    <p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-muted ver-detalles">Ver detalles</p>
                @endif
            </div>
            {{--  --}}
            {{--  --}}
            {{--  --}}
            {{--  --}}
            <!-- 💰 Valor  -->
            <div class="card" id="card-valor" role="button" tabindex="0" aria-controls="seccion-mis-vehiculos">
                <h3>💰 Valor</h3>

                @if ($vehiculos->isEmpty())
                    <p class="text-muted">Sin vehículos registrados.</p>
                @else
                    <div class="vehiculos-lista">
                        @foreach ($vehiculos as $v)
                            <div class="vehiculo-mini">
                                <div>
                                    <strong>{{ $v->marca }} {{ $v->modelo }}</strong><br>
                                    <span style="font-size:0.9rem;color:#666;">
                                        {{ number_format($v->precio, 2, ',', '.') }} €
                                        @if (!empty($v->precio_segunda_mano) && $v->precio_segunda_mano > 0)
                                            <br>
                                            <span style="font-size:0.85rem;color:#999;">
                                                (2ª mano: {{ number_format($v->precio_segunda_mano, 2, ',', '.') }} €)
                                            </span>
                                        @endif
                                    </span>
                                    <p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-muted ver-detalles">Ver detalles</p>
                @endif
            </div>
            {{--  --}}
            {{--  --}}
            {{--  --}}
            {{--  --}}
            <!-- 📍 Kilometraje (ÚNICO con gráfico) -->
            <div class="card" id="card-km" role="button" tabindex="0" aria-controls="seccion-mis-vehiculos">
                <h3>📍 Kilómetros</h3>

                @if ($vehiculos->isEmpty())
                    <p class="text-muted">Sin vehículos registrados.</p>
                @else
                    <div class="vehiculos-lista">
                        @foreach ($vehiculos as $v)
                            <div class="vehiculo-mini">
                                <div>
                                    <strong>{{ $v->marca }} {{ $v->modelo }}</strong><br>
                                    <span style="font-size:0.9rem;color:#666;">
                                        {{ number_format($v->km, 0, ',', '.') }} km
                                    </span>
                                    <p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-muted ver-detalles">Ver detalles</p>
                @endif
            </div>
            {{--  --}}
            {{--  --}}
            {{--  --}}
            {{--  --}}
            <!-- 🧾 Gastos -->
            <div class="card" id="card-gastos" role="button" tabindex="0" aria-controls="seccion-mis-vehiculos">
                <h3>🧾 Gastos</h3>

                @if ($vehiculos->isEmpty())
                    <p class="text-muted">Sin vehículos registrados.</p>
                @else
                    <div class="vehiculos-lista">
                        @foreach ($vehiculos as $v)
                            @php
                                $gastoCalc = $v->gastoCalc; // ← viene calculado del controlador
                            @endphp
                            <div class="vehiculo-mini">
                                <div>
                                    <strong>{{ $v->marca }} {{ $v->modelo }}</strong><br>
                                    <span style="font-size:0.9rem;color:#666;">
                                        {{ number_format($gastoCalc, 2, ',', '.') }} €
                                    </span>
                                    <p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-muted ver-detalles">Ver detalles</p>
                @endif
            </div>
            {{--  --}}
            {{--  --}}
            {{--  --}}
            {{--  --}}
            <!-- 📅 Calendario -->
            <div class="card" id="card-calendario" role="button" tabindex="0" aria-controls="panel-calendario">
                <h3>📅 Calendario</h3>

                <div class="mini-calendar small">
                    @php
                        use Carbon\Carbon;
                        $now = Carbon::now();
                        $first = $now->copy()->startOfMonth();
                        $daysInMonth = $now->daysInMonth;
                        $leading = $first->isoWeekday() - 1;
                        $sampleEvents = [5, 12, 21];
                    @endphp

                    <div class="mc-header">
                        <span class="mc-month">{{ $now->translatedFormat('F Y') }}</span>
                    </div>

                    <div class="mc-grid mc-days">
                        <div>Lu</div>
                        <div>Ma</div>
                        <div>Mi</div>
                        <div>Ju</div>
                        <div>Vi</div>
                        <div>Sa</div>
                        <div>Do</div>
                    </div>

                    <div class="mc-grid mc-dates">
                        @for ($i = 0; $i < $leading; $i++)
                            <div class="mc-date mc-empty"></div>
                        @endfor

                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $date = $first->copy()->day($d);
                                $isToday = $date->isToday();
                            @endphp
                            <div class="mc-date {{ $isToday ? 'mc-today' : '' }}">
                                <span>{{ $d }}</span>
                                @if (in_array($d, $sampleEvents))
                                    <span class="mc-dot" title="Evento"></span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección listado de vehículos (colapsable) --}}
        <section id="seccion-mis-vehiculos" class="collapsible" style="margin-top: 24px">
            <h2 class="h2_mis_vehiculos">Mis vehículos</h2>

            @if ($vehiculos->isEmpty())
                <p class="text-muted">Aún no has registrado ningún vehículo.
                    <a href="{{ route('vehiculo.create') }}">Añadir vehículo</a>.
                </p>
            @else
                <div class="vehiculos-grid">
                    @foreach ($vehiculos as $v)
                        @php
                            // Fallback robusto para car_avatar
                            if (empty($v->car_avatar)) {
                                $carSrc = asset('assets/images/default-car.png');
                            } elseif (preg_match('/^https?:\/\//', $v->car_avatar)) {
                                $carSrc = $v->car_avatar;
                            } else {
                                $carSrc = asset('storage/' . ltrim($v->car_avatar, '/'));
                            }

                            $gastoCalc = $v->gastos_total ?? $v->precio * 0.05;
                        @endphp

                        <article class="vehiculo-card">
                            <div class="vehiculo-media">
                                <img src="{{ $carSrc }}"
                                    alt="Imagen de {{ $v->marca }} {{ $v->modelo }}"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/images/default-car.png') }}';">
                            </div>

                            <div class="vehiculo-body">
                                <h3 class="vehiculo-titulo">
                                    {{ $v->marca }} {{ $v->modelo }}
                                    <span>({{ $v->anio_matriculacion }})</span>
                                </h3>

                                {{-- 🟩 VISTA COMPLETA → para tarjeta "Vehículos" --}}
                                <div class="tarjeta-detalle">
                                    <ul class="vehiculo-datos">
                                        <li><strong>Matrícula:</strong> {{ $v->matricula }}</li>
                                        <li class="km"><strong>Km:</strong>
                                            {{ number_format($v->km, 0, ',', '.') }} km</li>
                                        <li><strong>CV:</strong> {{ $v->cv }}</li>
                                        <li><strong>Combustible:</strong> {{ $v->combustible }}</li>
                                        <li><strong>Etiqueta:</strong> {{ $v->etiqueta }}</li>

                                        <li class="precio">
                                            <strong>Precio:</strong>
                                            {{ number_format($v->precio, 2, ',', '.') }} €
                                        </li>

                                        @if (!empty($v->precio_segunda_mano) && $v->precio_segunda_mano > 0)
                                            <li class="precio2">
                                                <strong>2ª mano:</strong>
                                                {{ number_format($v->precio_segunda_mano, 2, ',', '.') }} €
                                            </li>
                                        @endif

                                        <li class="gastos">
                                            <strong>Gastos:</strong>
                                            {{ number_format($gastoCalc, 2, ',', '.') }} €
                                        </li>

                                        <li>
                                            <strong>Compra:</strong>
                                            {{ \Carbon\Carbon::parse($v->fecha_compra)->format('d/m/Y') }}
                                        </li>
                                    </ul>
                                </div>
                                {{--  --}}
                                {{--  --}}
                                {{--  --}}
                                {{--  --}}
                                {{-- 🟦 VISTA KM → para tarjeta "Kilómetros" --}}
                                <div class="tarjeta-km">
                                    <ul class="vehiculo-datos">
                                        <li class="km">
                                            <strong>Kilometraje actual:</strong>
                                            {{ number_format($v->km, 0, ',', '.') }} km
                                        </li>
                                    </ul>
                                    <p></p>
                                    {{-- FORMULARIO: NUEVO REGISTRO DE KM --}}
                                    <form action="{{ route('km.store', $v->id_vehiculo) }}" method="POST"
                                        class="vehiculo-km-form mt-3">
                                        @csrf

                                        <div class="row g-3">

                                            <!-- COLUMNA 1 — FECHA -->
                                            <div class="col-md-4">
                                                <label for="fecha_{{ $v->id_vehiculo }}" class="form-label">
                                                    Fecha del registro
                                                </label>
                                                <input type="date" id="fecha_{{ $v->id_vehiculo }}"
                                                    name="fecha_registro" class="form-control form-control-sm"
                                                    value="{{ now()->format('Y-m-d') }}" required>
                                                <p></p>
                                                <label for="km_actual_{{ $v->id_vehiculo }}" class="form-label">
                                                    Kilómetros actuales
                                                </label>
                                                <input type="number" id="km_actual_{{ $v->id_vehiculo }}"
                                                    name="km_actual" class="form-control form-control-sm"
                                                    min="{{ $v->km ?? 0 }}" required
                                                    placeholder="Introduce los km actuales">
                                                <br>
                                            </div>

                                            <!-- COLUMNA 2 — KM + COMENTARIO DEBAJO -->
                                            <div class="col-md-4">
                                                <label for="comentario_{{ $v->id_vehiculo }}"
                                                    class="form-label mt-3">
                                                    Comentario (opcional)
                                                </label>
                                                <p></p>
                                                <textarea id="comentario_{{ $v->id_vehiculo }}" name="comentario" class="cometarioText" rows="2"
                                                    placeholder="Ej: viaje, revisión, trayecto diario..."></textarea>
                                            </div>

                                            <!-- COLUMNA 3 — VACÍA (TUS TABLAS VAN AQUÍ) -->
                                            <div class="col-md-4">
                                                {{-- Aquí metes tu tabla --}}
                                                <div class="km-tabla">
                                                    {{-- TABLA REGISTRO VEHÍCULOS CON SU ID --}}
                                                    @php
                                                        // Usamos la relación registrosKm si existe, y la ordenamos por fecha descendente
                                                        $registrosKm =
                                                            $v->registrosKm->sortByDesc('fecha_registro') ?? collect();
                                                    @endphp

                                                    @if ($registrosKm->isNotEmpty())
                                                        <div class="tabla-registros-km-wrapper mt-3">
                                                            <table class="tabla-registros-km">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Fecha</th>
                                                                        <th>Km</th>
                                                                        <th>Comentario</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($registrosKm as $rk)
                                                                        <tr>
                                                                            <td>{{ \Carbon\Carbon::parse($rk->fecha_registro)->format('d/m/Y') }}
                                                                            </td>
                                                                            <td>{{ number_format($rk->km_actual, 0, ',', '.') }}
                                                                                km
                                                                            </td>
                                                                            <td>{{ $rk->comentario ?: '—' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <p class="text-muted mt-2">Aún no hay registros de kilómetros
                                                            para este
                                                            vehículo.</p>
                                                    @endif
                                                </div>

                                                <p></p>
                                                {{-- BOTON GRAFICO --}}
                                                <button class="btn btn-primary btn-sm w-100 mt-3"
                                                    onclick="document.getElementById('modalKm_{{ $v->id_vehiculo }}').showModal();">
                                                    Gráficos Kilómetros
                                                </button>

                                                <dialog id="modalKm_{{ $v->id_vehiculo }}" class="chart-dialog">
                                                    <h3>📈 Kilómetros — {{ $v->marca }} {{ $v->modelo }}</h3>

                                                    <div id="chartKm_{{ $v->id_vehiculo }}"
                                                        style="height: 420px; width: 100%;"></div>

                                                    <div class="dialog-buttons">
                                                        <button
                                                            onclick="document.getElementById('modalKm_{{ $v->id_vehiculo }}').close();"
                                                            class="btn btn-secondary">
                                                            Cerrar
                                                        </button>
                                                    </div>
                                                </dialog>

                                                {{--  --}}
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm w-100 mt-3">
                                            Guardar registro de km
                                        </button>
                                    </form>
                                </div>
                                {{--  --}}
                                {{--  --}}
                                {{--  --}}
                                {{--  --}}
                                {{-- 🟥 VISTA GASTOS → para tarjeta "Gastos" --}}
                                <div class="tarjeta-gastos mt-4">
                                    <ul class="vehiculo-datos">
                                        <li class="gastos">
                                            <strong>Gastos totales:</strong>

                                            @php
                                                // Igual que arriba: gasto por vehículo
                                                $gastoCalc = $v->gastoCalc ?? 0;
                                            @endphp

                                            {{ number_format($gastoCalc, 2, ',', '.') }} €
                                        </li>
                                    </ul>

                                    {{-- FORMULARIO: NUEVO GASTO --}}
                                    <form action="{{ route('gastos.store', $v->id_vehiculo) }}" method="POST"
                                        enctype="multipart/form-data" class="vehiculo-gastos-form mt-3"> @csrf

                                        <div class="row g-3">

                                            <!-- COLUMNA 1 — FECHA GASTO -->
                                            <div class="col-md-4">
                                                <label for="fecha_gasto_{{ $v->id_vehiculo }}" class="form-label">
                                                    Fecha del gasto
                                                </label>
                                                <input type="date" id="fecha_gasto_{{ $v->id_vehiculo }}"
                                                    name="fecha_gasto" class="form-control form-control-sm"
                                                    value="{{ now()->format('Y-m-d') }}" required>
                                                <p></p>
                                                <label for="importe_{{ $v->id_vehiculo }}" class="form-label">
                                                    Importe (€)
                                                </label>
                                                <input type="number" id="importe_{{ $v->id_vehiculo }}"
                                                    name="importe" class="form-control form-control-sm"
                                                    step="0.01" min="0" placeholder="Ej: 45.90" required>
                                                <p></p>
                                                {{-- Si quieres también el tipo, puedes meterlo aquí arriba o debajo de importe --}}
                                                <label for="tipo_gasto_{{ $v->id_vehiculo }}"
                                                    class="form-label mt-3">
                                                    Tipo de gasto
                                                </label>
                                                <select id="tipo_gasto_{{ $v->id_vehiculo }}" name="tipo_gasto"
                                                    class="form-select form-select-sm" required>
                                                    <option value="">Selecciona tipo...</option>
                                                    <option value="combustible">Combustible</option>
                                                    <option value="mantenimiento">Mantenimiento</option>
                                                    <option value="seguro">Seguro</option>
                                                    <option value="impuestos">Impuestos</option>
                                                    <option value="peajes">Peajes</option>
                                                    <option value="otros">Otros</option>
                                                </select>
                                            </div>

                                            <!-- COLUMNA 2 — IMPORTE + COMENTARIO/ DESCRIPCIÓN DEBAJO -->
                                            <div class="col-md-4">
                                                <label for="descripcion_gasto_{{ $v->id_vehiculo }}"
                                                    class="form-label mt-3">
                                                    Descripción (opcional)
                                                </label>
                                                <textarea id="descripcion_gasto_{{ $v->id_vehiculo }}" name="descripcion" class="cometarioText" rows="2"
                                                    placeholder="Ej: gasolina, peaje, revisión, seguro..."></textarea>

                                                <p></p>
                                                <label for="archivo_{{ $v->id_vehiculo }}" class="form-label mt-3">
                                                    Archivo adjunto (opcional)
                                                </label>
                                                <input type="file" id="archivo_{{ $v->id_vehiculo }}"
                                                    name="archivo" class="form-control form-control-sm"
                                                    style="border-radius: 0px;">
                                            </div>

                                            <!-- COLUMNA 3 — TABLA GASTOS -->
                                            <div class="col-md-4">
                                                <div class="gastos-tabla">
                                                    @php
                                                        // Relación registrosGastos ordenada por fecha descendente
                                                        $registrosGastos =
                                                            $v->registrosGastos->sortByDesc('fecha_gasto') ?? collect();
                                                    @endphp

                                                    @if ($registrosGastos->isNotEmpty())
                                                        <div class="tabla-registros-gastos-wrapper mt-3">
                                                            <table class="tabla-registros-gastos">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Fecha</th>
                                                                        <th>Tipo</th>
                                                                        <th>Importe</th>
                                                                        <th>Descripción</th>
                                                                        <th>Archivo</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    @foreach ($registrosGastos as $g)
                                                                        @php
                                                                            // Soportar tanto 'archivo_path' como 'archivo' por si en la BD se usó otro nombre
                                                                            $rawPath =
                                                                                $g->archivo_path ??
                                                                                ($g->archivo ?? null);
                                                                            $archivoUrl = null;

                                                                            if (!empty($rawPath)) {
                                                                                // Normalizamos cualquier cosa:
                                                                                // puede venir como "gastos/...", "storage/gastos/...", "public/storage/gastos/..."
                                                                                $path = ltrim($rawPath, '/');

                                                                                if (strpos($path, 'public/') === 0) {
                                                                                    $path = substr($path, 7); // quitamos "public/"
                                                                                }

                                                                                if (strpos($path, 'storage/') === 0) {
                                                                                    $path = substr($path, 8); // quitamos "storage/"
                                                                                }

                                                                                // Resultado final: siempre servimos desde /storage/...
                                                                                $archivoUrl = asset('storage/' . $path);
                                                                            }
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{ \Carbon\Carbon::parse($g->fecha_gasto)->format('d/m/Y') }}
                                                                            </td>
                                                                            <td>{{ $g->tipo_gasto }}</td>
                                                                            <td>{{ number_format($g->importe, 2, ',', '.') }}
                                                                                €</td>
                                                                            <td>{{ $g->descripcion ?: '—' }}</td>
                                                                            <td>
                                                                                @if (!empty($g->archivo_path))
                                                                                    @php
                                                                                        // Normalizamos la ruta por si viene con o sin "storage/"
                                                                                        $path = ltrim(
                                                                                            $g->archivo_path,
                                                                                            '/',
                                                                                        );
                                                                                        if (
                                                                                            strpos(
                                                                                                $path,
                                                                                                'storage/',
                                                                                            ) === 0
                                                                                        ) {
                                                                                            $path = substr($path, 8);
                                                                                        }
                                                                                    @endphp

                                                                                    <a href="{{ asset('storage/' . $path) }}"
                                                                                        target="_blank">
                                                                                        Ver archivo
                                                                                    </a>
                                                                                @else
                                                                                    Sin archivo.
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <p class="text-muted mt-2">
                                                            Aún no hay registros de gastos para este vehículo.
                                                        </p>
                                                    @endif
                                                </div>
                                                <p></p>
                                                {{-- BOTÓN + MODAL GRÁFICO GASTOS --}}
                                                <button type="button" class="btn btn-primary btn-sm w-100 mt-3"
                                                    onclick="document.getElementById('modalGastos_{{ $v->id_vehiculo }}').showModal();">
                                                    Gráficos de gastos
                                                </button>

                                                <dialog id="modalGastos_{{ $v->id_vehiculo }}" class="chart-dialog">
                                                    <h3>📉 Gastos — {{ $v->marca }} {{ $v->modelo }}</h3>

                                                    <div id="chartGastos_{{ $v->id_vehiculo }}"
                                                        style="height: 420px; width: 100%;"></div>

                                                    <div class="dialog-buttons">
                                                        <button
                                                            onclick="document.getElementById('modalGastos_{{ $v->id_vehiculo }}').close();"
                                                            class="btn btn-secondary">
                                                            Cerrar
                                                        </button>
                                                    </div>
                                                </dialog>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm w-100 mt-3">
                                            Guardar gasto
                                        </button>
                                    </form>
                                </div>


                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
        {{--  --}}
        {{--  --}}
        {{--  --}}
        {{--  --}}
        {{-- PANEL CALENDARIO GRANDE --}}
        <section id="panel-calendario" class="calendar-panel" style="margin-top: 24px; display:none;">
            <div class="calendar-card">
                <div class="calendar-card-header">
                    <div class="calendar-nav">
                        <button id="cal-prev" type="button" class="calendar-nav-btn">&laquo;</button>
                        <h4 id="cal-month-label" class="calendar-month-label"></h4>
                        <button id="cal-next" type="button" class="calendar-nav-btn">&raquo;</button>
                    </div>
                </div>

                <div class="calendar-card-body">
                    <table class="calendar-table">
                        <thead>
                            <tr>
                                <th>Lun</th>
                                <th>Mar</th>
                                <th>Mié</th>
                                <th>Jue</th>
                                <th>Vie</th>
                                <th>Sáb</th>
                                <th>Dom</th>
                            </tr>
                        </thead>
                        <tbody id="cal-body">
                            {{-- Se rellena por JavaScript --}}
                        </tbody>
                    </table>

                    <aside class="calendar-details">
                        <h5 id="cal-details-title">Detalles del día</h5>
                        <div id="cal-details-content" class="calendar-details-content">
                            Pulsa un día con datos para ver los detalles.
                        </div>

                        <hr class="calendar-divider">

                        {{-- Formulario para añadir nota al calendario --}}
                        <form action="{{ route('notas-calendario.store') }}" method="POST"
                            class="calendar-note-form">
                            @csrf

                            {{-- Fecha del evento (se rellena al pulsar un día del calendario) --}}
                            <div class="form-group">
                                <label for="nota_fecha_evento">Fecha</label><br>
                                <input type="date" id="nota_fecha_evento" name="fecha_evento"
                                    class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}"
                                    required>
                            </div>
                            <p></p>
                            {{-- Hora opcional --}}
                            <div class="form-group">
                                <label for="nota_hora_evento">Hora (opcional)</label><br>
                                <input type="time" id="nota_hora_evento" name="hora_evento"
                                    class="form-control form-control-sm">
                            </div>
                            <p></p>

                            {{-- Título --}}
                            <div class="form-group">
                                <label for="nota_titulo">Título</label><br>
                                <input type="text" id="nota_titulo" name="titulo"
                                    class="form-control form-control-sm" placeholder="Revisión, ITV, viaje..."
                                    required>
                            </div>
                            <p></p>

                            {{-- Descripción --}}
                            <div class="form-group">
                                <label for="nota_descripcion">Descripción</label><br>
                                <textarea id="nota_descripcion" name="descripcion" rows="2" class="form-control form-control-sm"
                                    placeholder="Detalles de la nota..."></textarea>
                            </div>
                            <p></p>

                            {{-- Opcional: vincular a vehículo --}}
                            @if ($vehiculos->isNotEmpty())
                                <div class="form-group">
                                    <label for="nota_id_vehiculo">Vehículo (opcional)</label><br>
                                    <select id="nota_id_vehiculo" name="id_vehiculo"
                                        class="form-select form-select-sm">
                                        <option value="">Sin vehículo asociado</option>
                                        @foreach ($vehiculos as $v)
                                            <option value="{{ $v->id_vehiculo }}">
                                                {{ $v->marca }} {{ $v->modelo }} ({{ $v->matricula }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <p></p>
                            <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">
                                Guardar nota
                            </button>
                        </form>
                    </aside>
                </div>
            </div>
        </section>
    </main>

    {{-- Datos para el calendario: mezcla de km, gastos y notas_calendario --}}
    <script>
        // Espera un array de objetos con:
        // { fecha: 'YYYY-MM-DD', km: number, gastos: number, nota: string }
        const CALENDAR_EVENTS = @json($calendarEvents ?? []);
    </script>

    <script src="{{ asset('assets/js/perfil/perfil.js') }}"></script>

    {{-- Script específico del calendario grande --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const panelCalendario = document.getElementById('panel-calendario');

            const monthLabel = document.getElementById('cal-month-label');
            const calBody = document.getElementById('cal-body');
            const prevBtn = document.getElementById('cal-prev');
            const nextBtn = document.getElementById('cal-next');
            const detailsTitle = document.getElementById('cal-details-title');
            const detailsContent = document.getElementById('cal-details-content');

            // inputs del formulario de nota
            const notaFechaInput = document.getElementById('nota_fecha_evento');

            // Indexar eventos por fecha YYYY-MM-DD
            const eventsByDate = {};
            (CALENDAR_EVENTS || []).forEach(e => {
                if (!e.fecha) return;
                eventsByDate[e.fecha] = eventsByDate[e.fecha] || [];
                eventsByDate[e.fecha].push(e);
            });

            let current = new Date(); // mes actual

            const monthNames = [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ];

            // para resaltar el día seleccionado
            let selectedCell = null;
            let selectedDate = null;

            function formatDateISO(date) {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            function renderCalendar() {
                const year = current.getFullYear();
                const month = current.getMonth();

                monthLabel.textContent =
                    `${monthNames[month].charAt(0).toUpperCase() + monthNames[month].slice(1)} ${year}`;

                calBody.innerHTML = '';

                // Primer día del mes (Lunes=1... Domingo=7)
                const first = new Date(year, month, 1);
                let startDay = first.getDay(); // 0 domingo, 1 lunes...
                if (startDay === 0) startDay = 7;

                const daysInMonth = new Date(year, month + 1, 0).getDate();

                let day = 1;
                for (let week = 0; week < 6; week++) {
                    const tr = document.createElement('tr');

                    for (let dow = 1; dow <= 7; dow++) {
                        const td = document.createElement('td');

                        if ((week === 0 && dow < startDay) || day > daysInMonth) {
                            td.classList.add('empty');
                            tr.appendChild(td);
                            continue;
                        }

                        const cellDate = new Date(year, month, day);
                        const iso = formatDateISO(cellDate);

                        td.dataset.date = iso;
                        td.classList.add('calendar-day');

                        const dayNumber = document.createElement('div');
                        dayNumber.classList.add('day-number');
                        dayNumber.textContent = day;
                        td.appendChild(dayNumber);

                        // Si hay datos para este día
                        const items = eventsByDate[iso];
                        if (items && items.length) {
                            td.classList.add('has-data');

                            const badges = document.createElement('div');
                            badges.classList.add('day-badges');

                            const totalKm = items.reduce((acc, e) => acc + (Number(e.km) || 0), 0);
                            const totalGastos = items.reduce((acc, e) => acc + (Number(e.gastos) || 0), 0);
                            const hasNota = items.some(e => e.nota);

                            if (totalKm > 0) {
                                const kmBadge = document.createElement('span');
                                kmBadge.classList.add('badge', 'badge-km');
                                kmBadge.textContent = `${totalKm} km`;
                                badges.appendChild(kmBadge);
                            }

                            if (totalGastos > 0) {
                                const gastoBadge = document.createElement('span');
                                gastoBadge.classList.add('badge', 'badge-gastos');
                                gastoBadge.textContent = `${totalGastos.toFixed(2)} €`;
                                badges.appendChild(gastoBadge);
                            }
                            // Muestra hora de la primera nota si existe
                            if (hasNota) {
                                const notaBadge = document.createElement('span');
                                notaBadge.classList.add('badge', 'badge-nota');

                                // buscar la primera nota del día
                                const firstNota = items.find(i => i.nota);

                                // extraer la hora si existe
                                let hora = firstNota?.hora_evento || '';

                                // Formato bonito HH:MM
                                if (hora && hora.length >= 5) {
                                    hora = hora.slice(0, 5);
                                }

                                // Texto final
                                notaBadge.textContent = hora ? `📝 ${hora}` : '📝';

                                badges.appendChild(notaBadge);
                            }

                            td.appendChild(badges);
                        }

                        // si este día es el seleccionado, mantén la clase al re-renderizar
                        if (iso === selectedDate) {
                            td.classList.add('selected-day');
                            selectedCell = td;
                        }

                        td.addEventListener('click', function() {
                            // resaltar el día seleccionado
                            if (selectedCell) {
                                selectedCell.classList.remove('selected-day');
                            }
                            selectedCell = td;
                            selectedDate = iso;
                            td.classList.add('selected-day');

                            showDetails(iso);
                        });

                        tr.appendChild(td);
                        day++;
                    }

                    calBody.appendChild(tr);
                    if (day > daysInMonth) break;
                }
            }

            function showDetails(iso) {
                const items = eventsByDate[iso] || [];
                const [year, month, day] = iso.split('-');
                detailsTitle.textContent = `Detalles del ${day}/${month}/${year}`;

                // Sincronizar fecha seleccionada con el formulario de nota
                if (notaFechaInput) {
                    notaFechaInput.value = iso;
                }

                if (!items.length) {
                    detailsContent.textContent = 'No hay datos registrados para este día.';
                    return;
                }

                let html = '';
                items.forEach(e => {
                    html += `
                <div class="detail-item">
                    ${e.nota ? `<div class="detail-line"><strong>Nota:</strong> ${e.nota}</div>` : ''}
                    ${e.hora_evento ? `<div class="detail-line"><strong>Hora:</strong> ${e.hora_evento}</div>` : ''}
                    ${e.km ? `<div class="detail-line"><strong>Kilómetros:</strong> ${e.km}</div>` : ''}
                    ${e.gastos ? `<div class="detail-line"><strong>Gastos:</strong> ${e.gastos.toFixed(2)} €</div>` : ''}
                </div>
            `;
                });

                detailsContent.innerHTML = html;
            }

            // Navegación meses
            prevBtn.addEventListener('click', function() {
                current.setMonth(current.getMonth() - 1);
                renderCalendar();
            });

            nextBtn.addEventListener('click', function() {
                current.setMonth(current.getMonth() + 1);
                renderCalendar();
            });

            // Pintar la primera vez
            renderCalendar();

        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            @foreach ($vehiculos as $v)
                @php
                    $kmPoints = [];

                    foreach ($v->registrosKm as $rk) {
                        // Convertir fecha a timestamp en milisegundos
                        $ts = $rk->fecha_registro instanceof \Carbon\Carbon ? $rk->fecha_registro->timestamp : strtotime($rk->fecha_registro);

                        if ($ts) {
                            $kmPoints[] = [
                                'x' => $ts * 1000, // CanvasJS usa ms
                                'y' => (int) $rk->km_actual,
                            ];
                        }
                    }

                    // 🔥 Ordenar ASC por fecha: primera fecha → hoy
                    usort($kmPoints, fn($a, $b) => $a['x'] <=> $b['x']);
                @endphp

                    (function() {
                        const dialog = document.getElementById("modalKm_{{ $v->id_vehiculo }}");
                        let chartRendered = false;

                        dialog.addEventListener("toggle", function() {
                            if (!dialog.open || chartRendered) return;

                            chartRendered = true;

                            const chart = new CanvasJS.Chart("chartKm_{{ $v->id_vehiculo }}", {
                                animationEnabled: true,
                                theme: "light2",
                                title: {
                                    text: "Evolución de kilómetros"
                                },
                                axisX: {
                                    valueFormatString: "DD MMM"
                                },
                                axisY: {
                                    title: "Kilómetros",
                                    includeZero: true,
                                    maximum: null // si quieres un límite, pon un número
                                },
                                data: [{
                                    type: "splineArea",
                                    color: "#6599FF",
                                    xValueType: "dateTime",
                                    xValueFormatString: "DD MMM",
                                    yValueFormatString: "#,##0 km",
                                    dataPoints: {!! json_encode($kmPoints, JSON_NUMERIC_CHECK) !!}
                                }]
                            });

                            chart.render();
                        });
                    })();
            @endforeach

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            @foreach ($vehiculos as $v)
                @php
                    // ============================
                    // 1) AGRUPAR POR FECHA + TIPO
                    // ============================
                    // Estructura: [ tipo => [ fechaKey => [label, y] ] ]
                    $seriesMap = [];

                    foreach ($v->registrosGastos as $g) {
                        // --- Fecha normalizada ---
                        if ($g->fecha_gasto instanceof \Carbon\Carbon) {
                            $fechaKey = $g->fecha_gasto->format('Y-m-d');
                            $labelFecha = $g->fecha_gasto->format('d/m/Y');
                        } else {
                            $fecha = \Carbon\Carbon::parse($g->fecha_gasto);
                            $fechaKey = $fecha->format('Y-m-d');
                            $labelFecha = $fecha->format('d/m/Y');
                        }

                        // --- Tipo de gasto (ajusta el nombre del campo si es distinto) ---
                        $tipo = $g->tipo ?? 'Otros'; // <-- cambia 'tipo' por el campo real (ej: categoria)

                        if (!isset($seriesMap[$tipo])) {
                            $seriesMap[$tipo] = [];
                        }

                        if (!isset($seriesMap[$tipo][$fechaKey])) {
                            $seriesMap[$tipo][$fechaKey] = [
                                'label' => $labelFecha,
                                'y' => 0,
                            ];
                        }

                        $seriesMap[$tipo][$fechaKey]['y'] += (float) $g->importe;
                    }

                    // Ordenar las fechas dentro de cada serie
                    foreach ($seriesMap as $tipo => $pointsByDate) {
                        ksort($pointsByDate); // orden cronológico
                        $seriesMap[$tipo] = array_values($pointsByDate); // reset índices para CanvasJS
                    }
                @endphp

                    (function() {
                        const dialog = document.getElementById("modalGastos_{{ $v->id_vehiculo }}");
                        let chartRendered = false;

                        dialog.addEventListener("toggle", function() {
                            if (!dialog.open) return;
                            if (chartRendered) return;
                            chartRendered = true;

                            var chart = new CanvasJS.Chart("chartGastos_{{ $v->id_vehiculo }}", {
                                title: {
                                    text: "Gastos Totales (€): {{ number_format($v->registrosGastos->sum('importe'), 2, ',', '.') }} €"
                                },
                                theme: "light2",
                                animationEnabled: true,
                                toolTip: {
                                    shared: true,
                                    reversed: true
                                },
                                axisY: {
                                    title: "Gastos acumulados",
                                    suffix: " €",
                                    includeZero: true
                                },
                                legend: {
                                    cursor: "pointer",
                                    itemclick: function(e) {
                                        if (typeof(e.dataSeries.visible) === "undefined" ||
                                            e.dataSeries.visible) {
                                            e.dataSeries.visible = false;
                                        } else {
                                            e.dataSeries.visible = true;
                                        }
                                        e.chart.render();
                                    }
                                },
                                data: [
                                    @php $firstTipo = true; @endphp
                                    @foreach ($seriesMap as $tipo => $points)
                                        {
                                            type: "stackedColumn",
                                            name: "{{ $tipo }}",
                                            showInLegend: true,
                                            yValueFormatString: "€#,##0.00",
                                            dataPoints: {!! json_encode($points, JSON_NUMERIC_CHECK) !!}
                                        }
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                ]
                            });

                            chart.render();
                        });
                    })();
            @endforeach
        });
    </script>


    <!-- Bootstrap JS necesario para los modales -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
