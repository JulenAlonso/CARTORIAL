@extends('layouts.app')

{{-- Indicamos al layout que esta página es full width --}}
@section('fullwidth', true)

@section('content')
    <style>
        /* ============================= */
        /*      FULL PAGE + MODERN UI    */
        /* ============================= */
        .ayuda-wrapper {
            width: 100vw;
            height: calc(100vh - 70px);
            background: #f3f4f6;
            padding: 24px 40px;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .ayuda-container {
            width: 100%;
            margin: 0 auto;
            flex: 1;
        }

        /* BOTÓN VOLVER */
        .btn-volver {
            background: #e5edff;
            color: #1d4ed8;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid #c7d2fe;
            transition: 0.2s ease-in-out;
            display: inline-block;
        }

        .btn-volver:hover {
            background: #dbe4ff;
            border-color: #a5b4fc;
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

        .ayuda-icon {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            background: linear-gradient(135deg, #0d6efd, #38bdf8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            box-shadow: 0 6px 18px rgba(37, 99, 235, .35);
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
    </style>

    <div class="ayuda-wrapper">
        <div class="ayuda-container">

            <!-- HEADER -->
            <div class="ayuda-header">

                <!-- BOTÓN VOLVER -->
                <a href="{{ url()->previous() }}" class="btn-volver">← Volver</a>

                <div class="ayuda-title-block">
                    <div class="ayuda-icon">?</div>
                    <div>
                        <h1 class="ayuda-title">Centro de Ayuda</h1>
                        <p class="ayuda-subtitle">Soluciona dudas, contacta soporte y consulta talleres cercanos.</p>
                    </div>
                </div>

                <div class="ayuda-header-right">
                    <span>🔐 Soporte exclusivo para usuarios</span><br>
                    <small>Si necesitas ayuda, escribe a <strong>soporte@autocontrol.com</strong></small>
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

                        <p><strong>Email soporte:</strong> soporte@autocontrol.com</p>
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
                                <div class="faq-answer">Sí, desde la sección de Notas y Calendario.</div>
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
                            <!-- GOOGLE MAPS EMBED -->
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d193812.9805710052!2d-3.7853302095096764!3d40.62265186891201!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1staller%20mec%C3%A1nico%2C%20toda%20Espa%C3%B1a!5e0!3m2!1ses!2ses!4v1763328370668!5m2!1ses!2ses"
                                allowfullscreen
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
