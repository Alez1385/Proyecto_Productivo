<?php
session_start();

include('../scripts/conexion.php');

// Restaurar sesión si existen cookies
if (isset($_COOKIE['username']) && isset($_COOKIE['id_usuario'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['id_usuario'] = $_COOKIE['id_usuario'];
    header("Location: ../dashboard/dashboard.php");
    exit();
}

$error = '';

// Verificar si hay un parámetro de error en la URL
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'emptyfields':
            $error = "Por favor, complete todos los campos.";
            break;
        case 'invalidpassword':
            $error = "Contraseña incorrecta.";
            break;
        case 'nouser':
            $error = "Usuario no encontrado.";
            break;
        default:
            $error = "Ocurrió un error. Por favor, inténtelo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style-login.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <script src="scripts/password.js"></script>
    <script>
        // Mostrar el modal con el mensaje de éxito o error
function showModal(type, message) {
    const overlay = document.getElementById('overlay');
    const modal = document.getElementById('modal');
    const modalHeader = document.querySelector('.modal-header');
    const modalBody = document.querySelector('.modal-body');
    const loadingBar = document.querySelector('.loading-bar');

    modalHeader.textContent = type === 'success' ? 'Completado' : 'Error';
    modalBody.textContent = message;

    if (type === 'success') {
        modal.classList.add('success');
        modal.classList.remove('error');
        loadingBar.style.display = 'block';
    } else {
        modal.classList.add('error');
        modal.classList.remove('success');
        loadingBar.style.display = 'none';
    }

    overlay.style.display = 'block';
    modal.style.display = 'flex';

    // Eliminar el parámetro de la URL después de 3 segundos

        setTimeout(() => {
            closeModal();
            removeURLParameter('error');
        }, 3000);
}

// Cerrar el modal
function closeModal() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('modal').style.display = 'none';
}

// Eliminar un parámetro específico de la URL
function removeURLParameter(param) {
    let url = new URL(window.location.href);
    url.searchParams.delete(param);
    window.history.replaceState(null, '', url);
}

// Mostrar el modal al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('message') && urlParams.get('message') === 'success') {
        showModal('success', 'Tu registro ha sido exitoso!');
    } else if (urlParams.has('error')) {
        showModal('error', '<?php echo addslashes($error); ?>');
    }
});

    </script>
</head>

<body>
    <div class="login-container">
        <form class="login-form" action="scripts/login_check.php" method="POST"> <!-- Cambié el action -->
            <h2>Sign in</h2>

            <div class="input-group">
                <img src="img/user-icon.svg" alt="User Icon" class="icon">
                <input type="text" placeholder="Username" name="username" required>
            </div>

            <div class="input-group">
                <img src="img/lock-icon.svg" alt="Lock-Icon" class="icon">
                <input type="password" placeholder="Password" name="password" id="password" required>
                <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
            </div>

            <div class="options">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="forgot_password.html" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login">LOGIN</button>

            <p>Or Sign in with Google</p>
            <div class="social-container">
                <a href="scripts/google_login.php" class="google-login"><img src="img/google-icon.svg" alt="Google"></a>
            </div>

            <p>¿No tienes una cuenta? <a href="register.php" class="register-link">Regístrate aquí</a>.</p>
        </form>
    </div>

    <!-- Modal y Overlay -->
    <div id="overlay" class="overlay"></div>
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="modal-header"></div>
            <div class="modal-body"></div>
            <div class="loading-bar"></div>
        </div>
    </div>
</body>
</html>
