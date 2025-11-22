@extends('layouts.app')

{{-- Make this page full width if your layout supports it --}}
@section('fullwidth', true)

@section('content')
    <style>
        nav {
            display: none !important;
        }

        /* ================ GLOBAL STYLING ================ */
        body {
            background: url('{{ asset("./assets/images/AI-Background-Image-Generator-How-It-Works-and-Why-You-Need-It.jpg") }}') center/cover no-repeat !important;
            /* deep black / navy */
        }

        .adminzone-wrapper {
            position: relative;
            min-height: calc(100vh - 64px);
            width: 100vw;
            overflow: hidden;
            padding: 0px 40px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            color: #e5e7eb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Animated neon grid background */
        .adminzone-bg-grid {
            position: absolute;
            inset: 0;
            opacity: 0.45;
            transform: perspective(600px) rotateX(60deg) translateY(-120px);
            transform-origin: top center;
            /* filter: drop-shadow(0 0 20px rgba(56, 189, 248, 0.5)); */
            pointer-events: none;
        }

        /* Floating gradient blobs */
        .adminzone-blob {
            position: absolute;
            filter: blur(40px);
            opacity: 0.55;
            pointer-events: none;
        }

        .adminzone-blob--cyan {
            width: 260px;
            height: 260px;
            /* background: radial-gradient(circle at 30% 30%, #22d3ee, transparent 60%); */
            top: -40px;
            left: -40px;
        }

        .adminzone-blob--violet {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle at 70% 70%, #a855f7, transparent 60%);
            /* bottom: -60px; */
            /* right: -60px; */
        }

        /* ============ TOP BAR ============ */
        .adminzone-topbar {
            color: #e5e7eb;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            z-index: 2;
        }

        .adminzone-title-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .adminzone-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 2px 10px;
            border-radius: 999px;
            border: 1px solid rgba(56, 189, 248, 0.6);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #67e8f9;
            /* background: linear-gradient(90deg, rgba(8, 47, 73, 0.9), rgba(30, 64, 175, 0.45)); */
        }

        .adminzone-title {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.03em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .adminzone-title span.highlight {
            background: linear-gradient(90deg, #22d3ee, #a855f7);
            -webkit-background-clip: text;
            color: transparent;
        }

        .adminzone-subtitle {
            font-size: 13px;
            color: #9ca3af;
        }

        .adminzone-top-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .adminzone-chip {
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.5);
            font-size: 11px;
            color: #9ca3af;
        }

        .btn-admin-outline,
        .btn-admin-primary {
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid transparent;
            background: transparent;
            color: #e5e7eb;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.18s ease-out;
        }

        .btn-admin-outline {
            border-color: rgba(148, 163, 184, 0.7);
        }

        .btn-admin-outline:hover {
            background: rgba(15, 23, 42, 0.9);
            border-color: #e5e7eb;
        }

        .btn-admin-primary {
            border: none;
            background: linear-gradient(135deg, #22d3ee, #a855f7);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.55);
        }

        .btn-admin-primary:hover {
            filter: brightness(1.1);
            box-shadow: 0 0 26px rgba(59, 130, 246, 0.85);
        }

        /* ================== MAIN LAYOUT GRID ================== */
        .adminzone-main {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.2fr);
            gap: 18px;
            z-index: 2;
        }

        @media (max-width: 992px) {
            .adminzone-main {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .adminzone-panel {
            background: #1e293b;
            /* << fondo limpio sin rayas */
            border-radius: 22px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            padding: 16px 18px 18px;
            position: relative;
            overflow: hidden;
        }

        .adminzone-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1px solid transparent;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.9), rgba(129, 140, 248, 0.0)) border-box;
            mask: linear-gradient(#000 0 0) padding-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0.35;
            pointer-events: none;
        }

        .adminzone-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .adminzone-panel-title {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .adminzone-panel-subtitle {
            font-size: 11px;
            color: #6b7280;
        }

        .adminzone-badge-soft {
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 10px;
            /* color: #a5b4fc; */
            /* border: 1px solid rgba(129, 140, 248, 0.6); */
            background: rgba(15, 23, 42, 0.9);
        }

        /* ============
                                                                   STATS STRIP
                                                                   ============ */
        .adminzone-stats-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .adminzone-stats-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .adminzone-stat-card {
            border-radius: 16px;
            padding: 10px 11px;
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.9), rgba(30, 64, 175, 0.6));
            border: 1px solid rgba(55, 65, 81, 0.9);
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .adminzone-stat-label {
            font-size: 11px;
            color: #9ca3af;
        }

        .adminzone-stat-value {
            font-size: 18px;
            font-weight: 600;
        }

        .adminzone-stat-tag {
            font-size: 10px;
            color: #7dd3fc;
        }

        /* ================
                                                                   USERS TABLE
                                                                   ================ */
        .adminzone-table {
            margin-top: 6px;
            border-radius: 14px;
            border: 1px solid rgba(31, 41, 55, 0.95);
            overflow: hidden;
            background: rgba(15, 23, 42, 0.95);
        }

        .adminzone-table-header,
        .adminzone-table-row {
            display: grid;
            grid-template-columns: 1.4fr 0.9fr 0.8fr 0.8fr;
            gap: 12px;
            padding: 8px 12px;
            font-size: 11px;
            align-items: center;
        }

        .adminzone-table-header {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.9), rgba(30, 64, 175, 0.7));
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #9ca3af;
        }

        .adminzone-table-row:nth-child(odd) {
            background: rgba(15, 23, 42, 0.9);
        }

        .adminzone-table-row:nth-child(even) {
            background: rgba(17, 24, 39, 0.9);
        }

        .adminzone-table-pill {
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            border: 1px solid rgba(55, 65, 81, 0.9);
            color: #e5e7eb;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .admin-user-row {
            cursor: pointer;
        }

        .admin-user-row:hover {
            background: rgba(30, 64, 175, 0.45);
        }

        .admin-user-row-details {
            grid-template-columns: 1fr;
            display: none;
        }

        .admin-user-details-box {
            border-radius: 12px;
            border: 1px solid rgba(55, 65, 81, 0.9);
            background: rgba(15, 23, 42, 0.96);
            padding: 10px 12px;
            font-size: 11px;
            color: #e5e7eb;
        }

        .admin-user-details-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9ca3af;
            margin-bottom: 6px;
        }

        .admin-user-details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 6px 18px;
            margin-bottom: 10px;
        }

        @media (max-width: 576px) {
            .admin-user-details-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .admin-user-details-label {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .admin-user-details-value {
            font-size: 12px;
            color: #e5e7eb;
            font-weight: 500;
        }

        /* Vehículos del usuario */
        .admin-user-vehicles-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9ca3af;
            margin: 8px 0 4px;
        }

        .admin-user-vehicles-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .admin-user-vehicle-card {
            border-radius: 10px;
            border: 1px solid rgba(55, 65, 81, 0.9);
            padding: 8px 10px;
            background: rgba(15, 23, 42, 0.95);
            font-size: 11px;
        }

        .admin-user-vehicle-matricula {
            font-weight: 600;
            font-size: 13px;
        }

        .admin-user-vehicle-modelo {
            font-size: 11px;
            color: #9ca3af;
        }

        .admin-user-vehicle-pill {
            padding: 2px 6px;
            border-radius: 999px;
            border: 1px solid rgba(75, 85, 99, 0.9);
            font-size: 10px;
            color: #e5e7eb;
        }

        /* RIGHT PANEL */
        .adminzone-right-grid {
            display: grid;
            grid-template-rows: auto auto;
            gap: 12px;
        }

        .adminzone-activity-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 6px;
            max-height: 230px;
            overflow: auto;
        }

        .adminzone-activity-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 11px;
        }

        .adminzone-activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            margin-top: 5px;
            background: radial-gradient(circle, #22d3ee, #0ea5e9);
            box-shadow: 0 0 16px rgba(56, 189, 248, 0.9);
        }

        .adminzone-activity-content strong {
            color: #e5e7eb;
        }

        .adminzone-activity-meta {
            font-size: 10px;
            color: #6b7280;
        }

        .adminzone-mini-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .adminzone-mini-card {
            border-radius: 14px;
            border: 1px solid rgba(55, 65, 81, 0.9);
            padding: 8px 10px;
            background: radial-gradient(circle at top, rgba(56, 189, 248, 0.18), transparent 70%),
                rgba(15, 23, 42, 0.96);
            font-size: 11px;
        }

        .adminzone-mini-value {
            font-size: 15px;
            font-weight: 600;
        }

        .adminzone-mini-label {
            font-size: 10px;
            color: #9ca3af;
        }

        /* Scrollbars */
        .adminzone-activity-list::-webkit-scrollbar {
            width: 4px;
        }

        .adminzone-activity-list::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, 0.8);
            border-radius: 999px;
        }
    </style>

    <div class="adminzone-wrapper">
        <div class="adminzone-bg-grid"></div>
        <div class="adminzone-blob adminzone-blob--cyan"></div>
        <div class="adminzone-blob adminzone-blob--violet"></div>

        {{-- TOP BAR --}}
        <div class="adminzone-topbar">
            {{-- Izquierda vacía (sin encabezado) --}}
            <div class="adminzone-title-group">
                {{-- Encabezado eliminado: sin badge, sin título, sin subtítulo --}}
            </div>

            {{-- Acciones derecha --}}
            <div class="adminzone-top-actions">
                <div class="adminzone-chip">
                    Sesión: <strong>{{ $user->user_name ?? 'admin' }}</strong>
                </div>
                <a href="{{ route('perfil') }}" class="btn-admin-outline">
                    ← Volver al perfil
                </a>
            </div>
        </div>
        {{-- MAIN GRID --}}
        <div class="adminzone-main">

            {{-- LEFT: OVERVIEW + USERS TABLE --}}
            <div class="adminzone-panel">
                <div class="adminzone-panel-header">
                    <div>
                        <div class="adminzone-panel-title">Visión general</div>
                        <div class="adminzone-panel-subtitle">
                            Resumen de la actividad reciente de la plataforma.
                        </div>
                    </div>
                    <span class="adminzone-badge-soft">Tiempo real (demo)</span>
                </div>

                {{-- Stats strip --}}
                <div class="adminzone-stats-strip">
                    <div class="adminzone-stat-card">
                        <div class="adminzone-stat-label">Usuarios registrados</div>
                        <div class="adminzone-stat-value">{{ $totalUsers }}</div>
                        <div class="adminzone-stat-tag">Total en la plataforma</div>
                    </div>
                    <div class="adminzone-stat-card">
                        <div class="adminzone-stat-label">Vehículos registrados</div>
                        <div class="adminzone-stat-value">{{ $totalVehiculos }}</div>
                        <div class="adminzone-stat-tag">Asociados a los usuarios</div>
                    </div>
                    <div class="adminzone-stat-card">
                        <div class="adminzone-stat-label">Gasto total registrado</div>
                        <div class="adminzone-stat-value">
                            € {{ number_format($gastoTotal, 2, ',', '.') }}
                        </div>
                        <div class="adminzone-stat-tag">Suma de todos los gastos</div>
                    </div>
                </div>

                {{-- Users table --}}
                <div class="adminzone-table">
                    <div class="adminzone-table-header">
                        <span>Usuario</span>
                        <span>Vehículos</span>
                        <span>Acciones</span>
                        <span>Última actividad</span>
                    </div>

                    @forelse ($latestUsers as $u)
                        {{-- Main row --}}
                        <div class="adminzone-table-row admin-user-row" data-user-id="{{ $u->id_usuario }}">
                            <span>
                                {{ $u->user_name ?? ($u->email ?? 'Usuario #' . $u->id_usuario) }}
                            </span>

                            <span>
                                <span class="adminzone-table-pill">
                                    {{ $u->vehiculos_count }} vehículo{{ $u->vehiculos_count === 1 ? '' : 's' }}
                                </span>
                            </span>

                            <span>
                                <span class="adminzone-table-pill" style="border-color:#22c55e;color:#bbf7d0;">
                                    Ver detalles ⌄
                                </span>
                            </span>

                            <span>
                                Hoy
                            </span>
                        </div>

                        {{-- Details row --}}
                        <div class="adminzone-table-row admin-user-row-details" id="user-details-{{ $u->id_usuario }}">
                            <div class="admin-user-details-box">
                                <div class="admin-user-details-title">Editar usuario</div>

                                {{-- Edit user form --}}
                                <form method="POST" action="{{ route('admin.users.update', $u->id_usuario) }}"
                                    class="mb-3">
                                    @csrf
                                    @method('PUT')

                                    <div class="admin-user-details-grid">
                                        <div>
                                            <label class="admin-user-details-label">ID usuario</label>
                                            <div class="admin-user-details-value">
                                                #{{ $u->id_usuario }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="admin-user-details-label">Nombre de usuario</label>
                                            <input type="text" name="user_name" class="form-control form-control-sm"
                                                value="{{ $u->user_name }}">
                                        </div>

                                        <div>
                                            <label class="admin-user-details-label">Nombre</label>
                                            <input type="text" name="nombre" class="form-control form-control-sm"
                                                value="{{ $u->nombre }}">
                                        </div>

                                        <div>
                                            <label class="admin-user-details-label">Apellidos</label>
                                            <input type="text" name="apellidos" class="form-control form-control-sm"
                                                value="{{ $u->apellidos }}">
                                        </div>

                                        <div>
                                            <label class="admin-user-details-label">Email</label>
                                            <input type="email" name="email" class="form-control form-control-sm"
                                                value="{{ $u->email }}">
                                        </div>

                                        <div>
                                            <label class="admin-user-details-label">Teléfono</label>
                                            <input type="text" name="telefono" class="form-control form-control-sm"
                                                value="{{ $u->telefono }}">
                                        </div>

                                        <div>
                                            <label class="admin-user-details-label">Rol admin</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="admin"
                                                    id="admin_{{ $u->id_usuario }}" value="1"
                                                    {{ !empty($u->admin) && $u->admin ? 'checked' : '' }}>
                                                <label class="form-check-label" for="admin_{{ $u->id_usuario }}">
                                                    Es administrador
                                                </label>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="admin-user-details-label">Fecha alta</label>
                                            <div class="admin-user-details-value">
                                                {{ optional($u->created_at)->format('d/m/Y') ?? '—' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-2 d-flex gap-2">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Guardar usuario
                                        </button>
                                </form>

                                {{-- Delete user form --}}
                                <form method="POST" action="{{ route('admin.users.delete', $u->id_usuario) }}"
                                    onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Eliminar usuario
                                    </button>
                                </form>
                            </div>

                            {{-- Vehicles --}}
                            <div class="admin-user-vehicles-title">
                                Vehículos del usuario
                            </div>

                            @if ($u->vehiculos_count > 0 && isset($u->vehiculos) && $u->vehiculos->count())
                                <div class="admin-user-vehicles-list">
                                    @foreach ($u->vehiculos as $veh)
                                        <div class="admin-user-vehicle-card">
                                            <div class="admin-user-vehicle-matricula">
                                                {{ $veh->matricula ?? 'Sin matrícula' }}
                                            </div>
                                            <div class="admin-user-vehicle-modelo mb-1">
                                                {{ $veh->marca ?? 'Marca desconocida' }} —
                                                {{ $veh->modelo ?? 'Modelo desconocido' }}
                                            </div>

                                            {{-- Edit vehicle form --}}
                                            <form method="POST"
                                                action="{{ route('admin.vehiculos.update', $veh->id_vehiculo) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <label class="admin-user-details-label">Matrícula</label>
                                                        <input type="text" name="matricula"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->matricula }}">
                                                    </div>

                                                    <div class="col-4">
                                                        <label class="admin-user-details-label">Marca</label>
                                                        <input type="text" name="marca"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->marca }}">
                                                    </div>

                                                    <div class="col-4">
                                                        <label class="admin-user-details-label">Modelo</label>
                                                        <input type="text" name="modelo"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->modelo }}">
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="admin-user-details-label">Año matr.</label>
                                                        <input type="number" name="anio_matriculacion"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->anio_matriculacion }}">
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="admin-user-details-label">Año fabr.</label>
                                                        <input type="number" name="anio_fabricacion"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->anio_fabricacion }}">
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="admin-user-details-label">Km</label>
                                                        <input type="number" name="km"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->km }}">
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="admin-user-details-label">Combustible</label>
                                                        <input type="text" name="combustible"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->combustible }}">
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="admin-user-details-label">Etiqueta</label>
                                                        <input type="text" name="etiqueta"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->etiqueta }}">
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="admin-user-details-label">Precio (€)</label>
                                                        <input type="number" step="0.01" name="precio"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->precio }}">
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="admin-user-details-label">2ª mano (€)</label>
                                                        <input type="number" step="0.01" name="precio_segunda_mano"
                                                            class="form-control form-control-sm"
                                                            value="{{ $veh->precio_segunda_mano }}">
                                                    </div>
                                                </div>
                                                <div class="mt-2 d-flex gap-2">
                                                    @csrf
                                                    @method('PUT')

                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        Guardar vehículo
                                                    </button>
                                            </form>

                                            {{-- Delete vehicle form --}}
                                            <form method="POST"
                                                action="{{ route('admin.vehiculos.delete', $veh->id_vehiculo) }}"
                                                onsubmit="return confirm('¿Seguro que quieres eliminar este vehículo?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Eliminar vehículo
                                                </button>
                                            </form>
                                        </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="admin-user-details-value" style="margin-top:4px;">
                            Este usuario no tiene vehículos registrados.
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="adminzone-table-row">
                <span>No hay usuarios registrados todavía.</span>
                <span>-</span>
                <span>-</span>
                <span>-</span>
            </div>
            @endforelse
        </div>
    </div>

    {{-- RIGHT: ACTIVITY + METRICS --}}
    <div class="adminzone-right-grid">
        <div class="adminzone-panel">
            <div class="adminzone-panel-header">
                <div>
                    <div class="adminzone-panel-title">Actividad reciente</div>
                    <div class="adminzone-panel-subtitle">
                        Últimos eventos administrados en AutoControl.
                    </div>
                </div>
            </div>

            <div class="adminzone-activity-list">
                <div class="adminzone-activity-item">
                    <div class="adminzone-activity-dot"></div>
                    <div class="adminzone-activity-content">
                        <strong>{{ $user->user_name ?? 'Admin' }}</strong> ha accedido a la Admin Zone.
                        <div class="adminzone-activity-meta">Ahora mismo</div>
                    </div>
                </div>

                <div class="adminzone-activity-item">
                    <div class="adminzone-activity-dot"></div>
                    <div class="adminzone-activity-content">
                        <strong>Sistema</strong> ha sincronizado los últimos gastos de vehículos.
                        <div class="adminzone-activity-meta">Hace 8 min · 5 registros actualizados</div>
                    </div>
                </div>

                <div class="adminzone-activity-item">
                    <div class="adminzone-activity-dot"></div>
                    <div class="adminzone-activity-content">
                        <strong>Script nocturno</strong> ha recalculado las métricas de consumo.
                        <div class="adminzone-activity-meta">Hoy · 03:14</div>
                    </div>
                </div>

                <div class="adminzone-activity-item">
                    <div class="adminzone-activity-dot"></div>
                    <div class="adminzone-activity-content">
                        <strong>Notificaciones</strong> enviadas a usuarios con ITV próxima.
                        <div class="adminzone-activity-meta">Ayer · 21 avisos enviados</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="adminzone-panel adminzone-panel-graph">
            <div class="adminzone-panel-header">
                <div>
                    <div class="adminzone-panel-title">Indicadores clave</div>
                    <div class="adminzone-panel-subtitle">
                        Métricas rápidas para tomar decisiones.
                    </div>
                </div>
            </div>

            <div class="adminzone-mini-metrics">
                <div class="adminzone-mini-card">
                    <div class="adminzone-mini-label">Gasto medio / vehículo</div>
                    <div class="adminzone-mini-value">
                        € {{ number_format($gastoMedioVehiculo, 2, ',', '.') }}
                    </div>
                    <div class="adminzone-mini-label">Promedio vehículos con gastos registrados.</div>
                </div>

                <div class="adminzone-mini-card">
                    <div class="adminzone-mini-label">Gasto total registrado</div>
                    <div class="adminzone-mini-value">
                        € {{ number_format($gastoTotal, 2, ',', '.') }}
                    </div>
                    <div class="adminzone-mini-label">Todos los vehículos.</div>
                </div>

                <div class="adminzone-mini-card">
                    <div class="adminzone-mini-label">Vehículos con gastos</div>
                    <div class="adminzone-mini-value">
                        {{ $vehiculosConGasto }}
                    </div>
                    <div class="adminzone-mini-label">Con al menos un gasto asociado.</div>
                </div>

                <div class="adminzone-mini-card">
                    <div class="adminzone-mini-label">Usuarios registrados</div>
                    <div class="adminzone-mini-value">{{ $totalUsers }}</div>
                    <div class="adminzone-mini-label">Control global de la plataforma.</div>
                </div>
            </div>

            <div id="adminGastosChart" style="height: 230px; width: 100%; margin-top: 10px;"></div>
        </div>
    </div>
    </div>
    </div>

    {{-- CanvasJS --}}
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var chart = new CanvasJS.Chart("adminGastosChart", {
                animationEnabled: true,
                backgroundColor: "transparent", // <<<<<< FONDO GRIS OSCURO

                title: {
                    text: "Distribución de gastos por categoría",
                    fontColor: "#E5E7EB",
                    fontSize: 16,
                },
                subtitles: [{
                    text: "AutoControl — Gastos totales",
                    fontColor: "#9CA3AF",
                }],
                data: [{
                    type: "pie",
                    yValueFormatString: "€#,##0.00",
                    indexLabelFontColor: "#E5E7EB",
                    indexLabelLineColor: "#6B7280",
                    indexLabel: "{label} ({y})",
                    dataPoints: {!! json_encode($categoriaDataPoints, JSON_NUMERIC_CHECK) !!}
                }]
            });

            chart.render();

        });
    </script>

    <script>
        // Accordion behavior for user details
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.admin-user-row');

            rows.forEach(function(row) {
                row.addEventListener('click', function(e) {
                    // Ignore clicks on buttons / forms
                    if (e.target.closest('a, button, input, select, textarea, label')) {
                        return;
                    }

                    const userId = this.getAttribute('data-user-id');
                    const detailsRow = document.getElementById('user-details-' + userId);
                    if (!detailsRow) return;

                    const isVisible = detailsRow.style.display === 'grid';

                    document.querySelectorAll('.admin-user-row-details').forEach(function(r) {
                        r.style.display = 'none';
                    });

                    if (!isVisible) {
                        detailsRow.style.display = 'grid';
                    }
                });
            });
        });
    </script>
@endsection
