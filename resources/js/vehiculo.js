        (function() {
            const fmt = new Intl.NumberFormat('de-DE'); // 1.234.567,89

            function formatInt(val) {
                const digits = (val || '').replace(/\D+/g, '');
                if (!digits) return '';
                return fmt.format(parseInt(digits, 10));
            }

            function formatMoney(val) {
                // Mantén dígitos y coma, formatea miles en la parte entera
                val = (val || '').replace(/[^\d,]/g, '');
                const parts = val.split(',');
                const intDigits = (parts[0] || '').replace(/\D+/g, '');
                let out = intDigits ? fmt.format(parseInt(intDigits, 10)) : '';
                if (parts.length > 1) {
                    const dec = (parts[1] || '').replace(/\D+/g, '');
                    out += ',' + dec; // conserva decimales escritos
                }
                return out;
            }

            function withCaret(el, formatter) {
                // Sencillo y fiable: tras formatear, cursor al final
                el.value = formatter(el.value);
                const len = el.value.length;
                try {
                    el.setSelectionRange(len, len);
                } catch (_) {}
            }

            function bindLiveFormat(selector, formatter) {
                document.querySelectorAll(selector).forEach(el => {
                    el.addEventListener('input', () => withCaret(el, formatter));
                    el.addEventListener('blur', () => {
                        el.value = formatter(el.value);
                    });
                });
            }

            // Vincular campos
            bindLiveFormat('.js-format-int', formatInt);
            bindLiveFormat('.js-format-money', formatMoney);

            // Normalizar antes de enviar
            const form = document.getElementById('vehiculo-form');
            if (form) {
                form.addEventListener('submit', () => {
                    const currentMaxYear = new Date().getFullYear() + 1;

                    form.querySelectorAll('.js-format-int').forEach(el => {
                        const raw = el.value.replace(/\./g, '');
                        let num = raw ? parseInt(raw, 10) : '';
                        if (el.id === 'anio' && raw) {
                            if (num < 1886) num = 1886;
                            if (num > currentMaxYear) num = currentMaxYear;
                        }
                        if (el.id === 'km' && raw && num < 0) num = 0;
                        if (el.id === 'cv' && raw && num < 1) num = 1;
                        el.value = raw ? String(num) : '';
                    });

                    form.querySelectorAll('.js-format-money').forEach(el => {
                        // "1.234.567,89" -> "1234567.89"
                        el.value = el.value.replace(/\./g, '').replace(',', '.');
                    });
                });
            }
        })();
