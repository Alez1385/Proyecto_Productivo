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
    updateLastAccess($_SESSION['id_usuario']);
    exit();
}

// Verificar si la cookie de "recordarme" está establecida
if (isset($_COOKIE['remember_token'])) {
    error_log("Remember token found: " . $_COOKIE['remember_token']);

    // Retrieve the token from the cookie
    $rememberToken = $_COOKIE['remember_token'];

    // Get the user ID associated with the token
    $userId = getUserIdFromToken($rememberToken);

    if ($userId) {
        // Validate the remember token
        if (validateRememberToken($userId, $rememberToken)) {
            // Get user information
            $user = getUserInfo($conn, $userId);

            if ($user) {
                // User found, start session
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['tipo_nombre'];

                // Regenerate session ID for security
                session_regenerate_id(true);

                // Update last access timestamp
                updateLastAccess($userId);

                // Redirect to dashboard
                header("Location: /dashboard/dashboard.php");
                exit();
            }
        }
    }

    // If we reach here, the token is invalid or expired
    removeRememberToken($userId);
    error_log("Invalid or expired remember token");
} else {
    error_log("No remember token found");
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

                <!-- Password -->
                <div class="input-group">
                    <input type="password" placeholder="Password" name="password" id="password" required>
                    <img src="./img/eye-open.svg" alt="Toggle Password Visibility" class="lock-icon" data-target="password">
                    <span id="password-strength" class="password-strength"></span>
                </div>
                <!-- Segundo campo de contraseña -->
                <div class="input-group">
                    <input type="password" placeholder="Confirm Password" name="password2" id="password2" required>
                    <img src="./img/eye-open.svg" alt="Toggle Password Visibility" class="lock-icon" data-target="password2">
                    <span id="password-strength" class="password-strength"></span>
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

            <div class="input-group">
                <input type="password" placeholder="Password" name="password" id="password3" required>
                <img src="./img/eye-open.svg" alt="Toggle Password Visibility" class="lock-icon" data-target="password3">
                <span id="password-strength" class="password-strength"></span>
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


    <!-- Modal de Error -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeModal">&times;</span>
            <div class="modal-icon">&#9888;</div>
            <h2>Error</h2>
            <p id="errorMessage"></p>
        </div>
    </div>


    <!-- Scripts -->
    <script src="../js/login.js"></script>
    <script src="scripts/password.js"></script>
    <script>
        


      // Password strength check function
      function checkPasswordStrength(password) {
            const minLength = 12;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[^A-Za-z0-9]/.test(password);
            
            const commonPasswords = ['password', '123456', 'qwerty', 'letmein'];
            
            const isStrong = password.length >= minLength && 
                             hasUpper && hasLower && hasNumber && hasSpecial &&
                             !commonPasswords.includes(password.toLowerCase());
            
            return isStrong;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const usernameError = document.getElementById('usernameError');
            const emailError = document.getElementById('emailError');
            const passwordStrength = document.getElementById('password-strength');

            // Username verification
            usernameInput.addEventListener('input', function() {
                const username = this.value;
                if (username.length > 0) {
                    fetch('scripts/check_username.php', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/x-www-form-urlencoded'
                        },
                        body: `username=${encodeURIComponent(username)}`
                    })
                    .then(response => response.text())
                    .then(text => {
                        usernameError.textContent = text === 'taken' ? 'El nombre de usuario ya está en uso. Elige otro.' : 'El nombre de usuario está disponible.';
                        usernameError.style.color = text === 'taken' ? '#c0392b' : '#27ae60';
                    });
                } else {
                    usernameError.textContent = '';
                }
            });

            // Email verification
            emailInput.addEventListener('input', function() {
                const email = this.value;
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (email.length > 0) {
                    if (!emailPattern.test(email)) {
                        emailError.textContent = 'Formato de correo electrónico no válido.';
                        emailError.style.color = '#c12646';
                    } else {
                        fetch('scripts/check_email.php', {
                            method: 'POST',
                            headers: {
                                'Content-type': 'application/x-www-form-urlencoded'
                            },
                            body: `email=${encodeURIComponent(email)}`
                        })
                        .then(response => response.text())
                        .then(text => {
                            if (text === 'invalid') {
                                emailError.textContent = 'Formato de correo electrónico no válido.';
                                emailError.style.color = '#c12646';
                            } else if (text === 'taken') {
                                emailError.textContent = 'El correo electrónico ya está en uso. Elige otro.';
                                emailError.style.color = '#c12646';
                            } else {
                                emailError.textContent = 'El correo electrónico está disponible.';
                                emailError.style.color = '#00bcff';
                            }
                        })
                        .catch(() => {
                            emailError.textContent = 'Error al verificar el correo electrónico. Inténtalo más tarde.';
                            emailError.style.color = '#c12646';
                        });
                    }
                } else {
                    emailError.textContent = '';
                }
            });

            // Password strength check
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const isStrong = checkPasswordStrength(password);
                
                if (password.length > 0) {
                    if (isStrong) {
                        passwordStrength.textContent = 'Strong password';
                        passwordStrength.style.color = '#27ae60';
                    } else {
                        passwordStrength.textContent = 'Weak password';
                        passwordStrength.style.color = '#c0392b';
                    }
                } else {
                    passwordStrength.textContent = '';
                }
            });
        });
    </script>
</body>

</html>