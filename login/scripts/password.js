function togglePassword(passwordId, toggleId) {
    const passwordField = document.getElementById(passwordId);
    const lockIcon = document.getElementById(toggleId);

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        lockIcon.src = 'img/eye-close.svg'; // Cambia a ícono de ojo cerrado
        lockIcon.classList.add('rotate');
    } else {
        passwordField.type = 'password';
        lockIcon.src = 'img/eye-open.svg'; // Cambia a ícono de ojo abierto
        lockIcon.classList.remove('rotate');
    }
}
