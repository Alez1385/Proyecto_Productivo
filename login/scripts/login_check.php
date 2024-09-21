<?php
// Conectar a la base de datos
require '../../scripts/conexion.php';

session_start();

// Validar y sanitizar las entradas
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    // Si el usuario o la contraseña están vacíos, redirigir con mensaje de error
    header("Location: ../../login.php?error=emptyfields");
    exit();
}

// Preparar la consulta SQL
$sql = "SELECT id_usuario, username, clave FROM usuario WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verificar la contraseña (asumiendo que la contraseña está hasheada con bcrypt)
    if (password_verify($password, $user['clave'])) {
        // Autenticación exitosa, crear sesiones
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_usuario'] = $user['id_usuario'];

        // Depurar la sesión
        if (!isset($_SESSION['id_usuario'])) {
            die("Error: No se pudo establecer la sesión.");
        }

        // Recordar el usuario si la opción está marcada
        if (isset($_POST['remember'])) {
            // Crear una cookie segura disponible para todo el proyecto
            setcookie('username', $username, time() + (86400 * 30), "/", "", isset($_SERVER["HTTPS"]), true); // 30 días, HTTPS y HttpOnly
            setcookie('id_usuario', $user['id_usuario'], time() + (86400 * 30), "/", "", isset($_SERVER["HTTPS"]), true); // 30 días, HTTPS y HttpOnly
        } else {
            // Eliminar la cookie si no se selecciona "recordar usuario"
            if (isset($_COOKIE['username'])) {
                setcookie('username', '', time() - 3600, "/", "", isset($_SERVER["HTTPS"]), true); // Expira la cookie
            }
            if (isset($_COOKIE['id_usuario'])) {
                setcookie('id_usuario', '', time() - 3600, "/", "", isset($_SERVER["HTTPS"]), true); // Expira la cookie
            }
        }

        // Redirigir al usuario al dashboard o página principal
        header("Location: ../../dashboard/dashboard.php");
        exit();
    } else {
        // Contraseña incorrecta, redirigir con mensaje de error
        header("Location: ../../login.php?error=invalidpassword");
        exit();
    }
} else {
    // Usuario no encontrado, redirigir con mensaje de error
    header("Location: ../../login.php?error=nouser");
    exit();
}

// Cerrar la conexión
$stmt->close();
$conn->close();
