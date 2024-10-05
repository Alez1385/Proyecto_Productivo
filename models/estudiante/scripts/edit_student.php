<?php
// Configuración
require_once '../../../scripts/conexion.php';
require_once '../../../scripts/config.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario y sanitizar
    $id_estudiante = sanitizeInput($_POST['id_estudiante']);
    $id_usuario = sanitizeInput($_POST['id_usuario']);
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
    $username = sanitizeInput($_POST['username']);
    $clave = !empty($_POST['clave']) ? password_hash($_POST['clave'], PASSWORD_DEFAULT) : null; // Hash de la nueva contraseña si se proporcionó

    // Manejo de archivo de imagen
    $foto = handleFileUpload($_FILES['foto'], $id_usuario, $conn);

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Actualizar la información del usuario en la base de datos
        $sql_usuario = "UPDATE usuario SET 
                nombre = ?, 
                apellido = ?, 
                tipo_doc = ?, 
                documento = ?, 
                fecha_nac = ?, 
                foto = COALESCE(?, foto), 
                mail = ?, 
                telefono = ?, 
                direccion = ?, 
                username = ?, 
                clave = COALESCE(?, clave) 
                WHERE id_usuario = ?";

        $stmt_usuario = $conn->prepare($sql_usuario);
        $stmt_usuario->bind_param('sssssssssssi', $nombre, $apellido, $tipo_doc, $documento, $fecha_nac, $foto, $mail, $telefono, $direccion, $username, $clave, $id_usuario);
        $stmt_usuario->execute();

        // Actualizar la información del estudiante en la base de datos
        $sql_estudiante = "UPDATE estudiante SET 
                           nivel_educativo = ?, 
                           genero = ?, 
                           observaciones = ? 
                           WHERE id_estudiante = ?";

        $stmt_estudiante = $conn->prepare($sql_estudiante);
        $stmt_estudiante->bind_param('sssi', $nivel_educativo, $genero, $observaciones, $id_estudiante);
        $stmt_estudiante->execute();

        // Confirmar transacción
        $conn->commit();

        // Redirigir a la página de estudiantes con un mensaje de éxito
        header("Location: ../estudiante.php?success=1");
        exit();
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        logError($e->getMessage());
        showError("Error al actualizar el estudiante. Por favor, inténtelo de nuevo.");
    }

    $conn->close();
} else {
    header("Location: ../students.php");
    exit();
}

// Funciones auxiliares
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data));
}

function handleFileUpload($file, $id_usuario, $conn)
{
    $foto_nombre = null;
    if ($file['error'] == 0) {
        $foto_tmp = $file['tmp_name'];
        $foto_nombre = uniqid() . '_' . basename($file['name']);
        $foto_ruta = '../../../uploads/' . $foto_nombre;

        if (!move_uploaded_file($foto_tmp, $foto_ruta)) {
            showError("Error al subir la imagen. Inténtelo de nuevo.");
        }

        // Eliminar la imagen antigua si se ha subido una nueva
        $query = "SELECT foto FROM usuario WHERE id_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $foto_antigua = $row['foto'];
            if ($foto_antigua && file_exists('../../../uploads/' . $foto_antigua)) {
                unlink('../../../uploads/' . $foto_antigua); // Elimina la imagen antigua
            }
        }
        $stmt->close();
    }
    return $foto_nombre;
}

function logError($error)
{
    $log_file = BASE_PATH . 'logs/error.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $error . PHP_EOL, FILE_APPEND);
}

function showError($message)
{
    echo "<script>
            alert('$message');
            window.location.href = '../students.php';
          </script>";
    exit();
}