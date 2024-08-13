<?php
// Conectar a la base de datos
require '../../scripts/conexion.php';

session_start();

// Obtener los datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Consulta para obtener el usuario
$sql = "SELECT * FROM usuario WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verificar la contraseña (asumiendo que la contraseña está hasheada con bcrypt)
    if (password_verify($password, $user['clave'])) {
        // Autenticación exitosa
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['username'];
        
        // Recordar el usuario si la opción está marcada
        if (isset($_POST['remember'])) {
            setcookie('username', $username, time() + (86400 * 30), "/"); // 30 días
        } else {
            if (isset($_COOKIE['username'])) {
                setcookie('username', '', time() - 3600, "/"); // Eliminar cookie
            }
        }
        
        // Redirigir al usuario al dashboard o página principal
        header("Location: ../../dashboard/dashboard.php");
        exit();
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
} else {
    echo "Usuario o contraseña incorrectos.";
}

// Cerrar la conexión
$stmt->close();
$conn->close();
