<?php
// Configuración
require_once '../../scripts/conexion.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario y sanitizar
    $mail = sanitizeInput($_POST['mail'] ?? '');
    $id_tipo_usuario = !empty($_POST['id_tipo_usuario']) ? (int)$_POST['id_tipo_usuario'] : 1; // Valor por defecto 4
    $login = sanitizeInput($_POST['username'] ?? '');
    
    // Verificar si la clave está presente
    if (!isset($_POST['clave']) || empty($_POST['clave'])) {
        showError("La contraseña es requerida.");
    }
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT); // Hash de la contraseña

    // Verificar que los campos requeridos estén presentes
    if (empty($mail) || empty($login)) {
        showError("El correo electrónico y el nombre de usuario son requeridos.");
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO usuario (mail, id_tipo_usuario, username, clave) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siss', $mail, $id_tipo_usuario, $login, $clave);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir a la página de inicio de sesión con un mensaje de éxito
        header("Location: ../login.php?message=success");
        exit();
    } else {
        $error = "Error al ejecutar la consulta: " . $stmt->error;
        logError($error);
        showError("Error al crear el usuario: " . $error);
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../login.php");
    exit();
}

// Funciones auxiliares
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function logError($error) {
    $log_file = '../../logs/error.log';
    $log_message = date('Y-m-d H:i:s') . " - " . $error . PHP_EOL;
    error_log($log_message, 3, $log_file);
}

function showError($message) {
    echo "<script>
            alert('" . addslashes($message) . "');
            window.location.href = '../login.php';
          </script>";
    exit();
}
?>