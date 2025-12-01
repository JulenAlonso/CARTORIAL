<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">
    <title>Perfil de Usuario — Cartorial</title>

    <link rel="stylesheet" href="{{ asset('assets/style/perfil/perfilImports.css') }}">

    {{-- Bootstrap Icons para los iconos del footer --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <meta name="km-data-url-template" content="{{ url('/vehiculos/__ID__/km/data') }}">
</head>

<body>
    <div id="perfil-layout">
        <aside>
            @php
                // Usuario autenticado
                $user = Auth::user();

                // Imagen por defecto
                $avatarSrc = asset('assets/images/user.png');

                if ($user) {
                    // 1) Si viene $avatarPath desde el controlador, lo respetamos
                    if (!empty($avatarPath ?? null)) {
                        $avatarSrc = $avatarPath;

                        // 2) Si el modelo tiene accessor "avatar_url"
                    } elseif (isset($user->avatar_url)) {
                        $avatarSrc = $user->avatar_url;

                        // 3) Si solo tenemos el campo "user_avatar" en BD
                    } elseif (!empty($user->user_avatar) && $user->user_avatar !== '0') {
                        // Si es URL absoluta (por ejemplo avatar externo)
                        if (preg_match('/^https?:\/\//', $user->user_avatar)) {
                            $avatarSrc = $user->user_avatar;
                        } else {
                            // Guardado en storage/app/public -> public/storage
                            $avatarSrc = asset('storage/' . ltrim($user->user_avatar, '/'));
                        }
                    }
                }
            @endphp

            <div class="profile-pic">
                <img src="{{ $avatarSrc }}" alt="Usuario"
                    onerror="this.onerror=null;this.src='{{ asset('assets/images/user.png') }}';">
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
                {{-- 👇 Aquí dentro, para que closest("form") lo encuentre --}}
                @include('components.loadingLogout')
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
            @php
                if (!function_exists('calcularValorVehiculoView')) {
                    function calcularValorVehiculoView(float $precioInicial, int $anios): array
                    {
                        if ($precioInicial <= 0 || $anios < 0) {
                            return [
                                'gama' => null,
                                'valor_actual' => 0,
                                'devaluacion_abs' => 0,
                                'devaluacion_pct' => 0,
                            ];
                        }

                        // 1) Determinar gama según precio inicial
                        if ($precioInicial >= 80001) {
                            $gama = 'Lujo';
                        } elseif ($precioInicial >= 40001) {
                            $gama = 'Alta';
                        } elseif ($precioInicial >= 20001) {
                            $gama = 'Media';
                        } else {
                            $gama = 'Baja';
                        }

                        $P = $precioInicial;
                        $valor = $P;

                        switch ($gama) {
                            case 'Lujo':
                                if ($anios <= 15) {
                                    // 1,5% del valor inicial por año
                                    $valor = $P * (1 - 0.015 * $anios);
                                } else {
                                    $valor15 = $P * (1 - 0.015 * 15);
                                    $aniosExtra = $anios - 15;
                                    // 2% anual sobre el valor del año 15
                                    $valor = $valor15 * (1 - 0.02 * $aniosExtra);
                                }
                                break;

                            case 'Alta':
                                if ($anios <= 15) {
                                    // 3,33% del valor inicial por año
                                    $valor = $P * (1 - 0.0333 * $anios);
                                } else {
                                    $valor15 = $P * (1 - 0.0333 * 15);
                                    $aniosExtra = $anios - 15;
                                    // 5% anual sobre el valor del año 15
                                    $valor = $valor15 * (1 - 0.05 * $aniosExtra);
                                }
                                break;

                            case 'Media':
                            case 'Baja':
                                if ($anios <= 15) {
                                    // 3,33% del valor inicial por año
                                    $valor = $P * (1 - 0.0333 * $anios);
                                } else {
                                    $valor15 = $P * (1 - 0.0333 * 15);
                                    $aniosExtra = $anios - 15;
                                    // 6% anual sobre el valor del año 15
                                    $valor = $valor15 * (1 - 0.06 * $aniosExtra);
                                }
                                break;
                        }

                        // Nunca negativo
                        $valor = max($valor, 0);

                        $devaluacionAbs = $P - $valor;
                        $devaluacionPct = ($devaluacionAbs / $P) * 100;

                        return [
                            'gama' => $gama,
                            'valor_actual' => $valor,
                            'devaluacion_abs' => $devaluacionAbs,
                            'devaluacion_pct' => $devaluacionPct,
                        ];
                    }
                }
            @endphp

            <div class="cards">
                {{-- ===================== --}}
                {{-- 🚗 Vehículos          --}}
                {{-- ===================== --}}
                <div class="card" id="card-vehiculos-registrados" role="button" tabindex="0" aria-expanded="false"
                    aria-controls="seccion-mis-vehiculos">
                    <h3>🚗 Vehículos</h3>

                    <div class="vehiculos-lista">
                        @foreach ($vehiculos as $v)
                            <div class="vehiculo-mini">
                                <div>
                                    <strong>{{ $v->marca }} {{ $v->modelo }}</strong>
                                    <br>
                                    <span style="font-size:0.9rem;color:#666;">
                                        Matrícula: {{ $v->matricula }}
                                    </span>
                                    <br>
                                    <span style="font-size:0.9rem;color:#444;">
                                        Año matriculación: {{ $v->anio_matriculacion }}
                                    </span>
                                    <p></p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <p class="text-muted ver-detalles">Ver detalles</p>
                </div>

                {{-- ===================== --}}
                {{-- 💰 Valor (2ª mano)   --}}
                {{-- ===================== --}}
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
                                        @if (!empty($v->precio_segunda_mano) && $v->precio_segunda_mano > 0)
                                            <span style="font-size:0.9rem;color:#444; display:inline-block;">
                                                <strong>Valor 2ª mano:</strong>
                                                {{ number_format($v->precio_segunda_mano, 2, ',', '.') }} €
                                            </span>
                                        @elseif (!empty($v->precio) && $v->precio > 0)
                                            <span style="font-size:0.9rem;color:#666; display:inline-block;">
                                                <strong>Valor nuevo:</strong>
                                                {{ number_format($v->precio, 2, ',', '.') }} €
                                            </span>
                                        @else
                                            <span style="font-size:0.9rem;color:#999; display:inline-block;">
                                                Sin precio registrado
                                            </span>
                                        @endif

                                        <p></p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-muted ver-detalles">Ver detalles</p>
                    @endif
                </div>

                {{-- ===================== --}}
                {{-- 📍 Kilómetros         --}}
                {{-- ===================== --}}
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
                                        <p></p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted ver-detalles">Ver detalles</p>
                    @endif
                </div>

                {{-- ===================== --}}
                {{-- 🧾 Gastos             --}}
                {{-- ===================== --}}
                <div class="card" id="card-gastos" role="button" tabindex="0"
                    aria-controls="seccion-mis-vehiculos">
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
                                        <p></p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted ver-detalles">Ver detalles</p>
                    @endif
                </div>

                {{-- ===================== --}}
                {{-- 📅 Calendario (mini) --}}
                {{-- ===================== --}}
                <div class="card" id="card-calendario" role="button" tabindex="0" aria-controls="panel-calendario">
                    <h3>📅 Calendario</h3>

                    <div class="mini-calendar small">
                        @php
                            $now = \Carbon\Carbon::now();
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
                    <p class="text-muted">
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

                                    {{-- 🟩 Datos generales --}}
                                    <div class="tarjeta-detalle">
                                        <ul class="vehiculo-datos">
                                            <li><strong>Matrícula:</strong> {{ $v->matricula }}</li>

                                            <li class="km"><strong>Km:</strong>
                                                {{ number_format($v->km, 0, ',', '.') }} km
                                            </li>

                                            <li><strong>CV:</strong> {{ $v->cv }}</li>
                                            <li><strong>Combustible:</strong> {{ $v->combustible }}</li>
                                            <li><strong>Etiqueta:</strong> {{ $v->etiqueta }}</li>

                                            {{-- 🔵 PRECIO NUEVO --}}
                                            @if ($v->precio > 0)
                                                <li class="precio">
                                                    <strong>Precio nuevo:</strong>
                                                    {{ number_format($v->precio, 2, ',', '.') }} €
                                                </li>
                                            @endif

                                            {{-- 🟠 PRECIO 2ª MANO --}}
                                            @if (!empty($v->precio_segunda_mano) && $v->precio_segunda_mano > 0)
                                                <li class="precio2">
                                                    <strong>Precio 2ª mano:</strong>
                                                    {{ number_format($v->precio_segunda_mano, 2, ',', '.') }} €
                                                </li>
                                            @endif

                                            {{-- 🧾 GASTOS --}}
                                            <li class="gastos">
                                                <strong>Gastos:</strong><br>
                                                <span style="font-size:0.9rem;color:#666;">
                                                    {{ number_format($v->gastoCalc, 2, ',', '.') }} €
                                                </span>
                                            </li>

                                            {{-- 📅 FECHA DE COMPRA --}}
                                            <li>
                                                <strong>Compra:</strong>
                                                {{ \Carbon\Carbon::parse($v->fecha_compra)->format('d/m/Y') }}
                                            </li>
                                        </ul>
                                    </div>
                                    {{-- ========================================= --}}
                                    {{-- TARJETA VALOR · POR VEHÍCULO             --}}
                                    {{-- ========================================= --}}
                                    @php
                                        // Años desde la matriculación de ESTE vehículo
                                        $aniosValor = now()->year - (int) $v->anio_matriculacion;

                                        // Cálculo para precio nuevo y 2ª mano (si existen)
                                        $datosNuevo =
                                            ($v->precio ?? 0) > 0
                                                ? calcularValorVehiculoView((float) $v->precio, $aniosValor)
                                                : null;

                                        $datosSegunda =
                                            ($v->precio_segunda_mano ?? 0) > 0
                                                ? calcularValorVehiculoView(
                                                    (float) $v->precio_segunda_mano,
                                                    $aniosValor,
                                                )
                                                : null;

                                        // Usamos la gama que salga del precio nuevo; si no hay, de la 2ª mano
                                        $datosBase = $datosNuevo ?? $datosSegunda;
                                    @endphp

                                    <div class="tarjeta-valor-global">
                                        <h3>💰 Valor actual del vehículo</h3>

                                        <div class="valor-item">
                                            <h4>{{ $v->marca }} {{ $v->modelo }}
                                                ({{ $v->anio_matriculacion }})</h4>

                                            {{-- Gama --}}
                                            <p>
                                                <strong>Gama:</strong>
                                                {{ $datosBase['gama'] ?? 'N/D' }}
                                            </p>

                                            {{-- 🔹 DOS COLUMNAS: NUEVO / 2ª MANO --}}
                                            <div class="valor-columns">
                                                {{-- Columna 1: Precio nuevo --}}
                                                <div class="valor-col">
                                                    <h5>🚘 Precio nuevo</h5>

                                                    @if ($datosNuevo)
                                                        <p>
                                                            <strong>Valor estimado actual:</strong>
                                                            {{ number_format($datosNuevo['valor_actual'], 2, ',', '.') }}
                                                            €
                                                        </p>

                                                        <p>
                                                            <strong>Devaluación desde nuevo:</strong>
                                                            -{{ number_format($datosNuevo['devaluacion_abs'], 2, ',', '.') }}
                                                            €
                                                            ({{ number_format($datosNuevo['devaluacion_pct'], 1, ',', '.') }}
                                                            %)
                                                        </p>
                                                    @else
                                                        <p class="text-muted">Sin precio nuevo registrado.</p>
                                                    @endif
                                                </div>

                                                {{-- Columna 2: Precio 2ª mano --}}
                                                <div class="valor-col">
                                                    <h5>🔁 Precio 2ª mano</h5>

                                                    @if ($datosSegunda)
                                                        <p>
                                                            <strong>Valor estimado actual:</strong>
                                                            {{ number_format($datosSegunda['valor_actual'], 2, ',', '.') }}
                                                            €
                                                        </p>

                                                        <p>
                                                            <strong>Devaluación desde 2ª mano:</strong>
                                                            -{{ number_format($datosSegunda['devaluacion_abs'], 2, ',', '.') }}
                                                            €
                                                            ({{ number_format($datosSegunda['devaluacion_pct'], 1, ',', '.') }}
                                                            %)
                                                        </p>
                                                    @else
                                                        <p class="text-muted">Sin precio de 2ª mano registrado.</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <hr>

                                            {{-- Botón + Modal gráfico valor (SOLO DE ESTE VEHÍCULO) --}}
                                            <button class="btn_grafico"
                                                onclick="document.getElementById('modalValor_{{ $v->id_vehiculo }}').showModal();">
                                                Ver gráfico de valor
                                            </button>

                                            <dialog id="modalValor_{{ $v->id_vehiculo }}" class="chart-dialog">
                                                <h3>📉 Valor del vehículo — {{ $v->marca }} {{ $v->modelo }}
                                                </h3>

                                                <div id="chartValor_{{ $v->id_vehiculo }}"
                                                    style="height: 420px; width: 100%;"></div>

                                                <div class="dialog-buttons">
                                                    <button
                                                        onclick="document.getElementById('modalValor_{{ $v->id_vehiculo }}').close();"
                                                        class="btn btn-secondary">
                                                        Cerrar
                                                    </button>
                                                </div>
                                            </dialog>
                                        </div>
                                    </div>
                                    {{-- --------------------------- --}}
                                    {{-- 🟦 Tarjeta KM --}}
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

                                                <!-- COLUMNA 1 — FECHA + KM -->
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

                                                <!-- COLUMNA 2 — COMENTARIO -->
                                                <div class="col-md-4">
                                                    <label for="comentario_{{ $v->id_vehiculo }}"
                                                        class="form-label mt-3">
                                                        Comentario (opcional)
                                                    </label>
                                                    <p></p>
                                                    <textarea id="comentario_{{ $v->id_vehiculo }}" name="comentario" rows="2"
                                                        placeholder="Ej: viaje, revisión, trayecto diario..." style="width: 100%; height: 50%"></textarea>
                                                </div>

                                                <!-- COLUMNA 3 — TABLA REGISTROS KM + GRÁFICO -->
                                                <div class="col-md-4">
                                                    <div class="km-tabla">
                                                        @php
                                                            $registrosKm =
                                                                $v->registrosKm->sortByDesc('fecha_registro') ??
                                                                collect();
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
                                                        @endif
                                                    </div>

                                                    <p></p>
                                                    {{-- BOTON GRAFICO --}}
                                                    <button class="btn_grafico "
                                                        onclick="document.getElementById('modalKm_{{ $v->id_vehiculo }}').showModal();">
                                                        Ver gráfico
                                                    </button>

                                                    <dialog id="modalKm_{{ $v->id_vehiculo }}" class="chart-dialog">
                                                        <h3>📉 Kilómetros — {{ $v->marca }} {{ $v->modelo }}
                                                        </h3>

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

                                                </div>
                                            </div>

                                            <button type="submit" class="btn_guardar">
                                                Guardar kilometraje
                                            </button>
                                            @include('components.loadingGuardarDato')
                                        </form>
                                    </div>

                                    {{-- 🟥 Tarjeta GASTOS --}}
                                    <div class="tarjeta-gastos mt-4">
                                        <ul class="vehiculo-datos">
                                            <li class="gastos">
                                                <strong>Gastos totales:</strong>

                                                @php
                                                    $gastoCalc = $v->gastoCalc ?? 0;
                                                @endphp

                                                {{ number_format($gastoCalc, 2, ',', '.') }} €
                                            </li>
                                        </ul>

                                        {{-- FORMULARIO: NUEVO GASTO --}}
                                        <form action="{{ route('gastos.store', $v->id_vehiculo) }}" method="POST"
                                            enctype="multipart/form-data" class="vehiculo-gastos-form mt-3">
                                            @csrf

                                            <div class="row g-3">

                                                <!-- COLUMNA 1 — FECHA + IMPORTE + TIPO -->
                                                <div class="col-md-4">
                                                    <label for="fecha_gasto_{{ $v->id_vehiculo }}"
                                                        class="form-label">
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
                                                        step="0.01" min="0" placeholder="Ej: 45.90"
                                                        required>
                                                    <p></p>
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

                                                <!-- COLUMNA 2 — DESCRIPCIÓN + ARCHIVO -->
                                                <div class="col-md-4">
                                                    <label for="descripcion_gasto_{{ $v->id_vehiculo }}"
                                                        class="form-label mt-3">
                                                        Descripción (opcional)
                                                    </label>
                                                    <p></p>
                                                    <textarea id="descripcion_gasto_{{ $v->id_vehiculo }}" name="descripcion" class="cometarioText" rows="2"
                                                        placeholder="Ej: gasolina, peaje, revisión, seguro..." style="width: 100%; height: 50%"></textarea>

                                                    <p></p>
                                                    <label for="archivo_{{ $v->id_vehiculo }}"
                                                        class="form-label mt-3">
                                                        Archivo adjunto (opcional)
                                                    </label>
                                                    <input type="file" id="archivo_{{ $v->id_vehiculo }}"
                                                        name="archivo" class="form-control form-control-sm"
                                                        style="border-radius: 0px;">
                                                </div>

                                                <!-- COLUMNA 3 — TABLA GASTOS + GRÁFICO -->
                                                <div class="col-md-4">
                                                    <div class="gastos-tabla">
                                                        @php
                                                            $registrosGastos =
                                                                $v->registrosGastos->sortByDesc('fecha_gasto') ??
                                                                collect();
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
                                                                                $rawPath =
                                                                                    $g->archivo_path ??
                                                                                    ($g->archivo ?? null);
                                                                                $archivoUrl = null;

                                                                                if (!empty($rawPath)) {
                                                                                    $path = ltrim($rawPath, '/');

                                                                                    if (
                                                                                        strpos($path, 'public/') === 0
                                                                                    ) {
                                                                                        $path = substr($path, 7);
                                                                                    }

                                                                                    if (
                                                                                        strpos($path, 'storage/') === 0
                                                                                    ) {
                                                                                        $path = substr($path, 8);
                                                                                    }

                                                                                    $archivoUrl = asset(
                                                                                        'storage/' . $path,
                                                                                    );
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
                                                                                                $path = substr(
                                                                                                    $path,
                                                                                                    8,
                                                                                                );
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
                                                        @endif
                                                    </div>
                                                    <p></p>
                                                    {{-- BOTÓN + MODAL GRÁFICO GASTOS --}}
                                                    <button type="button" class="btn_grafico "
                                                        onclick="document.getElementById('modalGastos_{{ $v->id_vehiculo }}').showModal();">
                                                        Ver gráfico
                                                    </button>

                                                    <dialog id="modalGastos_{{ $v->id_vehiculo }}"
                                                        class="chart-dialog">
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
                                            <button type="submit" class="btn_guardar">
                                                Añadir gasto
                                            </button>
                                            @include('components.loadingGuardarDato')
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- PANEL CALENDARIO GRANDE --}}
            <section id="panel-calendario" class="calendar-panel" style="margin-top: 24px; display:none;">
                <div class="calendar-card">

                    <!-- NAV DEL MES -->
                    <div class="calendar-card-header">
                        <div class="calendar-nav">
                            <button id="cal-prev" type="button" class="calendar-nav-btn">&laquo;</button>
                            <h4 id="cal-month-label" class="calendar-month-label"></h4>
                            <button id="cal-next" type="button" class="calendar-nav-btn">&raquo;</button>
                        </div>
                    </div>

                    <!-- GRID: CALENDARIO IZQUIERDA — DETALLES DERECHA -->
                    <div class="calendar-card-body">

                        <!-- CALENDARIO -->
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

                        <!-- DETALLES + FORMULARIO -->
                        <div class="calendar-details">

                            <h5 id="cal-details-title">Detalles del día</h5>

                            <div id="cal-details-content" class="calendar-details-content">
                                Pulsa un día con datos para ver los detalles.
                            </div>

                            <hr class="calendar-divider">

                            {{-- FORMULARIO DE NOTA --}}
                            <form action="{{ route('notas-calendario.store') }}" method="POST"
                                class="calendar-note-form">
                                @csrf

                                <!-- Fecha -->
                                <div class="form-group">
                                    <label for="nota_fecha_evento">Fecha</label><br>
                                    <input type="date" id="nota_fecha_evento" name="fecha_evento"
                                        class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}"
                                        style="width: 50%" required>
                                </div>
                                <p></p>

                                <!-- Hora -->
                                <div class="form-group">
                                    <label for="nota_hora_evento">Hora (opcional)</label><br>
                                    <input type="time" id="nota_hora_evento" name="hora_evento"
                                        class="form-control form-control-sm" style="width: 50%">
                                </div>
                                <p></p>

                                <!-- Título -->
                                <div class="form-group">
                                    <label for="nota_titulo">Título</label><br>
                                    <input type="text" id="nota_titulo" name="titulo"
                                        class="form-control form-control-sm" placeholder="Revisión, ITV, viaje..."
                                        style="width: 100%; height: 30px;" required>
                                </div>
                                <p></p>

                                <!-- Descripción -->
                                <div class="form-group">
                                    <label for="nota_descripcion">Descripción</label><br>
                                    <textarea id="nota_descripcion" name="descripcion" rows="2" class="form-control form-control-sm"
                                        placeholder="Detalles de la nota..." style="width: 100%; height: 60px;"></textarea>
                                </div>
                                <p></p>

                                <!-- Vehículo -->
                                @if ($vehiculos->isNotEmpty())
                                    <div class="form-group">
                                        <label for="nota_id_vehiculo">Vehículo (opcional)</label><br>
                                        <select id="nota_id_vehiculo" name="id_vehiculo"
                                            class="form-select form-select-sm" style="width: 100%; height: 20px;">
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

                                <!-- Botón -->
                                <button type="submit" class="btn_guardar">
                                    Guardar
                                </button>

                            </form>

                        </div>

                    </div>
                </div>
            </section>
        </main>

        {{-- Datos para el calendario: mezcla de km, gastos y notas_calendario --}}
        <script>
            const CALENDAR_EVENTS = @json($calendarEvents ?? []);
        </script>

        <script src="{{ asset('assets/js/perfil/perfil-cards.js') }}"></script>
        <script src="{{ asset('assets/js/perfil/calendario.js') }}"></script>

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

                const notaFechaInput = document.getElementById('nota_fecha_evento');

                const eventsByDate = {};
                (CALENDAR_EVENTS || []).forEach(e => {
                    if (!e.fecha) return;
                    eventsByDate[e.fecha] = eventsByDate[e.fecha] || [];
                    eventsByDate[e.fecha].push(e);
                });

                let current = new Date();

                const monthNames = [
                    'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
                ];

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

                    const first = new Date(year, month, 1);
                    let startDay = first.getDay();
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

                                if (hasNota) {
                                    const notaBadge = document.createElement('span');
                                    notaBadge.classList.add('badge', 'badge-nota');

                                    const firstNota = items.find(i => i.nota);
                                    let hora = firstNota?.hora_evento || '';

                                    if (hora && hora.length >= 5) {
                                        hora = hora.slice(0, 5);
                                    }

                                    notaBadge.textContent = hora ? `📝 ${hora}` : '📝';

                                    badges.appendChild(notaBadge);
                                }

                                td.appendChild(badges);
                            }

                            if (iso === selectedDate) {
                                td.classList.add('selected-day');
                                selectedCell = td;
                            }

                            td.addEventListener('click', function() {
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

                prevBtn.addEventListener('click', function() {
                    current.setMonth(current.getMonth() - 1);
                    renderCalendar();
                });

                nextBtn.addEventListener('click', function() {
                    current.setMonth(current.getMonth() + 1);
                    renderCalendar();
                });

                renderCalendar();

            });
        </script>
        {{-- ------------------------------------------------------------------------------------- --}}
        {{-- ------------------------------------------------------------------------------------- --}}
        {{-- GRAFICOS --}}

        {{-- =================== GRÁFICOS KM POR VEHÍCULO =================== --}}
        @php
            $kmData = [];

            foreach ($vehiculos as $v) {
                $kmPoints = [];

                foreach ($v->registrosKm as $rk) {
                    // Detectar si es instancia de Carbon sin usar "use Carbon"
                    if ($rk->fecha_registro instanceof \Carbon\Carbon) {
                        $ts = $rk->fecha_registro->timestamp;
                    } else {
                        $ts = strtotime($rk->fecha_registro);
                    }

                    if ($ts) {
                        $kmPoints[] = [
                            'x' => $ts * 1000,
                            'y' => (int) $rk->km_actual,
                        ];
                    }
                }

                usort($kmPoints, fn($a, $b) => $a['x'] <=> $b['x']);

                $kmData[$v->id_vehiculo] = $kmPoints;
            }
        @endphp

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Objeto global: idVehiculo => puntos km
                const KM_DATA = @json($kmData, JSON_NUMERIC_CHECK);

                // Recorremos cada vehículo que tenga datos (o aunque no tenga)
                Object.keys(KM_DATA).forEach(function(idVehiculo) {
                    const dialog = document.getElementById("modalKm_" + idVehiculo);
                    const containerId = "chartKm_" + idVehiculo;

                    if (!dialog) {
                        console.warn("No se encontró el dialog para vehículo", idVehiculo);
                        return;
                    }

                    let chartRendered = false;

                    dialog.addEventListener("toggle", function() {
                        // Solo cuando se abre y solo la primera vez
                        if (!dialog.open || chartRendered) return;
                        chartRendered = true;

                        const dataPoints = KM_DATA[idVehiculo] || [];

                        const chart = new CanvasJS.Chart(containerId, {
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
                                includeZero: true
                            },
                            data: [{
                                type: "splineArea",
                                color: "#6599FF",
                                xValueType: "dateTime",
                                xValueFormatString: "DD MMM",
                                yValueFormatString: "#,##0 km",
                                dataPoints: dataPoints
                            }]
                        });

                        chart.render();
                    });
                });
            });
        </script>
        {{-- ================================================================ --}}
        {{-- Gráficos de GASTOS --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                @foreach ($vehiculos as $v)
                    @php
                        $dataPoints = [];

                        foreach ($v->registrosGastos as $g) {
                            // Convertir fecha a Carbon
                            if ($g->fecha_gasto instanceof \Carbon\Carbon) {
                                $fecha = $g->fecha_gasto;
                            } else {
                                $fecha = \Carbon\Carbon::parse($g->fecha_gasto);
                            }

                            // Añadir punto inicialmente SIN ordenar
                            $dataPoints[] = [
                                'y' => (float) $g->importe,
                                'label' => $fecha->format('d/m/Y'),
                                'fecha_sort' => $fecha->timestamp, // 👈 lo usamos para ordenar correctamente
                            ];
                        }

                        // 🔥 ORDENAR ASCENDENTE por timestamp
                        usort($dataPoints, fn($a, $b) => $a['fecha_sort'] <=> $b['fecha_sort']);

                        // Eliminar la clave auxiliar
                        foreach ($dataPoints as &$p) {
                            unset($p['fecha_sort']);
                        }

                    @endphp

                        (function() {
                            const dialog = document.getElementById("modalGastos_{{ $v->id_vehiculo }}");
                            let chartRendered = false;

                            dialog.addEventListener("toggle", function() {
                                if (!dialog.open || chartRendered) return;
                                chartRendered = true;

                                var chart = new CanvasJS.Chart("chartGastos_{{ $v->id_vehiculo }}", {
                                    animationEnabled: true,
                                    theme: "light2",
                                    title: {
                                        text: "Gastos del vehículo — Total: {{ number_format($v->registrosGastos->sum('importe'), 2, ',', '.') }} €"
                                    },
                                    axisY: {
                                        title: "Importe (€)",
                                        suffix: " €"
                                    },
                                    data: [{
                                        type: "column",
                                        showInLegend: true,
                                        legendText: "Cada barra = 1 gasto",
                                        legendMarkerColor: "grey",
                                        yValueFormatString: "€#,##0.00",
                                        dataPoints: {!! json_encode($dataPoints, JSON_NUMERIC_CHECK) !!}
                                    }]
                                });

                                chart.render();
                            });
                        })();
                @endforeach

            });
        </script>
        {{-- ================================================================ --}}

        {{-- ================================================================ --}}
        {{-- Gráficos de VALOR (Nuevo + 2ª mano) --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                @foreach ($vehiculos as $v)
                    @php
                        $anios = now()->year - (int) $v->anio_matriculacion;

                        $dataNuevo = [];
                        $dataSegunda = [];

                        // Generar año por año desde la matriculación hasta hoy
                        for ($i = 0; $i <= $anios; $i++) {
                            $year = (int) $v->anio_matriculacion + $i;

                            // Usar \Carbon\Carbon
                            $fecha = \Carbon\Carbon::create($year, 1, 1)->timestamp * 1000;

                            if (($v->precio ?? 0) > 0) {
                                $calcNuevo = calcularValorVehiculoView((float) $v->precio, $i);
                                $dataNuevo[] = [
                                    'x' => $fecha,
                                    'y' => round($calcNuevo['valor_actual'], 2),
                                ];
                            }

                            if (($v->precio_segunda_mano ?? 0) > 0) {
                                $calc2 = calcularValorVehiculoView((float) $v->precio_segunda_mano, $i);
                                $dataSegunda[] = [
                                    'x' => $fecha,
                                    'y' => round($calc2['valor_actual'], 2),
                                ];
                            }
                        }
                    @endphp

                        (function() {
                            const dialog = document.getElementById("modalValor_{{ $v->id_vehiculo }}");
                            let chartRendered = false;

                            dialog.addEventListener("toggle", function() {
                                if (!dialog.open || chartRendered) return;
                                chartRendered = true;

                                var chart = new CanvasJS.Chart("chartValor_{{ $v->id_vehiculo }}", {
                                    animationEnabled: true,
                                    theme: "light2",
                                    title: {
                                        text: "Evolución del valor del vehículo"
                                    },
                                    axisX: {
                                        valueFormatString: "YYYY" // 👈 solo formato del eje
                                    },
                                    axisY: {
                                        prefix: "€",
                                        includeZero: false
                                    },
                                    toolTip: {
                                        shared: true
                                    },
                                    legend: {
                                        cursor: "pointer",
                                        itemclick: function(e) {
                                            e.dataSeries.visible = !(e.dataSeries.visible ??
                                                true);
                                            e.chart.render();
                                        }
                                    },
                                    data: [{
                                            type: "area",
                                            color: "#4A90E2", // 🔵 Precio nuevo
                                            name: "Precio nuevo",
                                            showInLegend: true,
                                            xValueType: "dateTime", // 👈 aquí
                                            xValueFormatString: "YYYY", // 👈 y aquí
                                            yValueFormatString: "€#,##0.##",
                                            dataPoints: {!! json_encode($dataNuevo, JSON_NUMERIC_CHECK) !!}
                                        },
                                        {
                                            type: "area",
                                            color: "#E24A4A", // 🔴 Precio 2ª mano
                                            name: "Precio 2ª mano",
                                            showInLegend: true,
                                            xValueType: "dateTime", // 👈 aquí también
                                            xValueFormatString: "YYYY",
                                            yValueFormatString: "€#,##0.##",
                                            dataPoints: {!! json_encode($dataSegunda, JSON_NUMERIC_CHECK) !!}
                                        }
                                    ]
                                });

                                chart.render();
                            });
                        })();
                @endforeach
            });
        </script>

        {{-- ------------------------------------------------------------------------------------- --}}
        {{-- ------------------------------------------------------------------------------------- --}}

        <!-- FOOTER -->
        <footer class="ayuda-footer">
            <div class="footer-content">

                <!-- Columna 1 - Marca -->
                <div class="footer-section">
                    <div class="footer-socials">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <!-- Columna 2 - Soporte -->
                <div class="footer-section">
                    <h4 class="footer-subtitle">Soporte</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('ayuda') }}">Centro de Ayuda</a></li>
                    </ul>
                </div>

                <!-- Columna 3 - Legal -->
                <div class="footer-section">
                    <h4 class="footer-subtitle">Legal</h4>
                    <ul class="footer-links footer-links-legal">
                        <li><a href="#">Privacidad</a></li>
                        <li><a href="#">Términos de uso</a></li>
                        <li><a href="#">Cookies</a></li>
                    </ul>
                </div>
            </div>

            <!-- Línea inferior -->
            <div class="footer-bottom">
                © {{ date('Y') }} Cartorial — Todos los derechos reservados.
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS necesario para los modales -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
