<?php
require_once "../scripts/conexion.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/form.css">

</head>

<body>
    <div class="form-container">
        <form class="register-form" action="scripts/register.php" method="post" enctype="multipart/form-data">
            <h2>Sign up</h2>

            <div class="form-group">
                <input type="text" placeholder="First Name" name="nombre" required>
            </div>

            <div class="form-group">
                <input type="text" placeholder="Last Name" name="apellido" required>
            </div>

            <div class="form-group">
                <select name="tipo_doc" required>
                    <option value="" disabled selected>Document Type</option>
                    <option value="ID">ID</option>
                    <option value="Passport">Passport</option>
                </select>
            </div>

            <div class="form-group">
                <input type="text" placeholder="Document Number" name="documento" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
            </div>

            <div class="form-group">
                <input type="date" placeholder="Birth Date" name="fecha_nac" required>
            </div>

            <div class="form-group">
                <input type="file" name="foto" accept="image/*" required>
            </div>

            <div class="form-group">
                <input type="email" placeholder="Email" name="mail" id="email" required>
                <span id="emailError" class="error-message"></span>
            </div>


            <div class="form-group">
                <input type="tel" placeholder="Phone Number" name="telefono" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
            </div>

            <div class="form-group">
                <input type="text" placeholder="Address" name="direccion" required>
            </div>

            <div class="form-group">
                <select name="id_tipo_usuario" required>
                    <option value="" disabled selected>User Type</option>
                    <?php
                    $query = "SELECT id_tipo_usuario, nombre FROM tipo_usuario";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_tipo_usuario'] . "'>" . $row['nombre'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay tipos de usuario disponibles</option>";
                    }
                    ?>
                </select>
            </div>


            <div class="form-group">
                <input type="text" placeholder="Username" name="username" id="username" required>
                <span id="usernameError" class="error-message"></span>
            </div>

            <div class="form-group">
                <input type="password" placeholder="Password" name="clave" id="password" required>
                <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">REGISTRO</button>

            <p>Already have an account? <a href="login.php">Sign in</a></p>
        </form>

    </div>

    <script src="scripts/password.js"></script>
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