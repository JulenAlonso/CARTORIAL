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
    {{ Auth::user()->user_name }}

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

            <form id="vehiculo-form" method="POST" action="{{ route('vehiculo.store') }}" enctype="multipart/form-data"
                novalidate>
                @csrf

                <div class="row g-4">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control @error('matricula') is-invalid @enderror"
                                id="matricula" name="matricula" value="{{ old('matricula') }}" required>
                            @error('matricula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" class="form-control @error('marca') is-invalid @enderror"
                                id="marca" name="marca" value="{{ old('marca') }}" required>
                            @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control @error('modelo') is-invalid @enderror"
                                id="modelo" name="modelo" value="{{ old('modelo') }}" required>
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="anio" class="form-label">Año</label>
                            <input type="text" inputmode="numeric"
                                class="form-control js-format-int @error('anio') is-invalid @enderror" id="anio"
                                name="anio" value="{{ old('anio') }}" required>
                            @error('anio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="km" class="form-label">Kilometraje</label>
                            <input type="text" inputmode="numeric"
                                class="form-control js-format-int @error('km') is-invalid @enderror" id="km"
                                name="km" value="{{ old('km') }}" required>
                            @error('km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cv" class="form-label">CV</label>
                            <input type="text" inputmode="numeric"
                                class="form-control js-format-int @error('cv') is-invalid @enderror" id="cv"
                                name="cv" value="{{ old('cv') }}" required>
                            @error('cv')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="combustible" class="form-label">Combustible</label>
                            <select class="form-select @error('combustible') is-invalid @enderror" id="combustible"
                                name="combustible" required>
                                <option value="">Selecciona tipo</option>
                                <option value="Gasolina" {{ old('combustible') === 'Gasolina' ? 'selected' : '' }}>
                                    Gasolina</option>
                                <option value="Diésel" {{ old('combustible') === 'Diésel' ? 'selected' : '' }}>Diésel
                                </option>
                                <option value="Híbrido" {{ old('combustible') === 'Híbrido' ? 'selected' : '' }}>Híbrido
                                </option>
                                <option value="Eléctrico" {{ old('combustible') === 'Eléctrico' ? 'selected' : '' }}>
                                    Eléctrico</option>
                            </select>
                            @error('combustible')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="etiqueta" class="form-label">Etiqueta Ambiental</label>
                            <select class="form-select @error('etiqueta') is-invalid @enderror" id="etiqueta"
                                name="etiqueta" required>
                                <option value="">Selecciona etiqueta</option>
                                <option value="0" {{ old('etiqueta') === '0' ? 'selected' : '' }}>0</option>
                                <option value="ECO" {{ old('etiqueta') === 'ECO' ? 'selected' : '' }}>ECO</option>
                                <option value="C" {{ old('etiqueta') === 'C' ? 'selected' : '' }}>C</option>
                                <option value="B" {{ old('etiqueta') === 'B' ? 'selected' : '' }}>B</option>
                                <option value="No tiene" {{ old('etiqueta') === 'No tiene' ? 'selected' : '' }}>No tiene
                                </option>
                            </select>
                            @error('etiqueta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_compra" class="form-label">Fecha de Compra</label>
                            <input type="date" class="form-control @error('fecha_compra') is-invalid @enderror"
                                id="fecha_compra" name="fecha_compra" value="{{ old('fecha_compra') }}" required>
                            @error('fecha_compra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio (€)</label>
                            <input type="text" inputmode="decimal"
                                class="form-control js-format-money @error('precio') is-invalid @enderror"
                                id="precio" name="precio" value="{{ old('precio') }}" required>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="precio_segunda_mano" class="form-label">Precio de Segunda Mano (€)</label>
                            <input type="text" inputmode="decimal"
                                class="form-control js-format-money @error('precio_segunda_mano') is-invalid @enderror"
                                id="precio_segunda_mano" name="precio_segunda_mano"
                                value="{{ old('precio_segunda_mano') }}">
                            @error('precio_segunda_mano')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="car_avatar" class="form-label">Imagen de Vehículo</label>
                            <input type="file" id="car_avatar" name="car_avatar"
                                class="form-control @error('car_avatar') is-invalid @enderror" accept="image/*">
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

    <!-- Formateo en vivo y normalización en submit -->
    <script>
        (function() {
            const fmt = new Intl.NumberFormat('de-DE'); // 1.234.567,89

            function formatInt(val) {
                const digits = (val || '').replace(/\D+/g, '');
                if (!digits) return '';
                return fmt.format(parseInt(digits, 10));
            }

            function formatMoney(val) {
                val = (val || '').replace(/[^\d,]/g, '');
                const parts = val.split(',');
                const intDigits = (parts[0] || '').replace(/\D+/g, '');
                let out = intDigits ? fmt.format(parseInt(intDigits, 10)) : '';
                if (parts.length > 1) {
                    const dec = (parts[1] || '').replace(/\D+/g, '');
                    out += ',' + dec;
                }
                return out;
            }

            function withCaret(el, formatter) {
                el.value = formatter(el.value);
                const len = el.value.length;
                try {
                    el.setSelectionRange(len, len);
                } catch (_) {}
            }

            function bindLiveFormat(selector, formatter) {
                document.querySelectorAll(selector).forEach(el => {
                    el.addEventListener('input', () => withCaret(el, formatter));
                    el.addEventListener('blur', () => {
                        el.value = formatter(el.value);
                    });
                });
            }

            bindLiveFormat('.js-format-int', formatInt);
            bindLiveFormat('.js-format-money', formatMoney);

            // Normalizar antes de enviar
            const form = document.getElementById('vehiculo-form');
            if (form) {
                form.addEventListener('submit', () => {
                    const currentMaxYear = new Date().getFullYear() + 1;

                    form.querySelectorAll('.js-format-int').forEach(el => {
                        const raw = (el.value || '').replace(/\./g, '');
                        let num = raw ? parseInt(raw, 10) : '';
                        if (el.id === 'anio' && raw) {
                            if (num < 1886) num = 1886;
                            if (num > currentMaxYear) num = currentMaxYear;
                        }
                        if (el.id === 'km' && raw && num < 0) num = 0;
                        if (el.id === 'cv' && raw && num < 1) num = 1;
                        el.value = raw ? String(num) : '';
                    });

                    form.querySelectorAll('.js-format-money').forEach(el => {
                        el.value = (el.value || '').replace(/\./g, '').replace(',', '.');
                    });
                });
            }
        })();
    </script>
</body>

</html>
