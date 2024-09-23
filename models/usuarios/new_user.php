<?php
require_once "../../scripts/conexion.php";
?>

<div class="form-container">
    <form class="register-form" action="scripts/save_user.php" method="post" enctype="multipart/form-data" id="newUserForm">
        <h2>Nuevo usuario</h2>

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
                $stmt = $conn->prepare("SELECT id_tipo_usuario, nombre FROM tipo_usuario");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id_tipo_usuario'] . "'>" . $row['nombre'] . "</option>";
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
            <img src="../../login/img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>REGISTRO</button>
    </form>
</div>
