@extends('layouts.app')

{{-- Indicamos al layout que esta página es full width --}}
@section('fullwidth', true)

@section('content')
    {{-- Bootstrap Icons para los iconos de RRSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --gold: #facc15;
            /* amarillo dorado */
            --gold-2: #f97316;
            /* naranja dorado */
        }

        /* Ocultar navbar del layout */
        nav {
            display: none !important;
        }

        /* ============================= */
        /*         BASE / SCROLL         */
        /* ============================= */
        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f3f4f6;

            /* Sobrescribe el layout en grid del perfil */
            display: block !important;
            grid-template-columns: none !important;
        }

        main {
            padding: 0 !important;
            margin: 0 !important;
            background: none !important;
        }

        .py-4 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        /* ============================= */
        /*      FULL PAGE + MODERN UI    */
        /* ============================= */

        .ayuda-wrapper {
            min-height: 100vh;
            /* ocupa como mínimo toda la altura */
            display: flex;
            flex-direction: column;
            /* para poder “pegar” el footer abajo */
            box-sizing: border-box;
            width: 100%;
        }

        .ayuda-container {
            width: 100%;
            /* 🔹 ocupa todo el ancho */
            max-width: 100%;
            /* 🔹 sin límite centrado */
            margin: 0 0 24px 0;
            /* sólo separación inferior con el footer */
            flex: 1 0 auto;
            /* ocupa el espacio disponible encima del footer */
            padding: 24px 40px 0;

        }

        /* ===== Botón volver ===== */
        .btn-gold {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-2) 100%);
            color: #1f2937;
            border: none;
            box-shadow: 0 8px 18px rgba(234, 179, 8, 0.2);
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-gold:hover {
            filter: brightness(1.05);
            color: #111827;
        }

        /* HEADER */
        .ayuda-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            width: 100%;
        }

        .ayuda-title-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ayuda-title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .ayuda-subtitle {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }

        .ayuda-header-right {
            text-align: right;
            font-size: 13px;
            color: #6b7280;
        }

        .ayuda-header-right span {
            display: inline-flex;
            padding: 6px 12px;
            background: #e0f2fe;
            border-radius: 999px;
            font-weight: 500;
        }

        /* GRID */
        .ayuda-grid {
            display: grid;
            grid-template-columns: 1.1fr 1.4fr;
            gap: 24px;
            width: 100%;
        }

        @media (max-width: 992px) {
            .ayuda-grid {
                grid-template-columns: 1fr;
            }
        }

        /* CARDS */
        .ayuda-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            position: relative;
        }

        .ayuda-card-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .ayuda-card-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 17px;
            font-weight: 700;
            color: #111827;
        }

        .ayuda-badge {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            letter-spacing: .05em;
            font-weight: 600;
        }

        /* FAQ */
        .faq-list {
            margin-top: 8px;
        }

        .faq-item {
            padding: 10px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            font-weight: 600;
            font-size: 15px;
            color: #111827;
        }

        .faq-answer {
            margin-top: 3px;
            font-size: 14px;
            color: #6b7280;
            line-height: 1.4;
        }

        /* MAPA */
        .map-wrapper {
            width: 100%;
            height: 450px;
            border-radius: 16px;
            overflow: hidden;
        }

        .map-wrapper iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* ======================= */
        /*         FOOTER          */
        /* ======================= */

        .ayuda-footer {
            width: 100%;
            /* 🔹 ocupa todo el ancho de la pantalla */
            background: #111827;
            color: #e5e7eb;
            padding: 16px 40px 10px;
            border-radius: 0;
            box-sizing: border-box;
            margin-top: auto;
            /* se queda pegado abajo si hay poca altura */
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            max-width: 1100px;
            margin: 0 auto;
        }

        @media (max-width: 992px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        .footer-section {
            display: flex;
            flex-direction: column;
        }

        .footer-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #facc15;
        }

        .footer-subtitle {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #f3f4f6;
        }

        .footer-text {
            font-size: 13px;
            color: #9ca3af;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 3px;
        }

        .footer-links a {
            font-size: 13px;
            text-decoration: none;
            color: #e5e7eb;
            transition: color .2s;
        }

        .footer-links a:hover {
            color: #facc15;
        }

        .footer-socials {
            display: flex;
            gap: 6px;
        }

        .social-icon {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e5e7eb;
            font-size: 14px;
            transition: all .2s ease;
        }

        .social-icon:hover {
            background: #facc15;
            color: #111827;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 12px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>

    <div class="ayuda-wrapper">
        <div class="ayuda-container">

            <!-- HEADER -->
            <div class="ayuda-header">

                <!-- BOTÓN VOLVER -->
                <a href="{{ route('perfil') }}" class="btn btn-gold">← Volver</a>

                <div class="ayuda-title-block">
                    <div>
                        <h1 class="ayuda-title"> ❓Centro de Ayuda</h1>
                        <p class="ayuda-subtitle">Soluciona dudas, contacta soporte y consulta talleres cercanos.</p>
                    </div>
                </div>

                <div class="ayuda-header-right">
                    <span>🔐 Soporte exclusivo para usuarios</span><br>
                    <small>Si necesitas ayuda, escribe a <strong>soporte@cartorial.com</strong></small>
                </div>
            </div>

            <!-- GRID -->
            <div class="ayuda-grid">
                <!-- IZQUIERDA -->
                <div>
                    <!-- CONTACTO -->
                    <div class="ayuda-card">
                        <div class="ayuda-card-header">
                            <div class="ayuda-card-title">📞 Contacto</div>
                            <span class="ayuda-badge">Soporte</span>
                        </div>

                        <p><strong>Email soporte:</strong> soporte@cartorial.com</p>
                        <p><strong>Teléfono:</strong> 600 123 456</p>
                        <p><strong>Horario:</strong> L–V 09:00–19:00</p>
                    </div>
                    <p></p>

                    <!-- FAQ -->
                    <div class="ayuda-card">
                        <div class="ayuda-card-header">
                            <div class="ayuda-card-title">ℹ️ Preguntas Frecuentes</div>
                            <span class="ayuda-badge">Guía</span>
                        </div>

                        <div class="faq-list">
                            <div class="faq-item">
                                <div class="faq-question">¿Cómo edito mi perfil?</div>
                                <div class="faq-answer">Desde la barra lateral en "Editar Perfil".</div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">¿Cómo añado un vehículo?</div>
                                <div class="faq-answer">Pulsa “Añadir Vehículo” en la barra lateral.</div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">¿Dónde veo mis kilómetros y gastos?</div>
                                <div class="faq-answer">En tu perfil verás tarjetas con el resumen.</div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">¿Puedo guardar notas o mantenimientos?</div>
                                <div class="faq-answer">Sí, desde la sección de Notas y Calendario en tu perfil.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DERECHA: MAPA -->
                <div>
                    <div class="ayuda-card">
                        <div class="ayuda-card-header">
                            <div class="ayuda-card-title">🛠️ Talleres cercanos</div>
                            <span class="ayuda-badge">Mapa</span>
                        </div>

                        <div class="map-wrapper">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d6053.17593290997!2d-3.768706204995342!3d40.66101221856797!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1staller%2C%20taller%20mecanico%2C%20auto%2C%20motor!5e0!3m2!1ses!2ses!4v1763579826062!5m2!1ses!2ses"
                                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <footer class="ayuda-footer">
            <div class="footer-content">

                <!-- Columna 1 - Marca -->
                <div class="footer-section">
                    <h3 class="footer-title">Cartorial</h3>
                    <p class="footer-text">
                        Tu asistente personal para gestionar vehículos, gastos, mantenimientos y mucho más.
                    </p>

                    <div class="footer-socials">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <!-- Columna 2 - Enlaces -->
                <div class="footer-section">
                    <h4 class="footer-subtitle">Navegación</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('perfil') }}">Mi Perfil</a></li>
                        <li><a href="{{ route('vehiculo.create') }}">Añadir Vehículo</a></li>
                        {{-- Antes: route('notas.index') que no existe --}}
                        <li><a href="{{ route('perfil') }}#panel-calendario">Notas y Mantenimientos</a></li>
                        <li><a href="#">Gastos</a></li>
                    </ul>
                </div>

                <!-- Columna 3 - Soporte -->
                <div class="footer-section">
                    <h4 class="footer-subtitle">Soporte</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('ayuda') }}">Centro de Ayuda</a></li>
                        <li><a href="mailto:soporte@cartorial.com">Correo soporte</a></li>
                        <li><a href="#">Estado del servicio</a></li>
                        <li><a href="#">Preguntas frecuentes</a></li>
                    </ul>
                </div>

                <!-- Columna 4 - Legal -->
                <div class="footer-section">
                    <h4 class="footer-subtitle">Legal</h4>
                    <ul class="footer-links">
                        <li><a href="#">Privacidad</a></li>
                        <li><a href="#">Términos de uso</a></li>
                        <li><a href="#">Cookies</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                © {{ date('Y') }} Cartorial — Todos los derechos reservados.
            </div>
        </footer>
    </div>
@endsection
