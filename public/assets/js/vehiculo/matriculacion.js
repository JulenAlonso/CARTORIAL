// Rango de letras a los años correspondientes
const matriculasPorAno = {
    2025: { start: 'MYF', end: 'NFV' },
    2024: { start: 'MNC', end: 'MXP' },
    2023: { start: 'MDR', end: 'MMN' },
    2022: { start: 'LWD', end: 'MDD' },
    2021: { start: 'LML', end: 'LVF' },
    2020: { start: 'LFH', end: 'LMC' },
    2019: { start: 'KTJ', end: 'LDR' },
    2018: { start: 'KHG', end: 'KSS' },
    2017: { start: 'JWN', end: 'KGN' },
    2016: { start: 'JLN', end: 'JVZ' },
    2015: { start: 'JCK', end: 'JKZ' },
    2014: { start: 'HVN', end: 'JBY' },
    2013: { start: 'HNT', end: 'HVF' },
    2012: { start: 'HJC', end: 'HNK' },
    2011: { start: 'HBP', end: 'HHT' },
    2010: { start: 'GTC', end: 'HBE' },
    2009: { start: 'GKS', end: 'GSR' },
    2008: { start: 'FZR', end: 'GKH' },
    2007: { start: 'FKY', end: 'FYY' },
    2006: { start: 'DVW', end: 'FKC' },
    2005: { start: 'DFZ', end: 'DVB' },
    2004: { start: 'CRV', end: 'DFF' },
    2003: { start: 'CDV', end: 'CRC' },
    2002: { start: 'BSL', end: 'CDC' },
    2001: { start: 'BFJ', end: 'BRT' },
    2000: { start: '--', end: 'BDR' }
};

// Función para obtener el año basado en la matrícula
function obtenerAnoDeMatricula(matricula) {
    // Extraemos las primeras tres letras alfabéticas de la matrícula
    const letras = matricula.replace(/[^A-Za-z]/g, '').slice(0, 3).toUpperCase(); // Filtramos solo letras y tomamos las primeras tres

    // Verificar en qué rango cae la matrícula
    for (let ano in matriculasPorAno) {
        const rango = matriculasPorAno[ano];
        if (letras >= rango.start && letras <= rango.end) {
            return ano;
        }
    }

    return ""; // Si no se encuentra un año válido
}

// Agregar evento de entrada en el campo de matrícula
document.getElementById('matricula').addEventListener('input', function () {
    const matricula = this.value; // Obtener el valor de la matrícula
    const añoMatriculacion = obtenerAnoDeMatricula(matricula); // Obtener el año correspondiente

    // Solo actualizar el campo si se encontró un año
    if (añoMatriculacion) {
        document.getElementById('anio').value = añoMatriculacion; // Actualizar el campo "Año"
    } else {
        document.getElementById('anio').value = ''; // Si no es válida, dejar el campo vacío
    }
});
