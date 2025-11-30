<div class="loader-overlay" id="loader-overlay">
    <div class="loader-box">
        <div class="custom-loader"></div>
        <p class="loader-text">Modificando vehiculo…</p>
    </div>
</div>

<style>
    .loader-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    /* Caja que envuelve el loader */
    .loader-box {
        background: #ffffffee;
        padding: 25px 35px;
        border-radius: 14px;
        border: 2px solid #d0d7ff;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        backdrop-filter: blur(6px);
        animation: fadeIn 0.3s ease;
    }

    .loader-text {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #233254;
        font-family: system-ui, sans-serif;
    }

    /* Loader original */
    .custom-loader {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        color: #766DF4;
        background:
            linear-gradient(currentColor 0 0) center/100% 8px,
            linear-gradient(currentColor 0 0) center/8px 100%,
            radial-gradient(farthest-side, #0000 calc(100% - 12px), currentColor calc(100% - 10px)),
            radial-gradient(circle 12px, currentColor 94%, #0000 0);
        background-repeat: no-repeat;
        animation: s10 1.1s infinite linear;
        position: relative;
    }

    .custom-loader::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: inherit;
        transform: rotate(45deg);
    }

    @keyframes s10 {
        to {
            transform: rotate(.5turn);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>

<script>
    // Hace que cualquier formulario muestre el loader
    document.addEventListener('DOMContentLoaded', () => {
        const loader = document.getElementById('loader-overlay');

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {

                // Mostrar loader
                loader.style.display = 'flex';

                // Prevenir doble clic
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerText = 'Procesando...';
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const loader = document.getElementById('loader-overlay');

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                // Evitar envío instantáneo
                e.preventDefault();

                // Mostrar loader
                loader.style.display = 'flex';

                // Bloquear botón de envío
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerText = 'Procesando...';
                }

                // Esperar 1 segundos antes de enviar el formulario
                setTimeout(() => {
                    form.submit(); // Aquí ya continúa a la siguiente vista
                }, 1000);
            });
        });
    });
</script>
