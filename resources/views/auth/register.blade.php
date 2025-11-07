<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/style/Register.css') }}">
    <!-- Bootstrap 5 CDN -->
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
                                    <p></p>
                                    <h4 class="mt-1 mb-5 pb-1">REGISTRO</h4>
                                </div>

                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <p>Introduce tus datos</p>

                                    <div class="row">
                                        <!-- Columna izquierda -->
                                        <div class="col-md-6">
                                            <!-- Nombre -->
                                            <label for="reg-nombre">Nombre</label>
                                            <div class="form-outline mb-3">
                                                <input type="text" id="reg-nombre" name="nombre"
                                                    class="form-control" placeholder="Nombre" required />
                                            </div>

                                            <!-- Apellidos -->
                                            <label for="reg-apellidos">Apellidos</label>
                                            <div class="form-outline mb-3">
                                                <input type="text" id="reg-apellidos" name="apellidos"
                                                    class="form-control" placeholder="Apellidos" required />
                                            </div>

                                        </div>

                                        <!-- Columna derecha -->
                                        <div class="col-md-6">
                                            <!-- Usuario -->
                                            <label for="reg-username">Usuario</label>
                                            <div class="form-outline mb-3">
                                                <input type="text" id="reg-username" name="user_name"
                                                    class="form-control" placeholder="Usuario" required />
                                            </div>

                                            <!-- Email -->
                                            <label for="reg-email">Correo electrónico</label>
                                            <div class="form-outline mb-3">
                                                <input type="email" id="reg-email" name="email" class="form-control"
                                                    placeholder="tu@correo.com" required />
                                            </div>

                                        </div>
                                        <div>
                                            <label for="telefono">Telefono</label>
                                            <input id="telefono" type="tel" name="telefono" class="form-control">
                                        </div>

                                        <div>
                                            <label for="password">Contraseña</label>
                                            <input id="password" type="password" name="password" class="form-control"
                                                required>
                                        </div>

                                        <div>
                                            <label for="password_confirmation">Confirmar contraseña</label>
                                            <input id="password_confirmation" type="password"
                                                name="password_confirmation" class="form-control" required>
                                        </div>

                                        <div>
                                            <label for="user_avatar">Foto de perfil</label>
                                            <input id="user_avatar" type="file" name="user_avatar"
                                                accept="image/* class=" form-control>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="text-center pt-1 mb-5 pb-1">
                                        <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                            type="submit" style="border: 0px;">Crear Cuenta</button>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-center pb-4">
                                        <p class="mb-0 me-2">¿Ya tienes cuenta?</p>
                                        <a href="{{ route('login') }}" class="btn btn-outline-danger">Login</a>
                                    </div>
                                </form>

                                <a href="{{ Auth::check() ? route('home') : url('/') }}" class="btn btn-volver">Volver
                                    al Inicio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

<!-- Bootstrap JS  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
