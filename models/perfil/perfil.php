<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

// Obtener los datos del usuario
$id_usuario = $_SESSION['id_usuario'];
$query = "SELECT id_usuario, nombre, apellido, mail, telefono, direccion, fecha_nac, foto FROM usuario WHERE id_usuario = ?";

$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

if (!$stmt->bind_param("i", $id_usuario)) {
    die("Error en el bind_param: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("Error en la ejecución de la consulta: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result === false) {
    die("Error al obtener el resultado: " . $stmt->error);
}

$usuario = $result->fetch_assoc();
if ($usuario === null) {
    die("No se encontró el usuario con ID: " . $id_usuario);
}

$stmt->close();

$avatar_path = !empty($usuario['foto']) ? '../../uploads/' . htmlspecialchars($usuario['foto']) : '../../assets/default-avatar.png';
error_log("Avatar path: " . $avatar_path); // Esto escribirá la ruta en el log de errores de PHP
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <link rel="stylesheet" href="../estudiante/css/student.css">
    <link rel="stylesheet" href="css/perfil.css">

</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <div class="header">
                <h1>Perfil de Usuario</h1>
            </div>
            <div class="profile-container form-container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="<?php echo $avatar_path; ?>" alt="Avatar" id="avatarImage">
                        <div class="avatar-overlay">
                            <label for="avatarUpload" class="avatar-change-icon">
                                <span class="material-icons-sharp">photo_camera</span>
                            </label>
                            <input type="file" id="avatarUpload" name="avatar" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    <center>
                    <h2><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h2>
                    <p><?php echo htmlspecialchars($usuario['mail']); ?></p>
                    </center>
                </div>
                <form id="profileForm" class="profile-form">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido:</label>
                        <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mail">Email:</label>
                        <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($usuario['mail']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="fecha_nac">Fecha de Nacimiento:</label>
                        <input type="date" id="fecha_nac" name="fecha_nac" value="<?php echo htmlspecialchars($usuario['fecha_nac']); ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-primary">Actualizar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h2>Confirmar actualización</h2>
            <p>¿Estás seguro de que quieres actualizar tu perfil?</p>
            <div class="modal-buttons">
                <button id="confirmUpdate" class="btn-primary">Confirmar</button>
                <button id="cancelUpdate" class="btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>
    <div id="notification" class="notification">
        Perfil actualizado correctamente
    </div>
    <div id="overlay"></div>
    <script src="js/perfil.js"></script>
</body>
</html>