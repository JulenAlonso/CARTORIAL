<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>CARTORIAL - Inicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/images/CARTORIAL2.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/style/Inicio.css') }}">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <div class="me-2">
                <img src="{{ asset('assets/images/CARTORIAL2.png') }}" alt="CARTORIAL" height="40"
                    class="d-inline-block align-text-top">
            </div>
            <a class="navbar-brand" href="#">CARTORIAL</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Registrarse</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-text">
            <h1>Gestiona tu vehículo sin complicaciones</h1>
            <p>Con CARTORIAL, controla el mantenimiento, gastos y recordatorios de todos tus coches desde una sola
                plataforma.</p>
            <a href="{{ route('register') }}" class="btn btn-custom me-2">Comenzar</a>
            <a href="{{ route('login') }}" class="btn btn-custom me-2">Ya tengo cuenta</a>
        </div>
        <div class="hero-image">
            <img src="{{ asset('assets/images/taller.png') }}" alt="Gestión de vehículo">
        </div>
    </section>

    <!-- Sobre Nosotros -->
    <section class="about">
        <h2>Quiénes somos</h2>
        <div class="about-content">
            <p>
                En <strong>CARTORIAL</strong> somos una plataforma creada por entusiastas del motor y la tecnología, con
                un
                objetivo claro: simplificar la gestión de tus vehículos.
                Sabemos que llevar un control de mantenimientos, revisiones e impuestos puede ser tedioso, por eso
                desarrollamos una herramienta intuitiva que te permite organizar todo desde un solo lugar.
                Nuestra misión es ayudarte a ahorrar tiempo, dinero y preocupaciones, manteniendo tu coche siempre al
                día
                con un solo clic.
            </p>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <h2>Características principales</h2>
        <div class="row justify-content-center g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-car-front"></i>
                    <h5>Gestión de vehículos</h5>
                    <p>Añade, edita y consulta todos tus coches de forma rápida y sencilla.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-calendar-check"></i>
                    <h5>Recordatorios automáticos</h5>
                    <p>Recibe alertas cuando se acerquen mantenimientos o vencimientos importantes.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-graph-up"></i>
                    <h5>Control de gastos</h5>
                    <p>Visualiza y analiza los costos de mantenimiento y consumo de tu vehículo.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta">
        <h2>Empieza a gestionar tu coche hoy mismo</h2>
        <p>Regístrate gratis y lleva un control total de tus vehículos.</p>
        <a href="{{ route('register') }}">Registrarme</a>
    </section>
    <!-- Footer -->
    <footer>
        &copy; 2025 CARTORIAL. Todos los derechos reservados.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/inicio.js') }}"></script>
    @include('components.cookie')


</body>

</html>
