<!-- reset_password.php -->
<?php
require '../scripts/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password === $confirmPassword) {
        // Obtener el correo electrónico asociado con el token
        $sql = "SELECT * FROM password_resets WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reset = $result->fetch_assoc();
            $email = $reset['email'];

            // Hash de la nueva contraseña
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Actualizar la contraseña en la base de datos
            $sql = "UPDATE usuario SET clave = ? WHERE correo = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashedPassword, $email);
            $stmt->execute();

            // Eliminar el token para que no pueda ser reutilizado
            $sql = "DELETE FROM password_resets WHERE token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();

            echo "Tu contraseña ha sido actualizada exitosamente.";
        } else {
            echo "Enlace de restablecimiento inválido o expirado.";
        }
    } else {
        echo "Las contraseñas no coinciden.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/style-reset.css">
</head>

<body>
    <div class="login-container">
        <form class="login-form" action="scripts/reset_password.php" method="POST">
            <h2>Reset Password</h2>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="input-group">
                <img src="img/lock-icon.svg" alt="Lock-Icon" class="icon">
                <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock1" onclick="togglePassword('password1', 'toggleLock1')">
                <input type="password" placeholder="New Password" name="password" id="password1" required>
            </div>

            <div class="input-group">
                <img src="img/lock-icon.svg" alt="Lock-Icon" class="icon">
                <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock2" onclick="togglePassword('password2', 'toggleLock2')">
                <input type="password" placeholder="Confirm Password" name="confirm_password" id="password2" required>
            </div>


            <button type="submit" class="btn-login">Reset Password</button>
        </form>
    </div>
    <script src="scripts/script_register.js"></script>
</body>

</html>