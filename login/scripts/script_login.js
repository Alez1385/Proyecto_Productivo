function togglePassword() {
    const passwordField = document.getElementById('password');
    const lockIcon = document.getElementById('toggleLock');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        lockIcon.src = 'img/eye-close.svg'; // Cambia a ícono de candado cerrado
        lockIcon.classList.add('rotate');
    } else {
        passwordField.type = 'password';
        lockIcon.src = 'img/eye-open.svg'; // Cambia a ícono de candado abierto
        lockIcon.classList.remove('rotate');
    }
}
