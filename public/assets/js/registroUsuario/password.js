document.addEventListener("DOMContentLoaded", () => {

    const password = document.getElementById("password");
    const confirm = document.getElementById("password_confirmation");
    const btn = document.getElementById("btn-submit");

    password.addEventListener("input", validar);
    confirm.addEventListener("input", validar);

    function validar() {
        const value = password.value;

        const valid = {
            len: value.length >= 8,
            lower: /[a-z]/.test(value),
            upper: /[A-Z]/.test(value),
            number: /\d/.test(value),
            symbol: (value.match(/[\W_]/g) || []).length >= 2,
            space: !/\s/.test(value),
            equal: value === confirm.value
        };

        actualizarChecklist(valid);

        // ACTIVAR BOTÓN SOLO SI TODO ES VÁLIDO
        btn.disabled = !Object.values(valid).every(v => v === true);
    }

    function actualizarChecklist(v) {
        actualizarItem("chk-8", v.len);
        actualizarItem("chk-lower", v.lower);
        actualizarItem("chk-upper", v.upper);
        actualizarItem("chk-number", v.number);
        actualizarItem("chk-symbol", v.symbol);
        actualizarItem("chk-space", v.space);
    }

    function actualizarItem(id, ok) {
        const item = document.getElementById(id);
        item.style.color = ok ? "green" : "red";
        item.innerHTML = (ok ? "✔️ " : "❌ ") + item.innerHTML.substring(2);
    }
});
