<div class="loader-overlay js-loader-logout">
    <div class="loader-box">
        <div class="custom-loader-logout"></div>
        <p class="loader-text-logout">Apagando motor...</p>
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
        border: 2px solid #ffb3b3;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        backdrop-filter: blur(6px);
        animation: fadeIn 0.3s ease;
    }

    .loader-text-logout {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #8a0000;
        font-family: system-ui, sans-serif;
    }

    .custom-loader-logout {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        color: #e63946;
        background:
            linear-gradient(currentColor 0 0) center/100% 8px,
            linear-gradient(currentColor 0 0) center/8px 100%,
            radial-gradient(farthest-side, #0000 calc(100% - 12px), currentColor calc(100% - 10px)),
            radial-gradient(circle 12px, currentColor 94%, #0000 0);
        background-repeat: no-repeat;
        animation: s10 1.1s infinite linear;
        position: relative;
    }

    .custom-loader-logout::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: inherit;
        transform: rotate(45deg);
    }

    @keyframes s10 {
        to { transform: rotate(.5turn); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.js-loader-logout').forEach(loader => {
            const form = loader.closest('form'); // 🔥 solo el form de logout

            if (!form) return;

            form.addEventListener('submit', (e) => {
                e.preventDefault();

                loader.style.display = 'flex';

                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Cerrando sesión...';
                }

                setTimeout(() => {
                    form.submit();
                }, 800);
            });
        });
    });
</script>
