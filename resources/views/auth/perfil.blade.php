<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <!-- Meta para responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon de Cartorial -->
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">

    <title>Perfil de Usuario — Cartorial</title>

    <!-- Estilos unificados para prevenir errores -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/style/perfil/perfil.css') }}"> --}}
    {{-- Estilos --}}
    <link rel="stylesheet" href="{{ asset('assets/style/perfil/perfilImports.css') }}">


    {{-- Bootstrap Icons para iconos del footer y otros elementos --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Librería CanvasJS para los gráficos --}}
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

    {{-- Template para llamadas AJAX de km si lo necesitas en JS --}}
    <meta name="km-data-url-template" content="{{ url('/vehiculos/__ID__/km/data') }}">
</head>

<body>
    <div id="perfil-layout">
        <aside>
            @php
                // ===============================
                // BLOQUE: Datos del usuario logueado
                // ===============================

                // Usuario autenticado
                $user = Auth::user();

                // Imagen por defecto por si no hay avatar
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
                        // Si es URL absoluta (por ejemplo un avatar externo)
                        if (preg_match('/^https?:\/\//', $user->user_avatar)) {
                            $avatarSrc = $user->user_avatar;
                        } else {
                            // Avatar guardado en storage/app/public → public/storage
                            $avatarSrc = asset('storage/' . ltrim($user->user_avatar, '/'));
                        }
                    }
                }
            @endphp

            <!-- FOTO / AVATAR DEL USUARIO -->
            <div class="profile-pic">
                <img src="{{ $avatarSrc }}" alt="Usuario"
                    onerror="this.onerror=null;this.src='{{ asset('assets/images/user.png') }}';">
            </div>

            <!-- Información del perfil del usuario (datos básicos) -->
            <div class="user-info">
                <p><strong>Nombre de usuario:</strong> {{ Auth::user()->user_name }}</p>
                <p><strong>Nombre:</strong> {{ Auth::user()->nombre }}</p>
                <p><strong>Apellidos:</strong> {{ Auth::user()->apellidos }}</p>
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                <p><strong>Teléfono:</strong> {{ Auth::user()->telefono }}</p>
            </div>

            <!-- Menú lateral del perfil -->
            <div class="sidebar-menu">
                <a href="{{ route('editarPerfil.create') }}" class="btn-sidebar">👤 Editar Perfil</a>
                <a href="{{ route('vehiculo.create') }}" class="btn-sidebar">➕ Añadir Vehículo</a>
                <a href="{{ route('editarVehiculo.create') }}" class="btn-sidebar">🛠️ Editar Vehiculo</a>
                <a href="{{ route('ayuda') }}" class="btn-sidebar">❓ Ayuda</a>
                <p></p>

                {{-- Enlace a Admin Zone solo visible si el usuario es admin --}}
                @if (auth()->user()?->admin == 1)
                    <a href="{{ route('admin.dashboard') }}" class="btn-sidebar-adminzone">
                        ⚙️ Admin Zone
                    </a>
                @endif
            </div>

            {{-- Botón de cierre de sesión --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout">Cerrar Sesión</button>
            </form>
        </aside>

        <main>
            <h1>Mi Perfil</h1>

            @php
                // ===============================
                // Preparación de variables globales del perfil
                // ===============================

                // Aseguramos que $vehiculos siempre sea una colección
                $vehiculos = $vehiculos ?? collect();

                // Totales pre-calculados (por si vienen del controlador o no)
                $totalVehiculos = $totalVehiculos ?? ($vehiculos->count() ?? 0);
                $valorTotal = $valorTotal ?? ($vehiculos->sum('precio') ?? 0);
                $kmTotal = $kmTotal ?? ($vehiculos->sum('km') ?? 0);
                $gastosTotales = $gastosTotales ?? 0;
            @endphp

            @php
                // ===============================================================
                // Helper: calcularValorVehiculoView()
                // ---------------------------------------------------------------
                // Esta función calcula el valor estimado actual de un vehículo a
                // partir de su precio inicial (nuevo o de segunda mano) y los años
                // transcurridos desde la compra/matriculación.
                //
                // Está ENVUELTA en un "if (!function_exists())" para evitar errores
                // si se carga esta vista varias veces o si el helper se declara
                // en otra parte del proyecto.
                // ===============================================================

                if (!function_exists('calcularValorVehiculoView')) {
                    /**
                     * Calcula la depreciación de un vehículo según:
                     *  - su precio inicial (nuevo o 2ª mano),
                     *  - la "gama" asignada según ese precio,
                     *  - una curva de devaluación dividida en 0–15 años y +15 años.
                     *
                     * @param float $precioInicial  Precio de referencia para calcular
                     *                              la devaluación. Puede ser:
                     *                              → el precio NUEVO del coche
                     *                              → el precio de SEGUNDA MANO
                     * @param int   $anios          Años transcurridos desde matriculación
                     *
                     * @return array {
                     *      gama: "Baja" | "Media" | "Alta" | "Lujo",
                     *      valor_actual: número calculado,
                     *      devaluacion_abs: euros perdidos,
                     *      devaluacion_pct: porcentaje perdido
                     * }
                     */
                    function calcularValorVehiculoView(float $precioInicial, int $anios): array
                    {
                        // ---------------------------------------------------------------
                        // 1) Validaciones básicas
                        // ---------------------------------------------------------------
                        // Si el precio inicial es ≤ 0 o los años son negativos,
                        // devolvemos valores nulos para evitar errores o cálculos absurdos.
                        if ($precioInicial <= 0 || $anios < 0) {
                            return [
                                'gama' => null,
                                'valor_actual' => 0,
                                'devaluacion_abs' => 0,
                                'devaluacion_pct' => 0,
                            ];
                        }

                        // ---------------------------------------------------------------
                        // 2) Clasificación en gama según el precio
                        // ---------------------------------------------------------------
                        // Esta gama determina qué fórmula de depreciación aplicamos.
                        // Se basa SOLO en el precio inicial recibido.
                        if ($precioInicial >= 80001) {
                            $gama = 'Lujo'; // +80.000€
                        } elseif ($precioInicial >= 40001) {
                            $gama = 'Alta'; // 40.000–80.000€
                        } elseif ($precioInicial >= 20001) {
                            $gama = 'Media'; // 20.000–40.000€
                        } else {
                            $gama = 'Baja'; // menos de 20.000€
                        }

                        // ---------------------------------------------------------------
                        // 3) Variables base
                        // ---------------------------------------------------------------
                        // $P → precio inicial del vehículo
                        // $valor → valor que vamos a ir recalculando
                        $P = $precioInicial;
                        $valor = $P;

                        // ---------------------------------------------------------------
                        // 4) Curvas de devaluación por gama
                        // ---------------------------------------------------------------
                        // Todas las gamas utilizan una fórmula diferente para
                        // los primeros 15 años y para los años extra (> 15).
                        //
                        // Se calcula: valor = precio_inicial * (1 - coeficiente * años)
                        // O, si han pasado más de 15 años:
                        //    valor15 = precio_inicial tras 15 años
                        //    valor   = valor15 * (1 - coef_extra * años_extra)
                        // ---------------------------------------------------------------
                        switch ($gama) {
                            // ======= GAMA LUJO =======
                            case 'Lujo':
                                if ($anios <= 15) {
                                    // Pérdida muy leve: 1,5% por año hasta los 15 años
                                    $valor = $P * (1 - 0.015 * $anios);
                                } else {
                                    // Después de 15 años → pérdida del 2% por año extra
                                    $valor15 = $P * (1 - 0.015 * 15);
                                    $aniosExtra = $anios - 15;
                                    $valor = $valor15 * (1 - 0.02 * $aniosExtra);
                                }
                                break;

                            // ======= GAMA ALTA =======
                            case 'Alta':
                                if ($anios <= 15) {
                                    // 3,33% anual hasta 15 años
                                    $valor = $P * (1 - 0.0333 * $anios);
                                } else {
                                    // +5% anual después de 15 años
                                    $valor15 = $P * (1 - 0.0333 * 15);
                                    $aniosExtra = $anios - 15;
                                    $valor = $valor15 * (1 - 0.05 * $aniosExtra);
                                }
                                break;

                            // ======= GAMA MEDIA / BAJA =======
                            case 'Media':
                            case 'Baja':
                                if ($anios <= 15) {
                                    // 3,33% anual hasta 15 años (igual que gama Alta)
                                    $valor = $P * (1 - 0.0333 * $anios);
                                } else {
                                    // Después de 15 años la caída es mayor → -6% anual
                                    $valor15 = $P * (1 - 0.0333 * 15);
                                    $aniosExtra = $anios - 15;
                                    $valor = $valor15 * (1 - 0.06 * $aniosExtra);
                                }
                                break;
                        }

                        // ---------------------------------------------------------------
                        // 5) Seguridad: nunca devolver valores negativos
                        // ---------------------------------------------------------------
                        $valor = max($valor, 0);

                        // ---------------------------------------------------------------
                        // 6) Cálculo final de devaluación
                        // ---------------------------------------------------------------
                        $devaluacionAbs = $P - $valor; // € perdidos
                        $devaluacionPct = ($devaluacionAbs / $P) * 100; // % perdido

                        // ---------------------------------------------------------------
                        // 7) Resultado final
                        // ---------------------------------------------------------------
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
                {{-- 🚗 Tarjeta: Vehículos --}}
                {{-- ===================== --}}
                <div class="card" id="card-vehiculos-registrados" role="button" tabindex="0" aria-expanded="false"
                    aria-controls="seccion-mis-vehiculos">
                    <h3>🚗 Vehículos</h3>

                    {{-- Lista compacta de vehículos en la tarjeta superior --}}
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
                {{-- 💰 Tarjeta: Valor     --}}
                {{-- ===================== --}}
                <div class="card" id="card-valor" role="button" tabindex="0" aria-controls="seccion-mis-vehiculos">
                    <h3>💰 Valor</h3>

                    @if ($vehiculos->isEmpty())
                        {{-- Mensaje si no hay vehículos --}}
                        <p class="text-muted">Sin vehículos registrados.</p>
                    @else
                        {{-- Lista compacta de valor por vehículo (nuevo o 2ª mano) --}}
                        <div class="vehiculos-lista">
                            @foreach ($vehiculos as $v)
                                <div class="vehiculo-mini">
                                    <div>
                                        <strong>{{ $v->marca }} {{ $v->modelo }}</strong><br>

                                        {{-- Mostramos valor de 2ª mano si existe; si no, precio nuevo; si no, mensaje --}}
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
                {{-- 📍 Tarjeta: Kilómetros --}}
                {{-- ===================== --}}
                <div class="card" id="card-km" role="button" tabindex="0" aria-controls="seccion-mis-vehiculos">
                    <h3>📍 Kilómetros</h3>

                    @if ($vehiculos->isEmpty())
                        {{-- Mensaje si no hay vehículos --}}
                        <p class="text-muted">Sin vehículos registrados.</p>
                    @else
                        {{-- Lista compacta de km por vehículo --}}
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
                {{-- 🧾 Tarjeta: Gastos    --}}
                {{-- ===================== --}}
                <div class="card" id="card-gastos" role="button" tabindex="0"
                    aria-controls="seccion-mis-vehiculos">
                    <h3>🧾 Gastos</h3>

                    @if ($vehiculos->isEmpty())
                        {{-- Mensaje si no hay vehículos --}}
                        <p class="text-muted">Sin vehículos registrados.</p>
                    @else
                        {{-- Lista compacta de gastos totales por vehículo --}}
                        <div class="vehiculos-lista">
                            @foreach ($vehiculos as $v)
                                @php
                                    // gastoCalc viene normalmente precalculado desde el controlador
                                    $gastoCalc = $v->gastoCalc;
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
                {{-- 📅 Tarjeta: Calendario mini --}}
                {{-- ===================== --}}
                <div class="card" id="card-calendario" role="button" tabindex="0"
                    aria-controls="panel-calendario">
                    <h3>📅 Calendario</h3>

                    <div class="mini-calendar small">
                        @php
                            // ⚠ IMPORTANTE:
                            // En Blade no usamos "use Carbon\Carbon;" aquí.
                            // Usamos el namespace completo \Carbon\Carbon para evitar errores.

                            // Obtenemos la fecha actual
                            $now = \Carbon\Carbon::now();

                            // Primer día del mes actual
                            $first = $now->copy()->startOfMonth();

                            // Número de días del mes
                            $daysInMonth = $now->daysInMonth;

                            // Número de huecos iniciales (Lunes=1 ... Domingo=7)
                            $leading = $first->isoWeekday() - 1;

                            // Días de ejemplo marcados como "evento" (demo visual)
                            $sampleEvents = [5, 12, 21];
                        @endphp

                        <div class="mc-header">
                            {{-- Nombre del mes y año en formato localizado --}}
                            <span class="mc-month">{{ $now->translatedFormat('F Y') }}</span>
                        </div>

                        {{-- Cabecera de días de la semana --}}
                        <div class="mc-grid mc-days">
                            <div>Lu</div>
                            <div>Ma</div>
                            <div>Mi</div>
                            <div>Ju</div>
                            <div>Vi</div>
                            <div>Sa</div>
                            <div>Do</div>
                        </div>

                        {{-- Celdas del calendario (mini) --}}
                        <div class="mc-grid mc-dates">
                            {{-- Huecos vacíos antes del día 1 (para cuadrar lunes-domingo) --}}
                            @for ($i = 0; $i < $leading; $i++)
                                <div class="mc-date mc-empty"></div>
                            @endfor

                            {{-- Días reales del mes --}}
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $date = $first->copy()->day($d);
                                    $isToday = $date->isToday();
                                @endphp
                                <div class="mc-date {{ $isToday ? 'mc-today' : '' }}">
                                    <span>{{ $d }}</span>

                                    {{-- Punto indicador si el día está en sampleEvents (modo demo) --}}
                                    @if (in_array($d, $sampleEvents))
                                        <span class="mc-dot" title="Evento"></span>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div> {{-- /div.cards --}}

            {{-- ===================================== --}}
            {{-- Sección: listado de vehículos (detalle) --}}
            {{-- ===================================== --}}
            <section id="seccion-mis-vehiculos" class="collapsible" style="margin-top: 24px">
                <h2 class="h2_mis_vehiculos">Mis vehículos</h2>

                @if ($vehiculos->isEmpty())
                    {{-- Mensaje si no hay vehículos registrados --}}
                    <p class="text-muted">
                        <a href="{{ route('vehiculo.create') }}">Añadir vehículo</a>.
                    </p>
                @else
                    {{-- Grid principal de tarjetas de vehículo --}}
                    <div class="vehiculos-grid">
                        @foreach ($vehiculos as $v)
                            @php
                                // ===============================
                                // BLOQUE: Imagen del vehículo (car_avatar)
                                // Fallbacks si no hay imagen, si es URL externa o si está en storage
                                // ===============================

                                if (empty($v->car_avatar)) {
                                    // Sin avatar → imagen por defecto
                                    $carSrc = asset('assets/images/default-car.png');
                                } elseif (preg_match('/^https?:\/\//', $v->car_avatar)) {
                                    // Si el valor es una URL completa, la usamos tal cual
                                    $carSrc = $v->car_avatar;
                                } else {
                                    // Avatar almacenado en storage/app/public
                                    $carSrc = asset('storage/' . ltrim($v->car_avatar, '/'));
                                }

                                // Cálculo rápido de gasto por defecto (por ejemplo 5% del precio)
                                // Si ya viene gastoCalc precalculado, normalmente no usarás esta línea.
                                $gastoCalc = $v->gastos_total ?? $v->precio * 0.05;
                            @endphp

                            <article class="vehiculo-card">
                                {{-- Imagen destacada del vehículo --}}
                                <div class="vehiculo-media">
                                    <img src="{{ $carSrc }}"
                                        alt="Imagen de {{ $v->marca }} {{ $v->modelo }}"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/images/default-car.png') }}';">
                                </div>

                                <div class="vehiculo-body">
                                    {{-- Título de la tarjeta: Marca + Modelo + Año --}}
                                    <h3 class="vehiculo-titulo">
                                        {{ $v->marca }} {{ $v->modelo }}
                                        <span>({{ $v->anio_matriculacion }})</span>
                                    </h3>

                                    {{-- ============================= --}}
                                    {{-- 🟩 Tarjeta interna: Datos generales --}}
                                    {{-- ============================= --}}
                                    <div class="tarjeta-detalle">
                                        <ul class="vehiculo-datos">
                                            <li><strong>Matrícula:</strong> {{ $v->matricula }}</li>

                                            <li class="km">
                                                <strong>Km:</strong>
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

                                            {{-- 🧾 GASTOS TOTALES (por vehículo) --}}
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

                                    {{-- ================================================== --}}
                                    {{-- TARJETA GLOBAL · VALOR (SOLO EN MODO-VALOR VISUAL) --}}
                                    {{-- ================================================== --}}
                                    <div class="tarjeta-valor-global">
                                        <h3>💰 Valor actual del vehículo</h3>

                                        {{-- Recorremos todos los vehículos de nuevo para mostrar un bloque de valoración --}}
                                        @foreach ($vehiculos as $v)
                                            @php
                                                // Años desde la matriculación hasta el año actual
                                                $anios = now()->year - (int) $v->anio_matriculacion;

                                                // Cálculo de devaluación para precio nuevo (si existe)
                                                $datosNuevo =
                                                    ($v->precio ?? 0) > 0
                                                        ? calcularValorVehiculoView((float) $v->precio, $anios)
                                                        : null;

                                                // Cálculo de devaluación para precio de 2ª mano (si existe)
                                                $datosSegunda =
                                                    ($v->precio_segunda_mano ?? 0) > 0
                                                        ? calcularValorVehiculoView(
                                                            (float) $v->precio_segunda_mano,
                                                            $anios,
                                                        )
                                                        : null;

                                                // Gama base: preferimos gama calculada sobre precio nuevo
                                                // y si no existe, usamos la de segunda mano
                                                $datosBase = $datosNuevo ?? $datosSegunda;
                                            @endphp

                                            <div class="valor-item">
                                                <h4>{{ $v->marca }} {{ $v->modelo }}
                                                    ({{ $v->anio_matriculacion }})</h4>

                                                {{-- Gama del vehículo (Lujo, Alta, Media, Baja) --}}
                                                <p>
                                                    <strong>Gama:</strong>
                                                    {{ $datosBase['gama'] ?? 'N/D' }}
                                                </p>

                                                {{-- =============================== --}}
                                                {{-- 🔹 DOS COLUMNAS: NUEVO / 2ª MANO --}}
                                                {{-- =============================== --}}
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

                                                {{-- Botón para abrir modal de gráfico de valor --}}
                                                <button class="btn_grafico"
                                                    onclick="document.getElementById('modalValor_{{ $v->id_vehiculo }}').showModal();">
                                                    Ver gráfico de valor
                                                </button>

                                                {{-- Modal CanvasJS para gráfico de evolución de valor --}}
                                                <dialog id="modalValor_{{ $v->id_vehiculo }}" class="chart-dialog">
                                                    <h3>📉 Valor del vehículo — {{ $v->marca }}
                                                        {{ $v->modelo }}</h3>

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
                                        @endforeach
                                    </div>

                                    {{-- ============================= --}}
                                    {{-- 🟦 Tarjeta interna: KM        --}}
                                    {{-- ============================= --}}
                                    <div class="tarjeta-km">
                                        {{-- Resumen de km actual --}}
                                        <ul class="vehiculo-datos">
                                            <li class="km">
                                                <strong>Kilometraje actual:</strong>
                                                {{ number_format($v->km, 0, ',', '.') }} km
                                            </li>
                                        </ul>
                                        <p></p>

                                        {{-- FORMULARIO: Nuevo registro de km --}}
                                        <form action="{{ route('km.store', $v->id_vehiculo) }}" method="POST"
                                            class="vehiculo-km-form mt-3">
                                            @csrf

                                            <div class="row g-3">
                                                <!-- COLUMNA 1 — FECHA + KM -->
                                                <div class="col-md-4">
                                                    <label for="fecha_{{ $v->id_vehiculo }}" class="form-label">
                                                        Fecha del registro
                                                    </label>
                                                    {{-- Fecha inicial por defecto: hoy --}}
                                                    <input type="date" id="fecha_{{ $v->id_vehiculo }}"
                                                        name="fecha_registro" class="form-control form-control-sm"
                                                        value="{{ now()->format('Y-m-d') }}" required>
                                                    <p></p>

                                                    <label for="km_actual_{{ $v->id_vehiculo }}" class="form-label">
                                                        Kilómetros actuales
                                                    </label>
                                                    {{-- Km actuales, no puede ser menor que el ya registrado --}}
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
                                                            // Ordenamos los registros de km de más reciente a más antiguo
                                                            $registrosKm =
                                                                $v->registrosKm->sortByDesc('fecha_registro') ??
                                                                collect();
                                                        @endphp

                                                        @if ($registrosKm->isNotEmpty())
                                                            {{-- Tabla con el histórico de km --}}
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

                                                    {{-- BOTÓN para abrir el gráfico de KM --}}
                                                    <button class="btn_grafico"
                                                        onclick="document.getElementById('modalKm_{{ $v->id_vehiculo }}').showModal();">
                                                        Ver gráfico
                                                    </button>

                                                    {{-- MODAL con el gráfico de KM (CanvasJS) --}}
                                                    <dialog id="modalKm_{{ $v->id_vehiculo }}" class="chart-dialog">
                                                        <h3>📉 Kilómetros — {{ $v->marca }} {{ $v->modelo }}
                                                        </h3>

                                                        {{-- 🔵 BOTONES DE FILTRO (Día / Mes / Año) --}}
                                                        <div class="chart-filters"
                                                            style="margin-bottom: 10px; text-align:center;">
                                                            <button type="button" class="btnFiltro"
                                                                data-view="day">Día</button>
                                                            <button type="button" class="btnFiltro"
                                                                data-view="month">Mes</button>
                                                            <button type="button" class="btnFiltro"
                                                                data-view="year">Año</button>
                                                        </div>

                                                        {{-- Contenedor del gráfico de KM --}}
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

                                            {{-- Botón para guardar el nuevo registro de km --}}
                                            <button type="submit" class="btn_guardar">
                                                Guardar kilometraje
                                            </button>
                                        </form>
                                    </div>

                                    {{-- ============================= --}}
                                    {{-- 🟥 Tarjeta interna: GASTOS    --}}
                                    {{-- ============================= --}}
                                    <div class="tarjeta-gastos mt-4">
                                        <ul class="vehiculo-datos">
                                            <li class="gastos">
                                                <strong>Gastos totales:</strong>

                                                @php
                                                    // gastoCalc puede venir precalculado en el modelo
                                                    $gastoCalc = $v->gastoCalc ?? 0;
                                                @endphp

                                                {{ number_format($gastoCalc, 2, ',', '.') }} €
                                            </li>
                                        </ul>

                                        {{-- FORMULARIO: nuevo gasto asociado al vehículo --}}
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
                                                    {{-- Fecha del gasto, por defecto hoy --}}
                                                    <input type="date" id="fecha_gasto_{{ $v->id_vehiculo }}"
                                                        name="fecha_gasto" class="form-control form-control-sm"
                                                        value="{{ now()->format('Y-m-d') }}" required>
                                                    <p></p>

                                                    <label for="importe_{{ $v->id_vehiculo }}" class="form-label">
                                                        Importe (€)
                                                    </label>
                                                    {{-- Importe del gasto en euros --}}
                                                    <input type="number" id="importe_{{ $v->id_vehiculo }}"
                                                        name="importe" class="form-control form-control-sm"
                                                        step="0.01" min="0" placeholder="Ej: 45.90"
                                                        required>
                                                    <p></p>

                                                    <label for="tipo_gasto_{{ $v->id_vehiculo }}"
                                                        class="form-label mt-3">
                                                        Tipo de gasto
                                                    </label>
                                                    {{-- Tipo de gasto (combustible, seguro, etc.) --}}
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
                                                    {{-- Descripción libre del gasto --}}
                                                    <textarea id="descripcion_gasto_{{ $v->id_vehiculo }}" name="descripcion" class="cometarioText" rows="2"
                                                        placeholder="Ej: gasolina, peaje, revisión, seguro..." style="width: 100%; height: 50%"></textarea>

                                                    <p></p>
                                                    <label for="archivo_{{ $v->id_vehiculo }}"
                                                        class="form-label mt-3">
                                                        Archivo adjunto (opcional)
                                                    </label>
                                                    {{-- Factura / ticket del gasto --}}
                                                    <input type="file" id="archivo_{{ $v->id_vehiculo }}"
                                                        name="archivo" class="form-control form-control-sm"
                                                        style="border-radius: 0px;">
                                                </div>

                                                <!-- COLUMNA 3 — TABLA GASTOS + GRÁFICO -->
                                                <div class="col-md-4">
                                                    <div class="gastos-tabla">
                                                        @php
                                                            // Ordenamos los registros de gasto de más reciente a más antiguo
                                                            $registrosGastos =
                                                                $v->registrosGastos->sortByDesc('fecha_gasto') ??
                                                                collect();
                                                        @endphp

                                                        @if ($registrosGastos->isNotEmpty())
                                                            {{-- Tabla con el histórico de gastos --}}
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
                                                                                // ===============================
                                                                                // BLOQUE: Normalización de la ruta del archivo del gasto
                                                                                // Permite que funcione aunque en BD se guarde con "public/" o "storage/"
                                                                                // ===============================

                                                                                $rawPath =
                                                                                    $g->archivo_path ??
                                                                                    ($g->archivo ?? null);
                                                                                $archivoUrl = null;

                                                                                if (!empty($rawPath)) {
                                                                                    $path = ltrim($rawPath, '/');

                                                                                    // Si empieza por public/, se lo quitamos
                                                                                    if (
                                                                                        strpos($path, 'public/') === 0
                                                                                    ) {
                                                                                        $path = substr($path, 7);
                                                                                    }

                                                                                    // Si empieza por storage/, también lo limpiamos
                                                                                    if (
                                                                                        strpos($path, 'storage/') === 0
                                                                                    ) {
                                                                                        $path = substr($path, 8);
                                                                                    }

                                                                                    // URL final del archivo guardado en storage
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
                                                                                            // Igual que arriba, normalizamos la ruta
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
                                                    <button type="button" class="btn_grafico"
                                                        onclick="document.getElementById('modalGastos_{{ $v->id_vehiculo }}').showModal();">
                                                        Ver gráfico
                                                    </button>

                                                    {{-- MODAL para el gráfico de gastos (día/mes/año) --}}
                                                    <dialog id="modalGastos_{{ $v->id_vehiculo }}"
                                                        class="chart-dialog">
                                                        <h3>📉 Gastos — {{ $v->marca }} {{ $v->modelo }}</h3>

                                                        {{-- Filtros de vista (agregación por día/mes/año) --}}
                                                        <div class="chart-filters"
                                                            style="margin-bottom: 10px; text-align:center;">
                                                            <button type="button" class="btnFiltro"
                                                                data-view="day">Día</button>
                                                            <button type="button" class="btnFiltro"
                                                                data-view="month">Mes</button>
                                                            <button type="button" class="btnFiltro"
                                                                data-view="year">Año</button>
                                                        </div>

                                                        {{-- Contenedor del gráfico de gastos --}}
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

                                            {{-- Botón para guardar el nuevo gasto --}}
                                            <button type="submit" class="btn_guardar">
                                                Añadir gasto
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- ===================================== --}}
            {{-- PANEL CALENDARIO GRANDE (detallado)  --}}
            {{-- ===================================== --}}
            <section id="panel-calendario" class="calendar-panel" style="margin-top: 24px; display:none;">
                <div class="calendar-card">

                    <!-- NAV DEL MES (prev / next) -->
                    <div class="calendar-card-header">
                        <div class="calendar-nav">
                            <button id="cal-prev" type="button" class="calendar-nav-btn">&laquo;</button>
                            <h4 id="cal-month-label" class="calendar-month-label"></h4>
                            <button id="cal-next" type="button" class="calendar-nav-btn">&raquo;</button>
                        </div>
                    </div>

                    <!-- GRID: Calendario (izquierda) — Detalles (derecha) -->
                    <div class="calendar-card-body">

                        <!-- CALENDARIO PRINCIPAL -->
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
                                {{-- El cuerpo del calendario se rellena por JavaScript --}}
                            </tbody>
                        </table>

                        <!-- DETALLES + FORMULARIO DE NOTA -->
                        <div class="calendar-details">

                            {{-- Título dinámico de los detalles del día seleccionado --}}
                            <h5 id="cal-details-title">Detalles del día</h5>

                            {{-- Contenedor donde se muestran los eventos (km, gastos, notas...) --}}
                            <div id="cal-details-content" class="calendar-details-content">
                                Pulsa un día con datos para ver los detalles.
                            </div>

                            <hr class="calendar-divider">

                            {{-- FORMULARIO DE NOTA EN EL CALENDARIO --}}
                            <form action="{{ route('notas-calendario.store') }}" method="POST"
                                class="calendar-note-form">
                                @csrf

                                <!-- Fecha de la nota -->
                                <div class="form-group">
                                    <label for="nota_fecha_evento">Fecha</label><br>
                                    <input type="date" id="nota_fecha_evento" name="fecha_evento"
                                        class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}"
                                        style="width: 50%" required>
                                </div>
                                <p></p>

                                <!-- Hora opcional de la nota -->
                                <div class="form-group">
                                    <label for="nota_hora_evento">Hora (opcional)</label><br>
                                    <input type="time" id="nota_hora_evento" name="hora_evento"
                                        class="form-control form-control-sm" style="width: 50%">
                                </div>
                                <p></p>

                                <!-- Título de la nota -->
                                <div class="form-group">
                                    <label for="nota_titulo">Título</label><br>
                                    <input type="text" id="nota_titulo" name="titulo"
                                        class="form-control form-control-sm" placeholder="Revisión, ITV, viaje..."
                                        style="width: 100%; height: 30px;" required>
                                </div>
                                <p></p>

                                <!-- Descripción de la nota -->
                                <div class="form-group">
                                    <label for="nota_descripcion">Descripción</label><br>
                                    <textarea id="nota_descripcion" name="descripcion" rows="2" class="form-control form-control-sm"
                                        placeholder="Detalles de la nota..." style="width: 100%; height: 60px;"></textarea>
                                </div>
                                <p></p>

                                <!-- Vehículo asociado a la nota (opcional) -->
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

                                <!-- Botón para guardar nota -->
                                <button type="submit" class="btn_guardar">
                                    Guardar
                                </button>

                            </form>

                        </div>

                    </div>
                </div>
            </section>
        </main>

        @php
            // ==========================================
            // PREPARACIÓN DE DATOS PARA JS (gráficas y calendario)
            // Estos arrays se transforman en JSON y se exponen en window.*
            // ==========================================

            // 1) Eventos calendario tal cual ya los tienes desde el controlador
            $calendarEventsJson = $calendarEvents ?? [];

            // 2) Datos para gráficas de KM por vehículo
            $kmChartData = [];
            foreach ($vehiculos as $v) {
                $rawKm = [];
                foreach ($v->registrosKm as $rk) {
                    // Normalizamos la fecha, usando \Carbon\Carbon
                    $fecha =
                        $rk->fecha_registro instanceof \Carbon\Carbon
                            ? $rk->fecha_registro
                            : \Carbon\Carbon::parse($rk->fecha_registro);

                    // Usamos timestamp en ms para CanvasJS y etiquetas útiles para agrupar
                    $rawKm[] = [
                        'timestamp' => $fecha->timestamp * 1000, // ms para dateTime
                        'year' => $fecha->format('Y'),
                        'month' => $fecha->format('Y-m'),
                        'day' => $fecha->format('Y-m-d'),
                        'dayLabel' => $fecha->format('d/m/Y'),
                        'monthLabel' => $fecha->format('m/Y'),
                        'yearLabel' => $fecha->format('Y'),
                        'km' => (int) $rk->km_actual,
                    ];
                }

                // Guardamos la serie cruda indexada por id_vehiculo
                $kmChartData[$v->id_vehiculo] = $rawKm;
            }

            // 3) Datos para gráficas de GASTOS por vehículo
            $gastosChartData = [];
            foreach ($vehiculos as $v) {
                $rawGastos = [];
                foreach ($v->registrosGastos as $g) {
                    // Normalizamos la fecha del gasto
                    $fecha =
                        $g->fecha_gasto instanceof \Carbon\Carbon
                            ? $g->fecha_gasto
                            : \Carbon\Carbon::parse($g->fecha_gasto);

                    // Guardamos la información para agrupar por día / mes / año
                    $rawGastos[] = [
                        'date' => $fecha->format('Y-m-d'),
                        'year' => $fecha->format('Y'),
                        'month' => $fecha->format('Y-m'),
                        'dayLabel' => $fecha->format('d/m/Y'),
                        'monthLabel' => $fecha->format('m/Y'),
                        'yearLabel' => $fecha->format('Y'),
                        'importe' => (float) $g->importe,
                    ];
                }

                // Guardamos por id_vehiculo
                $gastosChartData[$v->id_vehiculo] = $rawGastos;
            }

            // 4) Datos para gráficas de VALOR (precio nuevo + 2ª mano) por vehículo
            $valorChartData = [];
            foreach ($vehiculos as $v) {
                // Años desde la matriculación
                $anios = now()->year - (int) $v->anio_matriculacion;

                $dataNuevo = [];
                $dataSegunda = [];

                // Generamos un punto por cada año desde la matriculación hasta hoy
                for ($i = 0; $i <= $anios; $i++) {
                    $year = (int) $v->anio_matriculacion + $i;

                    // Creamos una fecha 1 de enero de ese año y la pasamos a timestamp ms
                    $fecha = \Carbon\Carbon::create($year, 1, 1)->timestamp * 1000;

                    // Serie de precio nuevo
                    if (($v->precio ?? 0) > 0) {
                        $calcNuevo = calcularValorVehiculoView((float) $v->precio, $i);
                        $dataNuevo[] = [
                            'x' => $fecha,
                            'y' => round($calcNuevo['valor_actual'], 2),
                        ];
                    }

                    // Serie de precio 2ª mano
                    if (($v->precio_segunda_mano ?? 0) > 0) {
                        $calc2 = calcularValorVehiculoView((float) $v->precio_segunda_mano, $i);
                        $dataSegunda[] = [
                            'x' => $fecha,
                            'y' => round($calc2['valor_actual'], 2),
                        ];
                    }
                }

                // Guardamos ambas series por id_vehiculo
                $valorChartData[$v->id_vehiculo] = [
                    'nuevo' => $dataNuevo,
                    'segunda' => $dataSegunda,
                ];
            }
        @endphp

        {{-- Inyección de datos JS globales (para los scripts externos) --}}
        <script>
            // Eventos del calendario (km, gastos, notas)
            window.CALENDAR_EVENTS = @json($calendarEventsJson);
            // Datos crudos de km por vehículo
            window.PERFIL_KM_DATA = @json($kmChartData);
            // Datos crudos de gastos por vehículo
            window.PERFIL_GASTOS_DATA = @json($gastosChartData);
            // Datos de evolución de valor por vehículo (nuevo / 2ª mano)
            window.PERFIL_VALOR_DATA = @json($valorChartData);
        </script>

        {{-- JS de control de tarjetas y modos (Vehículos / Valor / KM / Gastos / Calendario) --}}
        <script src="{{ asset('assets/js/perfil/perfil-cards.js') }}"></script>

        {{-- Script del calendario grande (navegación, eventos, detalles) --}}
        <script src="{{ asset('assets/js/perfil/calendario.js') }}"></script>

        {{-- Scripts de gráficas (KM, Gastos, Valor) --}}
        <script src="{{ asset('assets/js/perfil/perfil-graphs.js') }}"></script>

        <!-- FOOTER -->
        <footer class="ayuda-footer">
            ...
        </footer>
    </div>

    <!-- Bootstrap JS necesario para componentes de Bootstrap (si los usas) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
