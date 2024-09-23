<?php
session_start();
require '../../scripts/conexion.php';

// Validar y sanitizar las entradas
$username = filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) && $_POST['remember'] === 'on';  // Manejo del checkbox
$redirect = '../../dashboard/dashboard.php';

// Verificar si hay campos vacíos
if (empty($username) || empty($password)) {
    header("Location: ../login.php?error=emptyfields");
    exit();
}

// Preparar la consulta SQL
$sql = "SELECT id_usuario, username, clave FROM usuario WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el usuario existe
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
   
    // Verificar la contraseña hasheada con bcrypt
    if (password_verify($password, $user['clave'])) {
        // Autenticación exitosa, crear sesiones
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_usuario'] = $user['id_usuario'];
        
        // Recordar usuario con cookies si está seleccionado
        if ($remember) {
            setcookie('username', $user['username'], time() + (86400 * 30), "/", "", isset($_SERVER["HTTPS"]), true);
            setcookie('id_usuario', $user['id_usuario'], time() + (86400 * 30), "/", "", isset($_SERVER["HTTPS"]), true);
        }
        
        // Establecer una cookie para indicar que el registro fue exitoso
        setcookie('registration_success', 'true', time() + 3600, '/');
        
        // Redirigir al dashboard
        header("Location: $redirect");
        exit();
    } else {
        // Contraseña incorrecta
        header("Location: ../login.php?error=invalidpassword");
        exit();
    }
} else {
    // Usuario no encontrado
    header("Location: ../login.php?error=nouser");
    exit();
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>