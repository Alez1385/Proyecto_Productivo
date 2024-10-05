<?php
require_once "../../scripts/conexion.php";
?>

<div class="form-container">
    <form class="register-form" action="scripts/save_student.php" method="post" enctype="multipart/form-data" id="newStudentForm">
        <h2>Nuevo Estudiante</h2>

        <div class="form-group">
            <input type="text" placeholder="Nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <input type="text" placeholder="Apellido" name="apellido" required>
        </div>

        <div class="form-group">
            <select name="tipo_doc" required>
                <option value="" disabled selected>Tipo de Documento</option>
                <option value="ID">ID</option>
                <option value="Passport">Pasaporte</option>
            </select>
        </div>

        <div class="form-group">
            <input type="text" placeholder="Número de Documento" name="documento" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
        </div>

        <div class="form-group">
            <input type="date" placeholder="Fecha de Nacimiento" name="fecha_nac" required>
        </div>

        <div class="form-group">
            <input type="file" name="foto" accept="image/*" required>
        </div>

        <div class="form-group">
            <input type="email" placeholder="Email" name="mail" id="email" required>
            <span id="emailError" class="error-message"></span>
        </div>

        <div class="form-group">
            <input type="tel" placeholder="Teléfono" name="telefono" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
        </div>

        <div class="form-group">
            <input type="text" placeholder="Dirección" name="direccion" required>
        </div>

        <div class="form-group">
            <select name="nivel_educativo" required>
                <option value="" disabled selected>Nivel Educativo</option>
                <option value="primaria">Primaria</option>
                <option value="secundaria">Secundaria</option>
                <option value="terciaria">Terciaria</option>
            </select>
        </div>

        <div class="form-group">
            <select name="genero" required>
                <option value="" disabled selected>Género</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="O">Otro</option>
            </select>
        </div>

        <div class="form-group">
            <textarea placeholder="Observaciones" name="observaciones"></textarea>
        </div>

        <div class="form-group">
            <input type="text" placeholder="Nombre de Usuario" name="username" id="username" required>
            <span id="usernameError" class="error-message"></span>
        </div>

        <div class="form-group">
            <input type="password" placeholder="Contraseña" name="clave" id="password" required>
            <img src="../../login/img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>REGISTRAR ESTUDIANTE</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newStudentForm');
    const submitBtn = document.getElementById('submitBtn');
    const usernameField = document.getElementById('username');
    const emailField = document.getElementById('email');

    function checkFormValidity() {
        if (form.checkValidity() && !document.getElementById('usernameError').textContent && !document.getElementById('emailError').textContent) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    form.addEventListener('input', checkFormValidity);

    usernameField.addEventListener('input', function() {
        const username = this.value;
        const usernameError = document.getElementById('usernameError');

        if (username.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../login/scripts/check_username.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    if (this.responseText === 'taken') {
                        usernameError.textContent = 'El nombre de usuario ya está en uso. Elige otro.';
                        usernameError.style.color = '#c12646';
                    } else {
                        usernameError.textContent = 'El nombre de usuario está disponible.';
                        usernameError.style.color = '#00bcff';
                    }
                    checkFormValidity();
                }
            };

            xhr.send('username=' + encodeURIComponent(username));
        } else {
            usernameError.textContent = '';
            checkFormValidity();
        }
    });

    emailField.addEventListener('input', function() {
        const email = this.value;
        const emailError = document.getElementById('emailError');

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (emailPattern.test(email)) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../login/scripts/check_email.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    if (this.responseText === 'taken') {
                        emailError.textContent = 'El correo electrónico ya está en uso. Elige otro.';
                        emailError.style.color = '#c12646';
                    } else {
                        emailError.textContent = 'El correo electrónico está disponible.';
                        emailError.style.color = '#00bcff';
                    }
                    checkFormValidity();
                }
            };

            xhr.send('email=' + encodeURIComponent(email));
        } else {
            emailError.textContent = 'Introduce un formato de correo electrónico válido.';
            emailError.style.color = '#c12646';
            checkFormValidity();
        }
    });
});

function togglePassword(passwordId, toggleId) {
    const passwordField = document.getElementById(passwordId);
    const lockIcon = document.getElementById(toggleId);

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        lockIcon.src = '../../login/img/eye-close.svg';
        lockIcon.classList.add('rotate');
    } else {
        passwordField.type = 'password';
        lockIcon.src = '../../login/img/eye-open.svg';
        lockIcon.classList.remove('rotate');
    }
}
</script>