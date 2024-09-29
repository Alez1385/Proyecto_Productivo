function togglePassword(passwordId, toggleId) {
    const passwordField = document.getElementById(passwordId);
    const lockIcon = document.getElementById(toggleId);

    if (passwordField.type === 'password') {
        passwordField.type = 'text';  // Cambiar a texto para mostrar la contraseña
        lockIcon.src = 'img/eye-close.svg';  // Cambiar el ícono a 'cerrado'
    } else {
        passwordField.type = 'password';  // Cambiar a contraseña para ocultarla
        lockIcon.src = 'img/eye-open.svg';  // Cambiar el ícono a 'abierto'
    }
}
