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
<div class="form-container">
<form class="edit-form" action="scripts/edit_user.php" method="post" enctype="multipart/form-data">
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
            <option value="ID" <?php if ($user['tipo_doc'] == 'ID') echo 'selected'; ?>>ID</option>
            <option value="Passport" <?php if ($user['tipo_doc'] == 'Passport') echo 'selected'; ?>>Passport</option>
        </select>
    </div>

    <div class="form-group">
        <input type="text" placeholder="Document Number" name="documento" value="<?php echo $user['documento']; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
    </div>

    <div class="form-group">
        <input type="date" placeholder="Birth Date" name="fecha_nac" value="<?php echo $user['fecha_nac']; ?>" required>
    </div>

    <div class="form-group">
        <input type="file" name="foto" accept="image/*" id="fotoInput" onchange="previewImage(this);">
        <div id="imagePreview">
            <?php if ($user['foto']) : ?>
                <img src="../../uploads/<?php echo $user['foto']; ?>" alt="User Image" width="100" id="previewImg">
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <input type="email" placeholder="Email" name="mail" id="email" value="<?php echo $user['mail']; ?>" required>
        <span id="emailError" class="error-message"></span>
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


    <button type="submit" class="btn btn-primary" id="submitBtn">UPDATE</button>
</form>
</div>

