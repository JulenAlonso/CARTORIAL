(function() {
    // Expresiones regulares para los tres tipos de matrícula
    const regexProvincialNumeric = /^[A-Z]{1}-\d{4}$/; // M-1234
    const regexProvincialAlphanumeric = /^[A-Z]{1}-\d{4}-[A-Z]{2}$/; // M-1234-AZ
    const regexNationalAlphanumeric = /^\d{4} [A-Z]{3}$/; // 1234 XYZ

    // Función para validar el formato de la matrícula
    function validateLicensePlate(plate) {
        // Eliminar espacios y convertir todo a mayúsculas
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

    // Validar en tiempo real mientras el usuario escribe
    document.getElementById('matricula').addEventListener('input', function() {
        const plate = this.value;
        const validationMessage = validateLicensePlate(plate);
        
        if (validationMessage === 'Matrícula no válida') {
            this.setCustomValidity(validationMessage); // Establece el mensaje de error
        } else {
            this.setCustomValidity(''); // Si es válido, elimina el error
        }
    });
})();