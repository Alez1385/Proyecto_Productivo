<?php
// Configuración
require_once '../../../scripts/conexion.php';
require_once '../../../scripts/config.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario y sanitizar
    $nombre = sanitizeInput($_POST['nombre']);
    $apellido = sanitizeInput($_POST['apellido']);
    $tipo_doc = sanitizeInput($_POST['tipo_doc']);
    $documento = sanitizeInput($_POST['documento']);
    $fecha_nac = sanitizeInput($_POST['fecha_nac']);
    $mail = sanitizeInput($_POST['mail']);
    $telefono = sanitizeInput($_POST['telefono']);
    $direccion = sanitizeInput($_POST['direccion']);
    $id_tipo_usuario = sanitizeInput($_POST['id_tipo_usuario']);
    $login = sanitizeInput($_POST['username']);
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT); // Hash de la contraseña

    // Manejo de archivo de imagen
    $foto = handleFileUpload($_FILES['foto']);

    // Preparar y ejecutar la consulta
    $sql = "INSERT INTO usuario (nombre, apellido, tipo_doc, documento, fecha_nac, foto, mail, telefono, direccion, id_tipo_usuario, username, clave)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssssssssss', $nombre, $apellido, $tipo_doc, $documento, $fecha_nac, $foto, $mail, $telefono, $direccion, $id_tipo_usuario, $login, $clave);
        if ($stmt->execute()) {
            // Redirigir a la página de inicio de sesión con un mensaje de éxito
            header("Location: ../users.php");
            exit();
        } else {
            logError($stmt->error); // Función para registrar errores en un archivo de log
            showError("Error al crear el usuario. Por favor, inténtelo de nuevo.");
        }
        $stmt->close();
    } else {
        logError("Error en la preparación de la consulta: " . $conn->error);
        showError("Error al crear el usuario. Por favor, inténtelo de nuevo.");
    }
    $conn->close();
} else {
    header("Location: ../users.php");
    exit();
}

// Funciones auxiliares
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

function handleFileUpload($file) {
    $foto_nombre = "";
    if ($file['error'] == 0) {
        $foto_tmp = $file['tmp_name'];
        $foto_nombre = uniqid() . '_' . basename($file['name']);
        $foto_ruta = '../../../uploads/' . $foto_nombre;
       
        if (!move_uploaded_file($foto_tmp, $foto_ruta)) {
            showError("Error al subir la imagen. Inténtelo de nuevo.");
        }
    }
    return $foto_nombre;
}

function logError($error) {
    $log_file = BASE_PATH . 'logs/error.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $error . PHP_EOL, FILE_APPEND);
}

function showError($message) {
    echo "<script>
            alert('$message');
            window.location.href = '../users.php';
          </script>";
    exit();
}
