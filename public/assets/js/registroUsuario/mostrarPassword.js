/* ============================================================
    SISTEMA GLOBAL PARA MOSTRAR / OCULTAR CONTRASEÑA
    ------------------------------------------------------------
    Este script detecta todos los iconos con la clase
    ".toggle-password" y les añade funcionalidad para cambiar
    la visibilidad del campo de contraseña asociado.
    
    Cada icono debe tener el atributo:
        data-target="id_del_input"
   Ejemplo:
        <i class="toggle-password bi bi-eye" data-target="password"></i>
=============================================================== */

document.querySelectorAll(".toggle-password").forEach(icon => {

    // Evento click en cada icono de "ojo"
    icon.addEventListener("click", function () {

        // Obtener el input asociado usando el atributo data-target
        const target = document.getElementById(this.dataset.target);

        // Si el input no existe, no hacemos nada (evita errores)
        if (!target) return;

        // --------------------------------------------------------
        // Cambiar el tipo de input:
        // Si es "password", mostrar el texto → "text"
        // Si es "text", ocultar el texto → "password"
        // --------------------------------------------------------
        target.type = target.type === "password" ? "text" : "password";

        // --------------------------------------------------------
        // Alternar el icono Bootstrap Icons:
        // bi-eye        → icono de ojo (mostrar)
        // bi-eye-slash  → icono de ojo tachado (ocultar)
        // --------------------------------------------------------
        this.classList.toggle("bi-eye");
        this.classList.toggle("bi-eye-slash");
    });
});
