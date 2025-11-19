(function () {
    const fmt = new Intl.NumberFormat('de-DE'); // 1.234.567,89

    function formatInt(val) {
        const digits = (val || '').replace(/\D+/g, '');
        if (!digits) return '';
        return fmt.format(parseInt(digits, 10));
    }

    function formatMoney(val) {
        val = (val || '').replace(/[^\d,]/g, ''); // solo dígitos y coma
        const parts = val.split(',');
        const intDigits = (parts[0] || '').replace(/\D+/g, '');
        let out = intDigits ? fmt.format(parseInt(intDigits, 10)) : '';
        if (parts.length > 1) {
            const dec = (parts[1] || '').replace(/\D+/g, '');
            out += ',' + dec;
        }
        return out;
    }

    function withCaret(el, formatter) {
        el.value = formatter(el.value);
        const len = el.value.length;
        try {
            el.setSelectionRange(len, len);
        } catch (_) { }
    }

    function bindLiveFormat(selector, formatter) {
        document.querySelectorAll(selector).forEach(el => {
            el.addEventListener('input', () => withCaret(el, formatter));
            el.addEventListener('blur', () => {
                el.value = formatter(el.value);
            });
        });
    }

    bindLiveFormat('.js-format-int', formatInt);
    bindLiveFormat('.js-format-money', formatMoney);

    // Normalizar antes de enviar
    const form = document.getElementById('vehiculo-form');
    if (form) {
        form.addEventListener('submit', () => {
            const currentMaxYear = new Date().getFullYear() + 1;

            const inputFab = form.querySelector('#anio_fabricacion');
            const inputMat = form.querySelector('#anio_matriculacion');

            form.querySelectorAll('.js-format-int').forEach(el => {
                const raw = (el.value || '').replace(/\./g, '');
                let num = raw ? parseInt(raw, 10) : '';

                if (el.id === 'anio_fabricacion' && raw) {
                    if (num < 1886) num = 1886;                 // primer coche
                    if (num > currentMaxYear) num = currentMaxYear;
                }

                if (el.id === 'anio_matriculacion' && raw) {
                    if (num < 1886) num = 1886;
                    if (num > currentMaxYear) num = currentMaxYear;

                    // Si hay año de fabricación, matriculación no puede ser anterior
                    if (inputFab && inputFab.value) {
                        const fabRaw = inputFab.value.replace(/\./g, '');
                        const fabNum = fabRaw ? parseInt(fabRaw, 10) : NaN;
                        if (!isNaN(fabNum) && num < fabNum) {
                            num = fabNum;
                        }
                    }
                }

                if (el.id === 'km' && raw && num < 0) num = 0;
                if (el.id === 'cv' && raw && num < 1) num = 1;

                el.value = raw ? String(num) : '';
            });

            form.querySelectorAll('.js-format-money').forEach(el => {
                el.value = (el.value || '')
                    .replace(/\./g, '')
                    .replace(',', '.');
            });
        });
    }
})();
