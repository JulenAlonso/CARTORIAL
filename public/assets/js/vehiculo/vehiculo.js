(function () {
    const fmt = new Intl.NumberFormat('de-DE'); // 1.234.567,89

    function formatInt(val) {
        const digits = (val || '').replace(/\D+/g, ''); // Elimina caracteres no numéricos
        if (!digits) return '';
        return fmt.format(parseInt(digits, 10)); // Formatea el valor numérico
    }

    function formatMoney(val) {
        val = (val || '').replace(/[^\d,]/g, ''); // Eliminar todo lo que no sea dígitos o coma
        const parts = val.split(',');
        const intDigits = (parts[0] || '').replace(/\D+/g, '');  // Asegurarse de que sólo se usan dígitos en la parte entera
        let out = intDigits ? fmt.format(parseInt(intDigits, 10)) : ''; // Formatear la parte entera
        if (parts.length > 1) {
            const dec = (parts[1] || '').replace(/\D+/g, ''); // Eliminar cualquier carácter no numérico en la parte decimal
            out += ',' + dec; // Agregar la parte decimal
        }
        return out;
    }

    function withCaret(el, formatter) {
        el.value = formatter(el.value);
        const len = el.value.length;
        try {
            el.setSelectionRange(len, len); // Mantener el cursor al final
        } catch (_) { }
    }

    function bindLiveFormat(selector, formatter) {
        document.querySelectorAll(selector).forEach(el => {
            el.addEventListener('input', () => withCaret(el, formatter)); // Formatear en tiempo real
            el.addEventListener('blur', () => {
                el.value = formatter(el.value); // Formatear al perder el foco
            });
        });
    }

    bindLiveFormat('.js-format-int', formatInt); // Aplicar formato de enteros
    bindLiveFormat('.js-format-money', formatMoney); // Aplicar formato de dinero

    // Normalizar antes de enviar
    const form = document.getElementById('vehiculo-form');
    if (form) {
        form.addEventListener('submit', () => {
            const currentMaxYear = new Date().getFullYear() + 1;

            form.querySelectorAll('.js-format-int').forEach(el => {
                const raw = (el.value || '').replace(/\./g, ''); // Eliminar puntos como separadores de miles
                let num = raw ? parseInt(raw, 10) : '';
                if (el.id === 'anio' && raw) {
                    if (num < 1886) num = 1886; // Asegurar que el año no sea menor que 1886
                    if (num > currentMaxYear) num = currentMaxYear; // Asegurar que el año no sea mayor al actual
                }
                if (el.id === 'km' && raw && num < 0) num = 0; // Asegurar que los kilómetros no sean negativos
                if (el.id === 'cv' && raw && num < 1) num = 1; // Asegurar que los caballos de vapor no sean menores a 1
                el.value = raw ? String(num) : ''; // Asignar el valor limpio
            });

            form.querySelectorAll('.js-format-money').forEach(el => {
                el.value = (el.value || '').replace(/\./g, '').replace(',', '.'); // Normalizar el valor de dinero
            });
        });
    }
})();
