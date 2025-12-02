// ====================================================
//   SISTEMA UNIVERSAL PARA MOSTRAR / OCULTAR PASSWORD
//   Funciona con cualquier cantidad de inputs y iconos
//   Usa: <i class="toggle-password" data-target="id">
// ====================================================

// Seleccionar todos los iconos que activan el toggle
const toggleIcons = document.querySelectorAll(".toggle-password");

toggleIcons.forEach(icon => {

    // Para cada icono, escuchar el click
    icon.addEventListener("click", function () {

        // Obtener el input al que apunta este icono
        const inputId = this.dataset.target;
        const targetInput = document.getElementById(inputId);

        // Si el input no existe, no hacer nada (previene errores)
        if (!targetInput) return;

        // Cambiar tipo → password <-> text
        targetInput.type =
            targetInput.type === "password" ? "text" : "password";

        // Alternar icono (Bootstrap Icons)
        this.classList.toggle("bi-eye");
        this.classList.toggle("bi-eye-slash");
    });
});
// ====================================================