@extends('layouts.app')

@section('content')
    @php
        use Illuminate\Support\Carbon;

        // ✅ Null-safe: si no hay vehículos, no revienta
        $firstVehiculo = $vehiculos->first();
        $vehiculoSelId = (int) request('vehiculo', $firstVehiculo->id_vehiculo ?? 0);
        $vehiculoSel = $vehiculos->firstWhere('id_vehiculo', $vehiculoSelId);
        $currentYear = now()->year;
    @endphp

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/style/editarVehiculo/editarVehiculo.css') }}">
    <script src="{{ asset('./assets/js/editarVehiculo/editarVehiculo.js') }}"></script>

    <div class="vehiculos-editor">
        <div class="container-fluid">
            <h3 class="mb-4 text-center fw-bold">Mis Vehículos</h3>

            <div class="row">
                {{-- LISTA IZQUIERDA --}}
                <div class="col-md-3 border-end pe-4 vehiculos-list">
                    @if ($vehiculos->isEmpty())
                        <div class="alert alert-info">
                            No tienes vehículos registrados.
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
                                            {{ $v->anio_matriculacion }} — {{ $v->matricula }}
                                        </small>
                                    </div>

                                    <div class="d-flex align-items-center gap-1" style="position:relative; z-index:3;">
                                        {{-- Editar --}}
                                        <a href="{{ route('editarVehiculo.create', ['vehiculo' => $v->id_vehiculo]) }}"
                                            class="btn btn-outline-primary btn-sm action-btn" title="Editar"
                                            onclick="event.stopPropagation();">
                                            ✏️
                                        </a>

                                        {{-- Borrar --}}
                                        <form method="POST"
                                            action="{{ route('vehiculos.destroy', ['vehiculo' => $v->id_vehiculo]) }}"
                                            class="m-0 p-0"
                                            onsubmit="event.stopPropagation(); return confirm('¿Seguro que quieres eliminar este vehículo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm action-btn"
                                                title="Eliminar" onclick="event.stopPropagation();">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Link estirado para abrir/editar (solo sobre el resto del li) --}}
                                    <a class="stretched-link"
                                        href="{{ route('editarVehiculo.create', ['vehiculo' => $v->id_vehiculo]) }}"></a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- FORMULARIO DERECHA --}}
                <div class="col-md-9 ps-md-4" style="background-color: white; padding: 30px; border-radius: 8px;">
                    @if ($vehiculoSel)
                        <h5 class="mb-3 border-bottom pb-2">
                            ✏️ Editar: {{ $vehiculoSel->marca }} {{ $vehiculoSel->modelo }}
                            ({{ $vehiculoSel->anio_matriculacion }})
                        </h5>

                        {{-- ERRORES --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Hay errores en el formulario:</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    @error('db')
                                        <li>{{ $message }}</li>
                                    @enderror
                                    @error('app')
                                        <li>{{ $message }}</li>
                                    @enderror
                                </ul>
                            </div>
                        @endif

                        <form id="vehiculo-edit-form" method="POST"
                            action="{{ route('vehiculos.update', $vehiculoSel->id_vehiculo) }}"
                            enctype="multipart/form-data" class="w-100">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                {{-- BLOQUE CAMPOS --}}
                                <div class="col-12 col-xl-9">
                                    <div class="row g-3">
                                        {{-- Matrícula --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="matricula">Matrícula</label>
                                            <input type="text" name="matricula" id="matricula"
                                                class="form-control @error('matricula') is-invalid @enderror"
                                                value="{{ old('matricula', $vehiculoSel->matricula) }}" required>
                                            @error('matricula')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Año matriculación --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="anio_matriculacion">Año Matriculación</label>
                                            <input type="text" inputmode="numeric" name="anio_matriculacion"
                                                id="anio_matriculacion"
                                                class="form-control js-format-int @error('anio_matriculacion') is-invalid @enderror"
                                                value="{{ old('anio_matriculacion', $vehiculoSel->anio_matriculacion) }}">
                                            @error('anio_matriculacion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Kilometraje --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="km">Kilometraje</label>
                                            <input type="text" inputmode="numeric" name="km" id="km"
                                                class="form-control js-format-int @error('km') is-invalid @enderror"
                                                value="{{ old('km', $vehiculoSel->km) }}">
                                            @error('km')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Marca --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="marca">Marca</label>
                                            <select class="form-select @error('marca') is-invalid @enderror" id="marca"
                                                name="marca" required>
                                                <option value="">Selecciona marca</option>
                                                {{-- Se rellena por JS --}}
                                            </select>
                                            @error('marca')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Modelo --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="modelo">Modelo</label>
                                            <select class="form-select @error('modelo') is-invalid @enderror" id="modelo"
                                                name="modelo" required>
                                                <option value="">Selecciona modelo</option>
                                                {{-- Se rellena por JS --}}
                                            </select>
                                            @error('modelo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Año fabricación --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="anio_fabricacion">Año Fabricación</label>
                                            <input type="text" inputmode="numeric" name="anio_fabricacion"
                                                id="anio_fabricacion"
                                                class="form-control js-format-int @error('anio_fabricacion') is-invalid @enderror"
                                                value="{{ old('anio_fabricacion', $vehiculoSel->anio_fabricacion ?? '') }}">
                                            @error('anio_fabricacion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- CV --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="cv">CV</label>
                                            <input type="text" inputmode="numeric" name="cv" id="cv"
                                                class="form-control js-format-int @error('cv') is-invalid @enderror"
                                                value="{{ old('cv', $vehiculoSel->cv) }}">
                                            @error('cv')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Combustible --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="combustible">Combustible</label>
                                            <select class="form-select @error('combustible') is-invalid @enderror"
                                                id="combustible" name="combustible">
                                                <option value="">Selecciona tipo</option>
                                                <option value="Gasolina"
                                                    {{ old('combustible', $vehiculoSel->combustible) === 'Gasolina' ? 'selected' : '' }}>
                                                    Gasolina</option>
                                                <option value="Diésel"
                                                    {{ old('combustible', $vehiculoSel->combustible) === 'Diésel' ? 'selected' : '' }}>
                                                    Diésel</option>
                                                <option value="Híbrido"
                                                    {{ old('combustible', $vehiculoSel->combustible) === 'Híbrido' ? 'selected' : '' }}>
                                                    Híbrido</option>
                                                <option value="Eléctrico"
                                                    {{ old('combustible', $vehiculoSel->combustible) === 'Eléctrico' ? 'selected' : '' }}>
                                                    Eléctrico</option>
                                            </select>
                                            @error('combustible')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Etiqueta Ambiental --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="etiqueta">Etiqueta Ambiental</label>
                                            <select class="form-select @error('etiqueta') is-invalid @enderror"
                                                id="etiqueta" name="etiqueta">
                                                <option value="">Selecciona etiqueta</option>
                                                @foreach (['0', 'ECO', 'C', 'B', 'No tiene'] as $tag)
                                                    <option value="{{ $tag }}"
                                                        {{ old('etiqueta', $vehiculoSel->etiqueta) === $tag ? 'selected' : '' }}>
                                                        {{ $tag }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('etiqueta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Fecha de compra --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            @php
                                                $fechaValue = $vehiculoSel->fecha_compra
                                                    ? Carbon::parse($vehiculoSel->fecha_compra)->format('Y-m-d')
                                                    : '';
                                            @endphp
                                            <label class="form-label" for="fecha_compra">Fecha de compra</label>
                                            <input type="date" name="fecha_compra" id="fecha_compra"
                                                class="form-control @error('fecha_compra') is-invalid @enderror"
                                                value="{{ old('fecha_compra', $fechaValue) }}">
                                            @error('fecha_compra')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Precio --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="precio">
                                                Precio:
                                                <strong>{{ number_format($vehiculoSel->precio, 2, ',', '.') }} €</strong>
                                            </label>

                                            <input type="text" inputmode="decimal" name="precio" id="precio"
                                                class="form-control js-format-money @error('precio') is-invalid @enderror"
                                                value="">
                                            @error('precio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Precio 2ª mano --}}
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="form-label" for="precio_segunda_mano">
                                                Precio de Segunda Mano:
                                                <strong>{{ number_format($vehiculoSel->precio_segunda_mano, 2, ',', '.') }}
                                                    €</strong>
                                            </label>

                                            <input type="text" inputmode="decimal" name="precio_segunda_mano"
                                                id="precio_segunda_mano"
                                                class="form-control js-format-money @error('precio_segunda_mano') is-invalid @enderror"
                                                value="">
                                            @error('precio_segunda_mano')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- BLOQUE FOTO --}}
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
                                        <div class="border rounded p-2 bg-light mb-2">
                                            <img src="{{ $carSrc }}" alt="Vehículo"
                                                style="max-width:100%;height:auto;"
                                                onerror="this.src='{{ asset('assets/images/default-car.png') }}'">
                                        </div>
                                        <div class="file-upload-wrapper">
                                            <input type="file" name="car_avatar" id="car_avatar" accept="image/*"
                                                class="@error('car_avatar') is-invalid @enderror">
                                            @error('car_avatar')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
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
                <a href="{{ route('perfil') }}" class="btn btn-gold">Volver</a>
            </div>
        </div>
    </div>

    {{-- JS marca/modelo/año/precio --}}
    <script>
        let VEHICLE_DATA = {};

        function getModelsByBrand(brand) {
            const brandObj = VEHICLE_DATA[brand];
            if (!brandObj || !Array.isArray(brandObj.modelos)) return [];
            const set = new Set();
            brandObj.modelos.forEach(m => set.add(m.modelo));
            return Array.from(set).sort();
        }

        function findModelInfo(brand, modelName) {
            const brandObj = VEHICLE_DATA[brand];
            if (!brandObj || !Array.isArray(brandObj.modelos)) return null;
            for (const m of brandObj.modelos) {
                if (m.modelo === modelName) {
                    return m;
                }
            }
            return null;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const marcaSelect = document.getElementById('marca');
            const modeloSelect = document.getElementById('modelo');
            const anioFabInput = document.getElementById('anio_fabricacion');
            const precioInput = document.getElementById('precio');

            // ✅ Protegido aunque $vehiculoSel sea null
            const oldMarca = "{{ old('marca', $vehiculoSel->marca ?? '') }}";
            const oldModelo = "{{ old('modelo', $vehiculoSel->modelo ?? '') }}";

            async function loadVehicleData() {
                try {
                    const response = await fetch("{{ asset('assets/data/vehiculos.json') }}");
                    VEHICLE_DATA = await response.json();
                    initBrandSelect();
                } catch (err) {
                    console.error('Error cargando vehiculos.json', err);
                }
            }

            function initBrandSelect() {
                if (!marcaSelect) return;

                marcaSelect.innerHTML = '<option value="">Selecciona marca</option>';

                const brands = Object.keys(VEHICLE_DATA).sort();
                brands.forEach(brand => {
                    const opt = document.createElement('option');
                    opt.value = brand;
                    opt.textContent = brand;
                    if (oldMarca === brand) {
                        opt.selected = true;
                    }
                    marcaSelect.appendChild(opt);
                });

                if (oldMarca) {
                    updateModels();
                }
            }

            function updateModels() {
                if (!marcaSelect || !modeloSelect) return;

                const brand = marcaSelect.value;
                modeloSelect.innerHTML = '<option value="">Selecciona modelo</option>';

                if (!brand) return;

                const models = getModelsByBrand(brand);
                models.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m;
                    opt.textContent = m;
                    if (oldModelo === m) {
                        opt.selected = true;
                    }
                    modeloSelect.appendChild(opt);
                });
            }

            marcaSelect?.addEventListener('change', () => {
                updateModels();
                if (anioFabInput) anioFabInput.value = "";
                if (precioInput) precioInput.value = "";
            });

            modeloSelect?.addEventListener('change', () => {
                const brand = marcaSelect.value;
                const model = modeloSelect.value;
                const info = findModelInfo(brand, model);
                if (!info) return;

                if (anioFabInput && !anioFabInput.value && info.anio_fabricacion) {
                    anioFabInput.value = info.anio_fabricacion;
                }

                if (precioInput && !precioInput.value && info.precio_original_eur) {
                    precioInput.value = info.precio_original_eur;
                }
            });

            loadVehicleData();
        });
    </script>
@endsection
