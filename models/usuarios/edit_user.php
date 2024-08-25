<?php
require_once "../../scripts/conexion.php"; 

// Obtener el ID del usuario a editar
$id_usuario = $_GET['id_usuario'];

// Consultar los datos del usuario
$query = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../../css/form.css">
</head>
<body>
    <div class="form-container">
        <form class="edit-form" action="scripts/update_user.php" method="post" enctype="multipart/form-data">
            <h2>Edit User</h2>

            <input type="hidden" name="id_usuario" value="<?php echo $user['id_usuario']; ?>">
        
            <div class="form-group">
                <input type="text" placeholder="First Name" name="nombre" value="<?php echo $user['nombre']; ?>" required>
            </div>
        
            <div class="form-group">
                <input type="text" placeholder="Last Name" name="apellido" value="<?php echo $user['apellido']; ?>" required>
            </div>
        
            <div class="form-group">
                <select name="tipo_doc" required>
                    <option value="" disabled>Document Type</option>
                    <option value="ID" <?php if($user['tipo_doc'] == 'ID') echo 'selected'; ?>>ID</option>
                    <option value="Passport" <?php if($user['tipo_doc'] == 'Passport') echo 'selected'; ?>>Passport</option>
                </select>
            </div>
        
            <div class="form-group">
                <input type="text" placeholder="Document Number" name="documento" value="<?php echo $user['documento']; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
            </div>
        
            <div class="form-group">
                <input type="date" placeholder="Birth Date" name="fecha_nac" value="<?php echo $user['fecha_nac']; ?>" required>
            </div>
        
            <div class="form-group">
                <input type="file" name="foto" accept="image/*">
                <?php if ($user['foto']) : ?>
                    <img src="../../uploads/<?php echo $user['foto']; ?>" alt="User Image" width="100">
                <?php endif; ?>
            </div>
        
            <div class="form-group">
                <input type="email" placeholder="Email" name="mail" value="<?php echo $user['mail']; ?>" required>
            </div>
        
            <div class="form-group">
                <input type="tel" placeholder="Phone Number" name="telefono" value="<?php echo $user['telefono']; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
            </div>
        
            <div class="form-group">
                <input type="text" placeholder="Address" name="direccion" value="<?php echo $user['direccion']; ?>" required>
            </div>
        
            <div class="form-group">
                <select name="id_tipo_usuario" required>
                    <option value="" disabled>User Type</option>
                    <?php
                    $query = "SELECT id_tipo_usuario, nombre FROM tipo_usuario";
                    $result = $conn->query($query);
            
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $selected = $row['id_tipo_usuario'] == $user['id_tipo_usuario'] ? 'selected' : '';
                            echo "<option value='" . $row['id_tipo_usuario'] . "' $selected>" . $row['nombre'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay tipos de usuario disponibles</option>";
                    }
                    ?>
                </select>
            </div>
        
            <div class="form-group">
                <input type="text" placeholder="Username" name="username" id="username" value="<?php echo $user['username']; ?>" required>
                <span id="usernameError" class="error-message"></span>
            </div>
        
            <div class="form-group">
                <input type="password" placeholder="Password" name="clave" id="password">
                <img src="../../login/img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
            </div>

            <!-- Botón para volver -->
            <div class="form-group">
                <a href="users.php" style="color: #00bcff;">Volver</a>
            </div>
            
            <button type="submit" class="btn btn-primary" id="submitBtn">UPDATE</button>
        </form>
    </div>

    <script src="scripts/password.js"></script>
    <script>
        // Aquí se incluye la función para alternar la visibilidad de la contraseña
        function togglePassword(passwordId, toggleId) {
            const passwordField = document.getElementById(passwordId);
            const lockIcon = document.getElementById(toggleId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                lockIcon.src = '../../login/img/eye-close.svg'; // Cambia a ícono de ojo cerrado
                lockIcon.classList.add('rotate');
            } else {
                passwordField.type = 'password';
                lockIcon.src = '../../login/img/eye-open.svg'; // Cambia a ícono de ojo abierto
                lockIcon.classList.remove('rotate');
            }
        }

        // Verificación de nombre de usuario
        document.getElementById('username').addEventListener('input', function () {
            const username = this.value;
            const usernameError = document.getElementById('usernameError');
            const submitBtn = document.getElementById('submitBtn');

            if (username.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'scripts/check_username.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                xhr.onreadystatechange = function () {
                    if (this.readyState === 4) {
                        if (this.status === 200) {
                            if (this.responseText === 'taken') {
                                usernameError.textContent = 'El nombre de usuario ya está en uso. Elige otro.';
                                usernameError.style.color = '#c12646';
                                submitBtn.disabled = true;
                            } else {
                                usernameError.textContent = 'El nombre de usuario está disponible.';
                                usernameError.style.color = '#00bcff';
                                submitBtn.disabled = false;
                            }
                        } else {
                            usernameError.textContent = 'Error al verificar el nombre de usuario. Inténtalo más tarde.';
                            usernameError.style.color = '#c12646';
                            submitBtn.disabled = true;
                        }
                    }
                };

                xhr.onerror = function () {
                    usernameError.textContent = 'Error al conectar con el servidor. Inténtalo más tarde.';
                    usernameError.style.color = '#c12646';
                    submitBtn.disabled = true;
                };

                xhr.send('username=' + encodeURIComponent(username));
            } else {
                usernameError.textContent = '';
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
