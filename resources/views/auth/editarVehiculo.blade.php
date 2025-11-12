@extends('layouts.app')

@section('content')
    @php
        use Illuminate\Support\Carbon;
        $vehiculoSelId = (int) request('vehiculo', $vehiculos->first()->id_vehiculo ?? 0);
        $vehiculoSel = $vehiculos->firstWhere('id_vehiculo', $vehiculoSelId);
        $currentYear = now()->year;
    @endphp

    {{-- Bootstrap Icons (si ya lo tienes en el layout, puedes quitar esta línea) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('assets/style/editarVehiculo/editarVehiculo.css') }}">

    {{-- Estilos mínimos para asegurar tamaño / hover correcto de los botones --}}
    <style>
        /* Mismo tamaño para los dos botones de acción */
        .action-btn{
            width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;
            padding:0;border-radius:8px;position:relative;z-index:3;
        }
        .action-btn .bi{font-size:1rem;line-height:1}

        /* Evitar overlay blanco y mantener rojo puro en borrar */
        .btn-outline-danger.action-btn{
            background-color:#ff4b5c;border-color:#ff4b5c;color:#fff;
        }
        .btn-outline-danger.action-btn:hover,
        .btn-outline-danger.action-btn:focus,
        .btn-outline-danger.action-btn:active{
            background-color:#ff4b5c !important;border-color:#ff4b5c !important;color:#fff !important;
            box-shadow:none;
        }

        /* (Opcional) Mantener el lápiz sin cambio de fondo si lo deseas */
        .btn-outline-primary.action-btn{
            background:#ffffff;border:1px solid #dbeafe;color:#2563eb;
        }
        .btn-outline-primary.action-btn:hover{
            background:transparent;border-color:#bfdbfe;color:#1d4ed8;
        }

        /* Asegurar que el stretched-link no tape los botones */
        .vehiculos-editor .list-group-item{position:relative}
        .vehiculos-editor .list-group-item .stretched-link::after{z-index:1 !important}
    </style>

    <div class="vehiculos-editor">
        <div class="container-fluid">
            <h3 class="mb-4 text-center fw-bold">Editar mis vehículos</h3>

            <div class="row">
                {{-- LISTA IZQUIERDA --}}
                <div class="col-md-3 border-end pe-4 vehiculos-list">
                    @if ($vehiculos->isEmpty())
                        <div class="alert alert-info">No tienes vehículos registrados.
                            <a href="{{ route('vehiculo.create') }}">Añadir vehículo</a>.
                        </div>
                    @else
                        <ul class="list-group">
                            @foreach ($vehiculos as $v)
                                @php $isActive = $vehiculoSelId === $v->id_vehiculo; @endphp
                                <li class="list-group-item vehiculo-card d-flex justify-content-between align-items-center {{ $isActive ? 'active' : '' }}"
                                    style="position:relative;">
                                    <div class="me-2" style="min-width:0;">
                                        <div class="fw-semibold text-truncate">{{ $v->marca }} {{ $v->modelo }}</div>
                                        <small class="texto-secundario">
                                            {{ $v->anio }} — {{ $v->matricula }}
                                        </small>
                                    </div>

                                    <div class="d-flex align-items-center gap-1">
                                        {{-- Editar --}}
                                        <a href="{{ route('editarVehiculo.create', ['vehiculo' => $v->id_vehiculo]) }}"
                                           class="btn btn-outline-primary btn-sm action-btn"
                                           title="Editar" onclick="event.stopPropagation();">
                                            ✏️
                                        </a>

                                        {{-- Borrar (icono Bootstrap) --}}
                                        <form method="POST" action="{{ route('vehiculos.destroy', $v->id_vehiculo) }}"
                                              onsubmit="event.stopPropagation(); return confirm('¿Seguro que quieres eliminar este vehículo?');"
                                              class="m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm action-btn" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Link estirado para abrir/editar al hacer click en la tarjeta --}}
                                    <a class="stretched-link"
                                       href="{{ route('editarVehiculo.create', ['vehiculo' => $v->id_vehiculo]) }}"></a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- FORMULARIO DERECHA: campos 9/12 + foto 3/12 --}}
                <div class="col-md-9 ps-md-4">
                    @if ($vehiculoSel)
                        <h5 class="mb-3 border-bottom pb-2">
                            ✏️ Editar: {{ $vehiculoSel->marca }} {{ $vehiculoSel->modelo }} ({{ $vehiculoSel->anio }})
                        </h5>

                        <form method="POST" action="{{ route('vehiculos.update', $vehiculoSel->id_vehiculo) }}"
                              enctype="multipart/form-data" class="w-100">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                {{-- BLOQUE CAMPOS (9/12) --}}
                                <div class="col-12 col-xl-9">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Matrícula</label>
                                            <input type="text" name="matricula" class="form-control"
                                                   value="{{ old('matricula', $vehiculoSel->matricula) }}" required>
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Kilómetros</label>
                                            <input type="number" name="km" class="form-control" inputmode="numeric"
                                                   value="{{ old('km', $vehiculoSel->km) }}" min="0" step="1">
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            @php
                                                $fechaValue = $vehiculoSel->fecha_compra
                                                    ? \Illuminate\Support\Carbon::parse($vehiculoSel->fecha_compra)->format('Y-m-d')
                                                    : '';
                                            @endphp
                                            <label class="form-label">Fecha de compra</label>
                                            <input type="date" name="fecha_compra" class="form-control"
                                                   value="{{ old('fecha_compra', $fechaValue) }}">
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Marca</label>
                                            <input type="text" name="marca" class="form-control"
                                                   value="{{ old('marca', $vehiculoSel->marca) }}" required>
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">CV</label>
                                            <input type="number" name="cv" class="form-control"
                                                   value="{{ old('cv', $vehiculoSel->cv) }}" min="0" step="1">
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Precio (€)</label>
                                            <input type="text" name="precio" class="form-control"
                                                   value="{{ old('precio', $vehiculoSel->precio) }}"
                                                   placeholder="Ej: 12.345,67">
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Modelo</label>
                                            <input type="text" name="modelo" class="form-control"
                                                   value="{{ old('modelo', $vehiculoSel->modelo) }}" required>
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Combustible</label>
                                            <select class="form-select" name="combustible" required>
                                                <option value="">Selecciona tipo</option>
                                                @foreach (['Gasolina', 'Diésel', 'Híbrido', 'Eléctrico'] as $tipo)
                                                    <option value="{{ $tipo }}"
                                                        {{ old('combustible', $vehiculoSel->combustible) === $tipo ? 'selected' : '' }}>
                                                        {{ $tipo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Precio 2ª mano (€)</label>
                                            <input type="text" name="precio_segunda_mano" class="form-control"
                                                   value="{{ old('precio_segunda_mano', $vehiculoSel->precio_segunda_mano) }}"
                                                   placeholder="Ej: 9.999,99 (opcional)">
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Año</label>
                                            <input type="number" name="anio" class="form-control"
                                                   value="{{ old('anio', $vehiculoSel->anio) }}" min="1900"
                                                   max="{{ $currentYear }}">
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label">Etiqueta Ambiental</label>
                                            <select class="form-select" name="etiqueta" required>
                                                <option value="">Selecciona etiqueta</option>
                                                @foreach (['0', 'ECO', 'C', 'B', 'No tiene'] as $tag)
                                                    <option value="{{ $tag }}"
                                                        {{ old('etiqueta', $vehiculoSel->etiqueta) === $tag ? 'selected' : '' }}>
                                                        {{ $tag }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- BLOQUE FOTO (3/12) --}}
                                <div class="col-12 col-xl-3">
                                    <div class="text-center vehiculo-foto">
                                        <label class="form-label">Foto del vehículo</label>
                                        @php
                                            if (empty($vehiculoSel->car_avatar)) {
                                                $carSrc = asset('assets/images/default-car.png');
                                            } elseif (preg_match('/^https?:\/\//', $vehiculoSel->car_avatar)) {
                                                $carSrc = $vehiculoSel->car_avatar;
                                            } else {
                                                $carSrc = asset('storage/' . ltrim($vehiculoSel->car_avatar, '/'));
                                            }
                                        @endphp
                                        <div class="border rounded p-2 bg-light">
                                            <img src="{{ $carSrc }}" alt="Vehículo"
                                                 style="max-width:100%;height:auto;"
                                                 onerror="this.src='{{ asset('assets/images/default-car.png') }}'">
                                        </div>
                                        <div class="mt-2">
                                            <input type="file" name="car_avatar" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('editarVehiculo.create') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info mt-4">
                            Selecciona un vehículo en la lista para editar sus datos.
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-end mt-4">
                <a href="{{ route('perfil') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
@endsection
