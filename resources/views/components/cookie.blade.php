{{-- ================================= --}}
{{--   MODAL COOKIES CARTORIAL        --}}
{{--   (3 pestañas + switches)        --}}
{{-- ================================= --}}

<style>
/* ============================= */
/*            GLOBAL             */
/* ============================= */

.hidden { display: none !important; }

.cookie-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.cookie-box {
    background: #ffffff;
    border-radius: 14px;
    width: 95%;
    max-width: 900px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.18);
    overflow: hidden;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    border: 1px solid #dc2626;
}

/* ============================= */
/*            HEADER             */
/* ============================= */

.cookie-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid #e5e7eb;
}

.cookie-brand {
    display: flex;
    align-items: center;
    gap: 8px;
}

.cookie-brand img {
    height: 26px;
}

.cookie-brand span {
    font-weight: 700;
    font-size: 15px;
}

.cookie-powered {
    font-size: 12px;
    color: #6b7280;
}

/* ============================= */
/*            TABS               */
/* ============================= */

.cookie-tabs {
    display: flex;
    border-bottom: 1px solid #e5e7eb;
}

.cookie-tab {
    flex: 1;
    padding: 12px 16px;
    text-align: center;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    border: 1px solid #dc2626;
    background: #fff;
    color: #4b5563;
    transition: 0.15s ease;
}

.cookie-tab:hover {
    background:  #fee2e2;
}

.cookie-tab.active {
    color: #dc2626;               /* rojo estilo ejemplo */
    border-bottom-color: #dc2626;
}

/* ============================= */
/*            BODY               */
/* ============================= */

.cookie-body {
    padding: 18px 22px 10px;
    max-height: 360px;
    overflow-y: auto;
    font-size: 14px;
    color: #111827;
}

.cookie-panel { display: none; }
.cookie-panel.active { display: block; }

.cookie-panel h4 {
    font-size: 16px;
    margin: 0 0 8px 0;
    font-weight: 700;
}

.cookie-panel p {
    margin-bottom: 10px;
    line-height: 1.5;
}

/* ============================= */
/*      CATEGORÍAS / SWITCHES   */
/* ============================= */

.cookie-categories {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin-top: 22px;
    border-top: 1px solid #e5e7eb;
}

.cookie-category {
    padding: 16px 10px;
    text-align: center;
    border-left: 1px solid #e5e7eb;
}
.cookie-category:first-child { border-left: none; }

.cookie-category-title {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 10px;
}

/* Switch */
.cookie-switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
}

.cookie-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.cookie-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #e5e7eb;
    border-radius: 9999px;
    transition: 0.2s;
}

.cookie-slider::before {
    content: "";
    position: absolute;
    height: 18px;
    width: 18px;
    left: 4px;
    top: 3px;
    background-color: #ffffff;
    border-radius: 9999px;
    transition: 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}

.cookie-switch input:checked + .cookie-slider {
    background-color: #111827;
}

.cookie-switch input:checked + .cookie-slider::before {
    transform: translateX(20px);
}

.cookie-switch input:disabled + .cookie-slider {
    background-color: #9ca3af;
    cursor: not-allowed;
}

/* ============================= */
/*            FOOTER             */
/* ============================= */

.cookie-footer {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    border-top: 1px solid #e5e7eb;
}

.cookie-action-btn {
    padding: 12px 10px;
    border: 1px solid #dc2626;
    background: #ffffff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.15s ease;
    border-radius: 0;
}

.cookie-action-btn + .cookie-action-btn {
    border-left: none;
}

.cookie-action-btn:hover {
    background: #fee2e2;
}

.cookie-action-primary {
    background: #dc2626;
    color: #ffffff;
}

.cookie-action-primary:hover {
    background: #b91c1c;
}
</style>

<div id="cookieModal" class="cookie-modal hidden">
    <div class="cookie-box">
        {{-- Header con logo --}}
        <div class="cookie-top">
            <div class="cookie-brand">
                <img src="{{ asset('assets/images/CARTORIAL2.png') }}" alt="Cartorial">
                <span>CARTORIAL</span>
            </div>
            <div class="cookie-powered">
                Centro de preferencias de cookies
            </div>
        </div>

        {{-- Tabs --}}
        <div class="cookie-tabs">
            <button class="cookie-tab active" data-tab="consent">Consentimiento</button>
            <button class="cookie-tab" data-tab="details">Detalles</button>
            <button class="cookie-tab" data-tab="about">Acerca de las cookies</button>
        </div>

        {{-- Body --}}
        <div class="cookie-body">
            {{-- TAB 1: CONSENTIMIENTO --}}
            <div id="cookie-panel-consent" class="cookie-panel active">
                <h4>Esta página utiliza cookies</h4>
                <p>
                    En <strong>Cartorial</strong> usamos cookies para que la experiencia dentro de la plataforma sea
                    más cómoda y útil: adaptamos el contenido, mejoramos las funciones disponibles y analizamos
                    cómo se utiliza el sitio para seguir optimizándolo.
                </p>
                <p>
                    También colaboramos con proveedores de analítica, publicidad y redes sociales que, si lo permites,
                    pueden combinar algunos datos de uso con otra información que ya tengan o hayan obtenido a través
                    de los servicios que ofrecen.
                </p>
                <p>
                    Tú tienes el control: puedes aceptar todas las cookies, limitar su uso o quedarte solo con las
                    estrictamente necesarias.
                </p>

                {{-- Categorías + switches (se muestran en esta pestaña) --}}
                <div class="cookie-categories">
                    <div class="cookie-category">
                        <div class="cookie-category-title">Necesarias</div>
                        <label class="cookie-switch">
                            <input type="checkbox" id="cookie-necessary" checked disabled>
                            <span class="cookie-slider"></span>
                        </label>
                    </div>
                    <div class="cookie-category">
                        <div class="cookie-category-title">Preferencias</div>
                        <label class="cookie-switch">
                            <input type="checkbox" id="cookie-preferences">
                            <span class="cookie-slider"></span>
                        </label>
                    </div>
                    <div class="cookie-category">
                        <div class="cookie-category-title">Estadística</div>
                        <label class="cookie-switch">
                            <input type="checkbox" id="cookie-statistics">
                            <span class="cookie-slider"></span>
                        </label>
                    </div>
                    <div class="cookie-category">
                        <div class="cookie-category-title">Marketing</div>
                        <label class="cookie-switch">
                            <input type="checkbox" id="cookie-marketing">
                            <span class="cookie-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- TAB 2: DETALLES --}}
            <div id="cookie-panel-details" class="cookie-panel">
                <h4>Detalles de los tipos de cookies</h4>

                <p><strong>Cookies necesarias</strong><br>
                    Son imprescindibles para que <strong>Cartorial</strong> funcione correctamente. Permiten, por ejemplo,
                    navegar por la web, iniciar sesión, mantener la sesión activa y acceder a zonas seguras de la
                    plataforma. Sin ellas, muchos servicios básicos dejarían de estar disponibles.
                </p>

                <p><strong>Cookies de preferencias</strong><br>
                    Sirven para recordar ajustes que mejoran tu experiencia, como el idioma de la interfaz,
                    ciertas configuraciones visuales o la región desde la que accedes. Gracias a estas cookies,
                    Cartorial puede adaptarse mejor a tu forma de usar la aplicación.
                </p>

                <p><strong>Cookies estadísticas</strong><br>
                    Nos ayudan a entender de forma agregada y anónima cómo interactúan los usuarios con el sitio:
                    qué apartados se visitan más, cuánto tiempo se permanece en ellos o qué funciones resultan más
                    útiles. La información que recogen se utiliza exclusivamente para mejorar el rendimiento y
                    la usabilidad de Cartorial.
                </p>

                <p><strong>Cookies de marketing</strong><br>
                    Se utilizan para mostrar contenido y campañas más acordes con tus intereses, así como para medir
                    la efectividad de nuestra comunicación. También pueden emplearse para que proveedores externos
                    adapten los anuncios que muestran en otros sitios a partir de tu actividad.
                </p>

                <p><strong>Cookies no clasificadas</strong><br>
                    Son cookies que aún estamos revisando y categorizando junto con los proveedores que las generan,
                    para asegurar que su uso sea transparente y acorde a su finalidad.
                </p>

                <p style="font-size: 12px; color:#6b7280;">
                    Información sobre cookies revisada por última vez el 25/11/2025.
                </p>
            </div>

            {{-- TAB 3: ACERCA DE LAS COOKIES --}}
            <div id="cookie-panel-about" class="cookie-panel">
                <h4>¿Qué son las cookies y cómo las usamos?</h4>

                <p>
                    Las cookies son pequeños archivos que los sitios web almacenan en tu dispositivo para recordar
                    cierta información y hacer que la navegación sea más eficiente y personalizada.
                </p>
                <p>
                    En <strong>Cartorial</strong> solo instalamos sin tu permiso aquellas cookies que son estrictamente
                    necesarias para que la plataforma funcione. Para el resto de categorías —como preferencias,
                    estadísticas o marketing— te pedimos consentimiento explícito.
                </p>
                <p>
                    Algunos servicios de terceros integrados en Cartorial pueden establecer sus propias cookies
                    cuando se muestran en nuestras páginas (por ejemplo, contenidos incrustados o herramientas de analítica).
                </p>
                <p>
                    Puedes modificar o retirar tu consentimiento en cualquier momento accediendo de nuevo al
                    panel de configuración de cookies disponible en nuestro sitio.
                </p>
                <p>
                    Si deseas más información sobre cómo tratamos tus datos personales, cómo ejercer tus derechos
                    o cómo contactar con nosotros, puedes consultar nuestra Política de Privacidad.
                </p>
                <p>
                    Si nos escribes en relación con tu consentimiento de cookies, indícanos el identificador y la fecha
                    del mismo (si los tienes disponibles) para poder ayudarte con mayor rapidez.
                </p>
            </div>
        </div>

        {{-- Footer botones --}}
        <div class="cookie-footer">
            <button id="cookie-reject" class="cookie-action-btn">
                Denegar
            </button>
            <button id="cookie-allow-selection" class="cookie-action-btn">
                Permitir la selección
            </button>
            <button id="cookie-allow-all" class="cookie-action-btn cookie-action-primary">
                Permitir todas
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("cookieModal");

    const tabs = document.querySelectorAll(".cookie-tab");
    const panels = {
        consent: document.getElementById("cookie-panel-consent"),
        details: document.getElementById("cookie-panel-details"),
        about: document.getElementById("cookie-panel-about"),
    };

    const pref = document.getElementById("cookie-preferences");
    const stat = document.getElementById("cookie-statistics");
    const mark = document.getElementById("cookie-marketing");

    const btnReject = document.getElementById("cookie-reject");
    const btnSelect = document.getElementById("cookie-allow-selection");
    const btnAll = document.getElementById("cookie-allow-all");

    const STORAGE_KEY = "cartorial_cookie_settings";

    function openModalIfNeeded() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (!saved) {
            modal.classList.remove("hidden");
        } else {
            // Rellenar switches según lo guardado
            try {
                const cfg = JSON.parse(saved);
                pref.checked = !!cfg.preferences;
                stat.checked = !!cfg.statistics;
                mark.checked = !!cfg.marketing;
            } catch (e) {
                modal.classList.remove("hidden");
            }
        }
    }

    function saveSettings(options) {
        const payload = {
            necessary: true,
            preferences: !!options.preferences,
            statistics: !!options.statistics,
            marketing: !!options.marketing,
            consentDate: new Date().toISOString(),
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    }

    // Tabs
    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");

            const target = tab.getAttribute("data-tab");
            Object.keys(panels).forEach(key => {
                panels[key].classList.toggle("active", key === target);
            });
        });
    });

    // Botón "Denegar": solo necesarias
    btnReject.addEventListener("click", () => {
        pref.checked = false;
        stat.checked = false;
        mark.checked = false;
        saveSettings({
            preferences: false,
            statistics: false,
            marketing: false
        });
        modal.classList.add("hidden");
    });

    // Botón "Permitir la selección": respeta switches
    btnSelect.addEventListener("click", () => {
        saveSettings({
            preferences: pref.checked,
            statistics: stat.checked,
            marketing: mark.checked
        });
        modal.classList.add("hidden");
    });

    // Botón "Permitir todas": activa todo
    btnAll.addEventListener("click", () => {
        pref.checked = true;
        stat.checked = true;
        mark.checked = true;
        saveSettings({
            preferences: true,
            statistics: true,
            marketing: true
        });
        modal.classList.add("hidden");
    });

    openModalIfNeeded();
});
</script>
