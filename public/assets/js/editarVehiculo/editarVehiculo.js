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
    const letras = matricula.replace(/[^A-Za-z]/g, '').slice(0, 3).toUpperCase();
    for (let ano in matriculasPorAno) {
        const rango = matriculasPorAno[ano];
        if (letras >= rango.start && letras <= rango.end) {
            return ano;
        }
    }
    return "";
}

// Validar formato de matrícula
const regexProvincialNumeric = /^[A-Z]{1}-\d{4}$/;
const regexProvincialAlphanumeric = /^[A-Z]{1}-\d{4}-[A-Z]{2}$/;
const regexNationalAlphanumeric = /^\d{4} [A-Z]{3}$/;

function validateLicensePlate(plate) {
    plate = plate.trim().toUpperCase();
    if (regexProvincialNumeric.test(plate)) {
        return 'Formato provincial numérico (M-1234)';
    } else if (regexProvincialAlphanumeric.test(plate)) {
        return 'Formato provincial alfanumérico (M-1234-AZ)';
    } else if (regexNationalAlphanumeric.test(plate)) {
        return 'Formato alfanumérico nacional (1234 XYZ)';
    } else {
        return 'Matrícula no válida';
    }
}

document.getElementById('matricula').addEventListener('input', function () {
    const matricula = this.value;
    const añoMatriculacion = obtenerAnoDeMatricula(matricula);

    if (añoMatriculacion) {
        document.getElementById('anio').value = añoMatriculacion;
    } else {
        document.getElementById('anio').value = '';
    }

    const validationMessage = validateLicensePlate(matricula);
    if (validationMessage === 'Matrícula no válida') {
        this.setCustomValidity(validationMessage);
    } else {
        this.setCustomValidity('');
    }
});

