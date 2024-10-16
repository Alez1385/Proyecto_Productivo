// Función para alternar la visibilidad de la contraseña
function togglePasswordVisibility(event) {
    const icon = event.target;
    const passwordId = icon.getAttribute('data-target');
    const passwordField = document.getElementById(passwordId);

    // Alternar entre mostrar y ocultar la contraseña
    if (passwordField.type === 'password') {
        passwordField.type = 'text';  // Mostrar la contraseña
        icon.src = '/login/img/eye-close.svg';  // Cambiar ícono a "cerrar ojo"
    } else {
        passwordField.type = 'password';  // Ocultar la contraseña
        icon.src = '/login/img/eye-open.svg';  // Cambiar ícono a "abrir ojo"
    }
}

// Asignar el evento de clic a todos los íconos de ojo
document.querySelectorAll('.lock-icon').forEach(icon => {
    icon.addEventListener('click', togglePasswordVisibility);
});
