
const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("passwordInput");

togglePassword.addEventListener("click", function () {
    const type = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = type;

    // Cambia icono ojo / ojo tachado
    this.classList.toggle("bi-eye");
    this.classList.toggle("bi-eye-slash");
});
