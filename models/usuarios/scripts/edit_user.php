<?php
// Configuración
require_once '../../../scripts/conexion.php';
require_once '../../../scripts/config.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario y sanitizar
    $id_usuario = sanitizeInput($_POST['id_usuario']);
    $nombre = sanitizeInput($_POST['nombre']);
    $apellido = sanitizeInput($_POST['apellido']);
    $tipo_doc = sanitizeInput($_POST['tipo_doc']);
    $documento = sanitizeInput($_POST['documento']);
    $fecha_nac = sanitizeInput($_POST['fecha_nac']);
    $mail = sanitizeInput($_POST['mail']);
    $telefono = sanitizeInput($_POST['telefono']);
    $direccion = sanitizeInput($_POST['direccion']);
    $id_tipo_usuario = sanitizeInput($_POST['id_tipo_usuario']);
    $username = sanitizeInput($_POST['username']);
    $clave = !empty($_POST['clave']) ? password_hash($_POST['clave'], PASSWORD_DEFAULT) : null; // Hash de la nueva contraseña si se proporcionó

    // Manejo de archivo de imagen
    $foto = handleFileUpload($_FILES['foto'], $id_usuario, $conn);

    // Actualizar la información del usuario en la base de datos
    $sql = "UPDATE usuario SET 
            nombre = ?, 
            apellido = ?, 
            tipo_doc = ?, 
            documento = ?, 
            fecha_nac = ?, 
            foto = COALESCE(?, foto), 
            mail = ?, 
            telefono = ?, 
            direccion = ?, 
            id_tipo_usuario = ?, 
            username = ?, 
            clave = COALESCE(?, clave) 
            WHERE id_usuario = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssssssssssi', $nombre, $apellido, $tipo_doc, $documento, $fecha_nac, $foto, $mail, $telefono, $direccion, $id_tipo_usuario, $username, $clave, $id_usuario);
        if ($stmt->execute()) {
            // Verificar si el tipo de usuario ha cambiado a profesor
            if ($id_tipo_usuario == 2) { // Asumiendo que 2 es el ID para profesor
                // Verificar si ya existe un registro en la tabla profesor para este usuario
                $check_profesor = $conn->prepare("SELECT id_profesor FROM profesor WHERE id_usuario = ?");
                $check_profesor->bind_param("i", $id_usuario);
                $check_profesor->execute();
                $result = $check_profesor->get_result();
                
                if ($result->num_rows == 0) {
                    // Si no existe, crear un nuevo registro en la tabla profesor
                    $insert_profesor = $conn->prepare("INSERT INTO profesor (id_usuario, especialidad, experiencia, descripcion) VALUES (?, '', 0, '')");
                    $insert_profesor->bind_param("i", $id_usuario);
                    if (!$insert_profesor->execute()) {
                        logError("Error al crear registro de profesor: " . $insert_profesor->error);
                        showError("Error al actualizar el usuario como profesor. Por favor, inténtelo de nuevo.");
                    }
                    $insert_profesor->close();
                }
                $check_profesor->close();
            }
            // Aquí puedes agregar lógica similar para otros tipos de usuario y sus tablas correspondientes

            // Redirigir a la página de usuarios con un mensaje de éxito
            header("Location: ../users.php");
            exit();
        } else {
            logError($stmt->error);
            showError("Error al actualizar el usuario. Por favor, inténtelo de nuevo.");
        }
        $stmt->close();
    } else {
        logError("Error en la preparación de la consulta: " . $conn->error);
        showError("Error al actualizar el usuario. Por favor, inténtelo de nuevo.");
    }
    $conn->close();
} else {
    header("Location: ../users.php");
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
            window.location.href = '../users.php';
          </script>";
    exit();
}
