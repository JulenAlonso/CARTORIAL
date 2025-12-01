<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/style/login/Login.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/style/login/mostrarPassword.css') }}">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <section class="h-100 gradient-form" style="background-color: #eee;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">

                                    <div class="text-center">
                                        <img src="{{ asset('assets/images/CARTORIAL1.png') }}" style="width: 185px;"
                                            alt="logo">
                                        <p></p>
                                        <h4 class="mt-1 mb-5 pb-1">LOGIN</h4>
                                    </div>

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <p>Introduce tu usuario y contraseña</p>

                                        <div class="form-outline mb-4">
                                            <input type="email" name="email" class="form-control"
                                                placeholder="Correo electrónico" required autofocus />
                                        </div>

                                        <!-- ============================= -->
                                        <!--       INPUT PASSWORD          -->
                                        <!-- ============================= -->
                                        <div class="form-outline mb-4 password-wrapper">
                                            <input type="password" name="password" id="passwordInput"
                                                class="form-control" placeholder="Contraseña" required />
                                            <i class="bi bi-eye toggle-password" id="togglePassword"></i>
                                        </div>

                                        <div class="text-center pt-1 mb-5 pb-1">
                                            <button type="submit"
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3">
                                                Iniciar Sesión
                                            </button>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">¿No tienes cuenta?</p>
                                            <a href="{{ route('register') }}"
                                                class="btn btn-outline-danger">Regístrate</a>
                                        </div>
                                    </form>

                                    @include('components.loadingLogin')

                                    <a href="{{ Auth::check() ? route('perfil') : url('/') }}" class="btn btn-volver">
                                        Volver al Inicio
                                    </a>

                                </div>
                            </div>

                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">
                                        Lleva el control total de tu auto: registra gastos, mantenimiento y kilometraje.
                                        <br><br>
                                        Descubre reportes inteligentes y mantén tu vehículo siempre listo con CARTORIAL.
                                    </h4>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script mostrar/ocultar contraseña -->
<script src="{{ asset('./assets/js/login/mostrarPassword.js') }}"></script>
<!-- Bootstrap Icons (para el icono del ojo) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

</html>
