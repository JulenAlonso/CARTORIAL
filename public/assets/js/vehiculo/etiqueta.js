// ================================
// Cálculo automático de ETIQUETA
// ================================
function calcularEtiqueta(combustible, anioMat) {
    const year = parseInt(anioMat, 10);
    if (!combustible || isNaN(year)) return "";

    // Vehículos eléctricos e híbridos
    if (combustible === "Eléctrico") return "0";
    if (combustible === "Híbrido") return "ECO";

    // Gasolina
    if (combustible === "Gasolina") {
        if (year >= 2006) return "C";
        if (year >= 2001) return "B";
        return "No tiene";
    }

    // Diésel
    if (combustible === "Diésel") {
        if (year >= 2015) return "C";
        if (year >= 2006) return "B";
        return "No tiene";
    }

    // Otros casos
    return "";
}

/**
 * Conecta los inputs y actualiza la etiqueta automáticamente.
 */
function initEtiquetaAuto(combustibleSelect, anioMatInput, etiquetaSelect) {
    if (!combustibleSelect || !anioMatInput || !etiquetaSelect) return;

    function update() {
        const etiqueta = calcularEtiqueta(
            combustibleSelect.value,
            anioMatInput.value
        );
        if (etiqueta) {
            etiquetaSelect.value = etiqueta;
        }
    }

    combustibleSelect.addEventListener("change", update);
    anioMatInput.addEventListener("input", update);

    // Autocalcular al cargar (por si viene de old())
    update();
}
