<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Alta de Vehículo</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/style/Vehiculo.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @php
        // Año actual, lo usamos como límite máximo en los campos de año
        $currentYear = now()->year;
    @endphp

    <div class="container py-5">
        <div class="card p-4">
            <h3 class="text-center mb-4">Alta de Vehículo</h3>

            {{-- Bloque de errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Hay errores en el formulario:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        {{-- Errores personalizados del controlador (db/app) --}}
                        @error('db')
                            <li>{{ $message }}</li>
                        @enderror
                        @error('app')
                            <li>{{ $message }}</li>
                        @enderror
                    </ul>
                </div>
            @endif

            <form id="vehiculo-form"
                  method="POST"
                  action="{{ route('vehiculo.store') }}"
                  enctype="multipart/form-data"
                  novalidate>
                @csrf

                <div class="row g-4">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        {{-- matricula → columna vehiculos.matricula --}}
                        <div class="mb-3">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <input type="text"
                                   class="form-control @error('matricula') is-invalid @enderror"
                                   id="matricula"
                                   name="matricula"
                                   value="{{ old('matricula') }}"
                                   required>
                            @error('matricula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- anio_matriculacion → columna vehiculos.anio_matriculacion --}}
                        <div class="mb-3">
                            <label for="anio_matriculacion" class="form-label">Año Matriculación</label>
                            <input type="number"
                                   inputmode="numeric"
                                   class="form-control js-format-int @error('anio_matriculacion') is-invalid @enderror"
                                   id="anio_matriculacion"
                                   name="anio_matriculacion"
                                   value="{{ old('anio_matriculacion') }}"
                                   min="1886"
                                   max="{{ $currentYear }}">
                            @error('anio_matriculacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- marca → columna vehiculos.marca --}}
                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <select class="form-select @error('marca') is-invalid @enderror"
                                    id="marca"
                                    name="marca"
                                    required>
                                <option value="">Selecciona marca</option>
                                <!-- Se rellena por JS -->
                            </select>
                            @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- modelo → columna vehiculos.modelo --}}
                        <div class="mb-3">
                            <label for="modelo" class="form-label">Modelo</label>
                            <select class="form-select @error('modelo') is-invalid @enderror"
                                    id="modelo"
                                    name="modelo"
                                    required>
                                <option value="">Selecciona modelo</option>
                                <!-- Se rellena por JS -->
                            </select>
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- anio_fabricacion → columna vehiculos.anio_fabricacion --}}
                        <div class="mb-3">
                            <label for="anio_fabricacion" class="form-label">Año Fabricación</label>
                            <input type="number"
                                   inputmode="numeric"
                                   class="form-control js-format-int @error('anio_fabricacion') is-invalid @enderror"
                                   id="anio_fabricacion"
                                   name="anio_fabricacion"
                                   value="{{ old('anio_fabricacion') }}"
                                   min="1886"
                                   max="{{ $currentYear }}">
                            @error('anio_fabricacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- km → columna vehiculos.km --}}
                        <div class="mb-3">
                            <label for="km" class="form-label">Kilometraje</label>
                            <input type="number"
                                   inputmode="numeric"
                                   class="form-control js-format-int @error('km') is-invalid @enderror"
                                   id="km"
                                   name="km"
                                   value="{{ old('km') }}"
                                   min="0">
                            @error('km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- cv → columna vehiculos.cv --}}
                        <div class="mb-3">
                            <label for="cv" class="form-label">CV</label>
                            <input type="number"
                                   inputmode="numeric"
                                   class="form-control js-format-int @error('cv') is-invalid @enderror"
                                   id="cv"
                                   name="cv"
                                   value="{{ old('cv') }}"
                                   min="0">
                            @error('cv')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        {{-- combustible → columna vehiculos.combustible --}}
                        <div class="mb-3">
                            <label for="combustible" class="form-label">Combustible</label>
                            <select class="form-select @error('combustible') is-invalid @enderror"
                                    id="combustible"
                                    name="combustible">
                                <option value="">Selecciona tipo</option>
                                <option value="Gasolina" {{ old('combustible') === 'Gasolina' ? 'selected' : '' }}>
                                    Gasolina
                                </option>
                                <option value="Diésel" {{ old('combustible') === 'Diésel' ? 'selected' : '' }}>
                                    Diésel
                                </option>
                                <option value="Híbrido" {{ old('combustible') === 'Híbrido' ? 'selected' : '' }}>
                                    Híbrido
                                </option>
                                <option value="Eléctrico" {{ old('combustible') === 'Eléctrico' ? 'selected' : '' }}>
                                    Eléctrico
                                </option>
                            </select>
                            @error('combustible')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- etiqueta → columna vehiculos.etiqueta --}}
                        <div class="mb-3">
                            <label for="etiqueta" class="form-label">Etiqueta Ambiental</label>
                            <select class="form-select @error('etiqueta') is-invalid @enderror"
                                    id="etiqueta"
                                    name="etiqueta">
                                <option value="">Selecciona etiqueta</option>
                                <option value="0" {{ old('etiqueta') === '0' ? 'selected' : '' }}>0</option>
                                <option value="ECO" {{ old('etiqueta') === 'ECO' ? 'selected' : '' }}>ECO</option>
                                <option value="C" {{ old('etiqueta') === 'C' ? 'selected' : '' }}>C</option>
                                <option value="B" {{ old('etiqueta') === 'B' ? 'selected' : '' }}>B</option>
                                <option value="No tiene" {{ old('etiqueta') === 'No tiene' ? 'selected' : '' }}>
                                    No tiene
                                </option>
                            </select>
                            @error('etiqueta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- fecha_compra → columna vehiculos.fecha_compra --}}
                        <div class="mb-3">
                            <label for="fecha_compra" class="form-label">Fecha de Compra</label>
                            <input type="date"
                                   class="form-control @error('fecha_compra') is-invalid @enderror"
                                   id="fecha_compra"
                                   name="fecha_compra"
                                   value="{{ old('fecha_compra') }}">
                            @error('fecha_compra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- precio → columna vehiculos.precio --}}
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio (€)</label>
                            <input type="text"
                                   inputmode="decimal"
                                   class="form-control js-format-money @error('precio') is-invalid @enderror"
                                   id="precio"
                                   name="precio"
                                   value="{{ old('precio') }}">
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- precio_segunda_mano → columna vehiculos.precio_segunda_mano --}}
                        <div class="mb-3">
                            <label for="precio_segunda_mano" class="form-label">Precio de Segunda Mano (€)</label>
                            <input type="text"
                                   inputmode="decimal"
                                   class="form-control js-format-money @error('precio_segunda_mano') is-invalid @enderror"
                                   id="precio_segunda_mano"
                                   name="precio_segunda_mano"
                                   value="{{ old('precio_segunda_mano') }}">
                            @error('precio_segunda_mano')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- car_avatar → columna vehiculos.car_avatar --}}
                        <div class="mb-3">
                            <label for="car_avatar" class="form-label">Imagen de Vehículo</label>
                            <input type="file"
                                   id="car_avatar"
                                   name="car_avatar"
                                   class="form-control @error('car_avatar') is-invalid @enderror"
                                   accept="image/*">
                            @error('car_avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary me-2">Guardar Vehículo</button>
                    <a href="{{ route('perfil') }}" class="btn btn-gold">Volver</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/vehiculo/vehiculo.js') }}"></script>
    <script src="{{ asset('assets/js/vehiculo/matriculaFormato.js') }}"></script>
    <script src="{{ asset('assets/js/vehiculo/matriculacion.js') }}"></script>

    <script>
        // VEHICLE_DATA se cargará desde vehiculos.json
        let VEHICLE_DATA = {};

        // Helpers para modelos y datos
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

            const oldMarca = "{{ old('marca') }}";
            const oldModelo = "{{ old('modelo') }}";

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

            marcaSelect.addEventListener('change', () => {
                updateModels();
                if (anioFabInput) anioFabInput.value = "";
                if (precioInput) precioInput.value = "";
            });

            modeloSelect.addEventListener('change', () => {
                const brand = marcaSelect.value;
                const model = modeloSelect.value;
                const info = findModelInfo(brand, model);
                if (!info) return;

                // Rellenar automáticamente Año Fabricación desde el JSON
                if (anioFabInput && !anioFabInput.value && info.anio_fabricacion) {
                    anioFabInput.value = info.anio_fabricacion;
                }

                // Rellenar Precio original si no hay valor aún
                if (precioInput && !precioInput.value && info.precio_original_eur) {
                    precioInput.value = info.precio_original_eur;
                }
            });

            loadVehicleData();
        });
    </script>

</body>

</html>
