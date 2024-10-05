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
    $nivel_educativo = sanitizeInput($_POST['nivel_educativo']);
    $genero = sanitizeInput($_POST['genero']);
    $observaciones = sanitizeInput($_POST['observaciones']);
    $login = sanitizeInput($_POST['username']);
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT); // Hash de la contraseña

    // Manejo de archivo de imagen
    $foto = handleFileUpload($_FILES['foto']);

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Insertar en la tabla usuario
        $sql_usuario = "INSERT INTO usuario (nombre, apellido, tipo_doc, documento, fecha_nac, foto, mail, telefono, direccion, username, clave, id_tipo_usuario)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_usuario = $conn->prepare($sql_usuario);
        $id_tipo_usuario = 3; // Asumiendo que 3 es el ID para el tipo de usuario "estudiante"
        $stmt_usuario->bind_param('sssssssssssi', $nombre, $apellido, $tipo_doc, $documento, $fecha_nac, $foto, $mail, $telefono, $direccion, $login, $clave, $id_tipo_usuario);
        $stmt_usuario->execute();
        
        $id_usuario = $conn->insert_id;

        // Insertar en la tabla estudiante
        $sql_estudiante = "INSERT INTO estudiante (id_usuario, nivel_educativo, genero, observaciones)
                           VALUES (?, ?, ?, ?)";
        $stmt_estudiante = $conn->prepare($sql_estudiante);
        $stmt_estudiante->bind_param('isss', $id_usuario, $nivel_educativo, $genero, $observaciones);
        $stmt_estudiante->execute();

        // Confirmar transacción
        $conn->commit();

        // Redirigir a la página de estudiantes con un mensaje de éxito
        header("Location: estudiante.php?success=1");
        exit();
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        logError($e->getMessage());
        showError("Error al crear el estudiante. Por favor, inténtelo de nuevo.");
    }

    $conn->close();
} else {
    header("Location: estudiante.php");
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
            window.location.href = 'estudiante.php';
          </script>";
    exit();
}