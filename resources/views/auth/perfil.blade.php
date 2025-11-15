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
            <a href="{{ route('editarPerfil.create') }}" class="btn-sidebar">Editar Perfil</a>
            <a href="{{ route('vehiculo.create') }}" class="btn-sidebar">➕ Añadir Vehículo</a>
            <a href="{{ route('editarVehiculo.create') }}" class="btn-sidebar">Editar Vehiculo</a>
            <a href="{{ route('perfil') }}" class="btn-sidebar">⚙️ Ajustes</a>
            <a href="#" class="btn-sidebar">❓ Ayuda</a>
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
                                    ({{ $v->anio }})
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

            <!-- 🧾 Gastos (sin gráfico) -->
            <div class="card" id="card-gastos" role="button" tabindex="0" aria-controls="seccion-mis-vehiculos">
                <h3>🧾 Gastos</h3>

                @if ($vehiculos->isEmpty())
                    <p class="text-muted">Sin vehículos registrados.</p>
                @else
                    <div class="vehiculos-lista">
                        @foreach ($vehiculos as $v)
                            @php
                                $gastoCalc = $v->gastos_total ?? $v->precio * 0.05;
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

            <!-- 📅 Calendario -->
            <div class="card" id="card-calendario" role="button" tabindex="0"
                aria-controls="seccion-mis-vehiculos">
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
                                    {{ $v->marca }} {{ $v->modelo }} <span>({{ $v->anio }})</span>
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

                                {{-- 🟦 VISTA KM → para tarjeta "Kilómetros" --}}
                                <div class="tarjeta-km">
                                    <ul class="vehiculo-datos">
                                        <li class="km">
                                            <strong>Kilometraje actual:</strong>
                                            {{ number_format($v->km, 0, ',', '.') }} km
                                        </li>
                                    </ul>

                                    {{-- FORMULARIO: NUEVO REGISTRO DE KM --}}
                                    <form action="{{ route('km.store', $v->id_vehiculo) }}" method="POST"
                                        class="vehiculo-km-form mt-3">
                                        @csrf

                                        <div class="mb-2">
                                            <label for="fecha_{{ $v->id_vehiculo }}" class="form-label">
                                                Fecha del registro
                                            </label>
                                            <input type="date" id="fecha_{{ $v->id_vehiculo }}"
                                                name="fecha_registro" class="form-control form-control-sm"
                                                value="{{ now()->format('Y-m-d') }}" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="km_actual_{{ $v->id_vehiculo }}" class="form-label">
                                                Kilómetros actuales
                                            </label>
                                            <input type="number" id="km_actual_{{ $v->id_vehiculo }}"
                                                name="km_actual" class="form-control form-control-sm"
                                                min="{{ $v->km ?? 0 }}" required
                                                placeholder="Introduce los km actuales">
                                        </div>

                                        <div class="mb-2">
                                            <label for="comentario_{{ $v->id_vehiculo }}" class="form-label">
                                                Comentario (opcional)
                                            </label>
                                            <textarea id="comentario_{{ $v->id_vehiculo }}" name="comentario" class="form-control form-control-sm"
                                                rows="2" placeholder="Ej: viaje, revisión, trayecto diario..."></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            Guardar registro de km
                                        </button>
                                    </form>

                                    {{-- TABLA REGISTRO VEHÍCULOS CON SU ID --}}
                                    @php
                                        // Usamos la relación registrosKm si existe, y la ordenamos por fecha descendente
                                        $registrosKm = $v->registrosKm->sortByDesc('fecha_registro') ?? collect();
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
                                                            <td>{{ number_format($rk->km_actual, 0, ',', '.') }} km
                                                            </td>
                                                            <td>{{ $rk->comentario ?: '—' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted mt-2">Aún no hay registros de kilómetros para este
                                            vehículo.</p>
                                    @endif

                                    {{-- 🟥 VISTA GASTOS → para tarjeta "Gastos" --}}
                                    <div class="tarjeta-gastos">
                                        <ul class="vehiculo-datos">
                                            <li class="gastos">
                                                <strong>Gastos totales:</strong>
                                                {{ number_format($gastoCalc, 2, ',', '.') }} €
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </main>

    <script src="{{ asset('assets/js/perfil/perfil.js') }}"></script>
</body>

</html>
