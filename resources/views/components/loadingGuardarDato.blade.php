<div class="loader-overlay js-loader-guardar-dato">
    <div class="loader-box">
        <div class="custom-loader"></div>
        <p class="loader-text">Actualizando datos…</p>
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

    .loader-box {
        background: #ffffffee;
        padding: 25px 35px;
        border-radius: 14px;
        border: 2px solid #b3c7ff;
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
        color: #0D47A1;
        font-family: system-ui, sans-serif;
    }

    .custom-loader {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        color: transparent;
        background:
            linear-gradient(to right, #1976D2, #0D47A1) center/100% 8px,
            linear-gradient(to right, #1976D2, #0D47A1) center/8px 100%,
            radial-gradient(farthest-side, #0000 calc(100% - 12px), #1976D2 calc(100% - 10px)),
            radial-gradient(circle 12px, #0D47A1 94%, #0000 0);
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
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Para cada loader de tipo "guardar dato"
        document.querySelectorAll('.js-loader-guardar-dato').forEach(loader => {
            const form = loader.closest('form'); // 🔥 solo su form

            if (!form) return;

            form.addEventListener('submit', (e) => {
                e.preventDefault();

                loader.style.display = 'flex';

                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Actualizando datos…';
                }

                setTimeout(() => {
                    form.submit();
                }, 1000);
            });
        });
    });
</script>
