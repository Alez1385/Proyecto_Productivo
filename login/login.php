<?php
// login.php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
session_start();
require_once('../scripts/conexion.php');
require_once('../scripts/functions.php');

// Set security headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data:;");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");



// Force HTTPS
if (!in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'])) {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
        exit();
    }
}

// Si ya hay una sesión activa, redirigir al dashboard
if (isset($_SESSION['username'])) {
    header("Location: /dashboard/dashboard.php");
    exit();
}

// Verificar si la cookie de "recordarme" está establecida
if (isset($_COOKIE['remember_token'])) {
    error_log("Remember token encontrado: " . $_COOKIE['remember_token']);
    // Recuperar el token de la cookie
    $rememberToken = $_COOKIE['remember_token'];

    // Verificar el token en la base de datos
    $user = getUserInfo($conn, getUserIdFromToken($conn, $rememberToken));

    if (isset($user)) {
        // Usuario encontrado, iniciar sesión
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['tipo_nombre'];
        
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
        
        // Redirigir al dashboard
        header("Location: /dashboard/dashboard.php");
        exit();
    } else {
        // El token no es válido, eliminar la cookie
        // setcookie('remember_token', '', time() - 3600, "/");
        echo $user;
    }   
} else {
    error_log("No se encontró remember token");
}

// CSRF Token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirect handling
$redirect = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_URL) ?? '/dashboard/dashboard.php';
if (!isValidRedirect($redirect)) {
    $redirect = '/dashboard/dashboard.php';
}

$error = '';
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js" integrity="sha512-TZlMGFY9xKj38t/5m2FzJ+RM/aD5alMHDe26p0mYUMoCF5G7ibfHUQILq0qQPV3wlsnCwL+TPRNK4vIWGLOkUQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/auth-debug.js"></script>
    <script>
        // Log form submission
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            form.addEventListener('submit', (e) => {
                logAuthInfo('Login form submitted');
                logAuthInfo(`Username: ${form.username.value}`);
                logAuthInfo(`Remember me: ${form.remember.checked}`);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Log session data
            console.log('Session data:', <?php echo json_encode($_SESSION); ?>);

            // Log cookies
            console.log('Cookies:', document.cookie);
        });
    </script>
</head>

<body>
    <div class="container" id="container">
        <!-- Formulario de Registro -->
        <div class="form-container sign-up">
            <form action="scripts/register.php" method="post" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                <h1>Register</h1>
                <span>Usa tu email para registrarte</span>

                <!-- Username -->
                <div class="input-group">
                    <input type="text" placeholder="Username" name="username" id="username" required>
                    <span id="usernameError" class="error-message"></span>
                </div>

                <!-- Email -->
                <div class="input-group">
                    <input type="email" placeholder="Email" name="mail" id="email" required>
                    <span id="emailError" class="error-message"></span>
                </div>

                <!-- Password con ojito para mostrar/ocultar -->
                <div class="input-group">
                    <input type="password" placeholder="Password" name="clave" id="clave" required>
                    <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="register-lock-icon" onclick="togglePassword('clave','register-lock-icon')">
                    <span id="register-password-strength" class="password-strength"></span>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Crear Cuenta</button>
            </form>
        </div>

        <!-- Formulario de Login -->
        <div class="form-container sign-in">
            <form action="scripts/login_check.php" method="POST" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                <h2>Login</h2>
                <span>Usa tu email o contraseña</span>

                <!-- Username -->
                <div class="input-group">
                    <input type="text" placeholder="Username" name="username" required>
                </div>

                <!-- Password con ojito para mostrar/ocultar -->
                <div class="input-group">
                    <input type="password" placeholder="Password" name="password" id="password" required>
                    <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="login-lock-icon" onclick="togglePassword('password', 'login-lock-icon')">
                    <span id="login-password-strength" class="password-strength"></span>
                </div>

                <div class="options">
                    <label>
                        <input type="checkbox" name="remember"> Recordar Contraseña
                    </label>
                </div>

                <a href="forgot_password.html" class="forgot-password">¿Olvidaste tu contraseña?</a>
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>

        <!-- Contenedor de Cambios entre Formulario de Login y Registro -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bienvenido de vuelta!</h1>
                    <p>Introduzca sus datos personales para utilizar todas las funciones del sitio</p>
                    <button class="hidden" id="login">Login</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hola, Amigo!</h1>
                    <p>Regístrese con sus datos personales para utilizar todas las funciones del sitio</p>
                    <button class="hidden" id="register">Crear Usuario</button>
                </div>
            </div>
        </div>
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

    <!-- Scripts -->
    <script src="../js/login.js"></script>
    <script>
        // Función para mostrar/ocultar contraseñas
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

        // Función para medir la fuerza de la contraseña
        function checkPasswordStrength(password, strengthElementId) {
            const result = zxcvbn(password);
            const strength = ['Muy débil', 'Débil', 'Regular', 'Fuerte', 'Muy fuerte'];
            const strengthElement = document.getElementById(strengthElementId);
            strengthElement.textContent = 'Fortaleza de la contraseña: ' + strength[result.score];

            // Añadir clases de color basadas en la puntuación
            strengthElement.className = 'password-strength'; // Resetear clases
            strengthElement.classList.add('strength-' + result.score);
        }

        // Aplicar medidor de fuerza de contraseña para el formulario de registro
        document.getElementById('clave').addEventListener('input', function() {
            checkPasswordStrength(this.value, 'register-password-strength');
        });

        // Aplicar medidor de fuerza de contraseña para el formulario de login
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value, 'login-password-strength');
        });

        // Verificación de nombre de usuario
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            const usernameError = document.getElementById('usernameError');

            if (username.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'scripts/check_username.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                xhr.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        usernameError.textContent = this.responseText === 'taken' ? 'El nombre de usuario ya está en uso. Elige otro.' : 'El nombre de usuario está disponible.';
                        usernameError.style.color = this.responseText === 'taken' ? '#c0392b' : '#27ae60';
                    }
                };
                xhr.send('username=' + encodeURIComponent(username));
            }
        });

        // Verificación de email
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailError = document.getElementById('emailError');

            if (email.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'scripts/check_email.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                xhr.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        emailError.textContent = this.responseText === 'taken' ? 'El correo electrónico ya está en uso.' : 'El correo electrónico está disponible.';
                        emailError.style.color = this.responseText === 'taken' ? '#c0392b' : '#27ae60';
                    }
                };
                xhr.send('email=' + encodeURIComponent(email));
            }
        });
    </script>
</body>

</html>