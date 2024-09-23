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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
    <div class="container" id="container">
    <div class="form-container sign-up">
    <form action="scripts/register.php" method="post" enctype="multipart/form-data">
                <h1>Register</h1>
                <span>Usa tu email para registrarte</span>
                <div class="input-group">
                <input type="text" placeholder="Username" name="username" id="username" required>
                <span id="usernameError" class="error-message"></span>
                </div>
                
                <div class="input-group">
                <input type="email" placeholder="Email" name="mail" id="email" required>
                <span id="emailError" class="error-message"></span>
                </div>
                
                <div class="input-group">
                <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
                <input type="password" placeholder="Password" name="clave" id="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">Crear Cuenta</button>
                
            </form>
        </div>


    <div class="form-container sign-in">
        <form  action="scripts/login_check.php" method="POST" > 
            <!-- Cambié el action -->
            <h2>Login</h2>
            <span>Usa tu email y contraseña</span>

            <div class="input-group">
            <input type="text" placeholder="Username" name="username" required>
                </div>
                <div class="input-group">
                <input type="password" placeholder="Password" name="password" id="password" required>
                <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
                </div>
                <div class="options">
                <label>
                    <input type="checkbox" name="remember"> Recordar Contraseña
                </label>
                
            </div>
            <a href="forgot_password.html" class="forgot-password">Olvidaste tu contraseña?</a>
                <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

       

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

    <script src="../js/login.js"></script>
 
    <script>
        function checkFormValidity() {
            const usernameError = document.getElementById('usernameError').textContent;
            const emailError = document.getElementById('emailError').textContent;
            const submitBtn = document.getElementById('submitBtn');

            if (usernameError === 'El nombre de usuario está disponible.' && emailError === 'El correo electrónico está disponible.') {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }


        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            const usernameError = document.getElementById('usernameError');

            if (username.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'scripts/check_username.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                xhr.onreadystatechange = function() {
                    if (this.readyState === 4) {
                        if (this.status === 200) {
                            if (this.responseText === 'taken') {
                                usernameError.textContent = 'El nombre de usuario ya está en uso. Elige otro.';
                                usernameError.style.color = '#c12646';
                            } else {
                                usernameError.textContent = 'El nombre de usuario está disponible.';
                                usernameError.style.color = '#00bcff';
                            }
                            checkFormValidity(); // Llama a la función para verificar el estado del formulario
                        } else {
                            usernameError.textContent = 'Error al verificar el nombre de usuario. Inténtalo más tarde.';
                            usernameError.style.color = '#c12646';
                            checkFormValidity(); // Llama a la función para deshabilitar el botón
                        }
                    }
                };

                xhr.onerror = function() {
                    usernameError.textContent = 'Error al conectar con el servidor. Inténtalo más tarde.';
                    usernameError.style.color = '#c12646';
                    checkFormValidity(); // Llama a la función para deshabilitar el botón
                };

                xhr.send('username=' + encodeURIComponent(username));
            } else {
                usernameError.textContent = '';
                checkFormValidity(); // Llama a la función para deshabilitar el botón
            }
        });

        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailError = document.getElementById('emailError');

            // Expresión regular para validar el formato del correo electrónico
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email.length > 0) {
                if (!emailPattern.test(email)) {
                    emailError.textContent = 'Formato de correo electrónico no válido.';
                    emailError.style.color = '#c12646';
                    checkFormValidity(); // Llama a la función para deshabilitar el botón
                } else {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'scripts/check_email.php', true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                    xhr.onreadystatechange = function() {
                        if (this.readyState === 4) {
                            if (this.status === 200) {
                                if (this.responseText === 'invalid') {
                                    emailError.textContent = 'Formato de correo electrónico no válido.';
                                    emailError.style.color = '#c12646';
                                } else if (this.responseText === 'taken') {
                                    emailError.textContent = 'El correo electrónico ya está en uso. Elige otro.';
                                    emailError.style.color = '#c12646';
                                } else {
                                    emailError.textContent = 'El correo electrónico está disponible.';
                                    emailError.style.color = '#00bcff';
                                }
                                checkFormValidity(); // Llama a la función para verificar el estado del formulario
                            } else {
                                emailError.textContent = 'Error al verificar el correo electrónico. Inténtalo más tarde.';
                                emailError.style.color = '#c12646';
                                checkFormValidity(); // Llama a la función para deshabilitar el botón
                            }
                        }
                    };

                    xhr.onerror = function() {
                        emailError.textContent = 'Error al conectar con el servidor. Inténtalo más tarde.';
                        emailError.style.color = '#c12646';
                        checkFormValidity(); // Llama a la función para deshabilitar el botón
                    };

                    xhr.send('email=' + encodeURIComponent(email));
                }
            } else {
                emailError.textContent = '';
                checkFormValidity(); // Llama a la función para deshabilitar el botón
            }
        });
    </script>
</body>
</html>
