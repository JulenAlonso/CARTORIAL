<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/style/registroUsuario/Register.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/style/login/mostrarPassword.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-xl-10">
                <div class="card rounded-3 text-black">
                    <div class="row g-0">

                        <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                            <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                <h4 class="mb-4">
                                    Lleva el control total de tu vehículo: gastos, mantenimiento y kilometraje.
                                    <br><br>
                                    Mantén tu vehículo siempre listo con CARTORIAL.
                                </h4>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card-body p-md-5 mx-md-4">

                                <div class="text-center">
                                    <img src="{{ asset('assets/images/CARTORIAL1.png') }}" style="width: 185px;"
                                        alt="logo" class="logo">
                                    <h4 class="mt-1 mb-5 pb-1">REGISTRO</h4>
                                </div>

                                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                                    @csrf
                                    <p>Introduce tus datos</p>

                                    <div class="row">

                                        <!-- Columna izquierda -->
                                        <div class="col-md-6">

                                            <label for="reg-nombre">Nombre</label>
                                            <div class="form-outline mb-3">
                                                <input type="text" id="reg-nombre" name="nombre"
                                                    class="form-control" placeholder="Nombre" required />
                                            </div>

                                            <label for="reg-apellidos">Apellidos</label>
                                            <div class="form-outline mb-3">
                                                <input type="text" id="reg-apellidos" name="apellidos"
                                                    class="form-control" placeholder="Apellidos" required />
                                            </div>

                                        </div>

                                        <!-- Columna derecha -->
                                        <div class="col-md-6">

                                            <label for="reg-username">Usuario</label>
                                            <div class="form-outline mb-3">
                                                <input type="text" id="reg-username" name="user_name"
                                                    class="form-control" placeholder="Usuario" required />
                                            </div>

                                            <label for="reg-email">Correo electrónico</label>
                                            <div class="form-outline mb-3">
                                                <input type="email" id="reg-email" name="email" class="form-control"
                                                    placeholder="tu@correo.com" required />
                                            </div>

                                        </div>

                                        <div>
                                            <label for="telefono">Teléfono</label>
                                            <input id="telefono" type="tel" name="telefono" class="form-control">
                                        </div>

                                        <!-- PASSWORD -->
                                        <div class="mt-3">

                                            <label for="password">Contraseña</label>
                                            <div class="password-wrapper">
                                                <input id="password" type="password" name="password"
                                                    class="form-control" required>
                                                <i class="bi bi-eye toggle-password" data-target="password"></i>
                                            </div>

                                            <!-- CHECKLIST -->
                                            <div id="password-checklist"
                                                style="display: grid; grid-template-columns: 1fr 1fr; gap: 5px 20px; font-size:14px; margin-top:8px;">

                                                <li id="chk-8" style="color:red; list-style:none;">❌ Mínimo 8
                                                    caracteres</li>
                                                <li id="chk-lower" style="color:red; list-style:none;">❌ Al menos una
                                                    minúscula</li>

                                                <li id="chk-upper" style="color:red; list-style:none;">❌ Al menos una
                                                    mayúscula</li>
                                                <li id="chk-number" style="color:red; list-style:none;">❌ Al menos un
                                                    número</li>

                                                <li id="chk-symbol" style="color:red; list-style:none;">❌ Al menos 2
                                                    símbolos</li>
                                                <li id="chk-space" style="color:red; list-style:none;">❌ Sin espacios
                                                </li>

                                            </div>

                                            <label for="password_confirmation" class="mt-3">Confirmar
                                                contraseña</label>
                                            <div class="password-wrapper">
                                                <input id="password_confirmation" type="password"
                                                    name="password_confirmation" class="form-control" required>
                                                <i class="bi bi-eye toggle-password"
                                                    data-target="password_confirmation"></i>
                                            </div>

                                            <div class="mt-3">
                                                <label for="user_avatar">Foto de perfil</label>
                                                <input id="user_avatar" type="file" name="user_avatar"
                                                    accept="image/*" class="form-control">
                                            </div>

                                        </div>

                                        <br>

                                        <div class="text-center pt-1 mb-5 pb-1">
                                            <button id="btn-submit"
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="submit" style="border: 0px;" disabled>
                                                Crear Cuenta
                                            </button>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">¿Ya tienes cuenta?</p>
                                            <a href="{{ route('login') }}" class="btn btn-outline-danger">Login</a>
                                        </div>
                                </form>

                                @include('components.loadingRegister')

                                <a href="{{ Auth::check() ? route('home') : url('/') }}" class="btn btn-volver">
                                    Volver al Inicio
                                </a>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

<!-- Script validación contraseña -->
<script src="{{ asset('./assets/js/registroUsuario/password.js') }}"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons (icono del ojo) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- Script mostrar/ocultar contraseña -->
<script src="{{ asset('./assets/js/login/mostrarPassword.js') }}"></script>

</html>
