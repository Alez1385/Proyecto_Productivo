<?php
include_once('../../scripts/conexion.php');

// Inicializar variables de error y éxito
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y validar los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $curso_id = trim($_GET['curso_id'] ?? '');
    
    $username = trim($_SESSION['username'] ?? '');
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $id_usuario = $row['id_usuario'];
            $stmt = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row) {
                $id_estudiante = $row['id_estudiante'];
            }
        } else {
            $error_message = "Error: No se pudo encontrar el usuario.";
        }

    // Validaciones
    if (empty($nombre) || empty($email) || empty($telefono)) {
        $error_message = 'Error: Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Error: El email proporcionado no es válido.';
    } else {
        // Procesar el archivo subido
        $target_dir = "../../uploads/comprobantes/";
        $file_name = basename($_FILES["comprobante"]["name"]);
        $target_file = $target_dir . time() . '_' . $file_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Comprobar si el archivo es una imagen real o un archivo falso
        $check = getimagesize($_FILES["comprobante"]["tmp_name"]);
        if ($check === false) {
            $error_message = "El archivo no es una imagen.";
            $uploadOk = 0;
        }

        // Comprobar el tamaño del archivo
        if ($_FILES["comprobante"]["size"] > 500000) {
            $error_message = "Lo siento, tu archivo es demasiado grande.";
            $uploadOk = 0;
        }

        // Permitir ciertos formatos de archivo
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error_message = "Lo siento, solo se permiten archivos JPG, JPEG, PNG & GIF.";
            $uploadOk = 0;
        }

        // Comprobar si $uploadOk está establecido en 0 por un error
        if ($uploadOk == 0) {
            $error_message = "Lo siento, tu archivo no fue subido.";
        } else {
            // Intentar mover el archivo a la carpeta de destino
            if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {
                try {
                    // Iniciar la transacción
                    $conn->begin_transaction();

                    // Preparar la consulta para guardar la inscripción
                    $stmt = $conn->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado, fecha_actualizacion, comprobante_pago) VALUES (?, ?, NOW(), 'pendiente', NOW(), ?)");
                    $stmt->bind_param("iis", $curso_id, $id_estudiante, $file_name);

                    // Ejecutar la consulta
                    if ($stmt->execute()) {
                        // Commit de la transacción
                        $conn->commit();

                        $inscripcion_id = $stmt->insert_id;
                        $success_message = "Inscripción completada con éxito. ID de inscripción: " . $inscripcion_id;

                        // Redirigir tras el éxito
                        header("Location: ../../dashboard/dashboard.php");
                        exit;
                    } else {
                        throw new Exception("Error al procesar la inscripción: " . $stmt->error);
                    }
                } catch (Exception $e) {
                    // Rollback en caso de error
                    $conn->rollback();
                    $error_message = "Error en la inscripción: " . $e->getMessage();
                }
            } else {
                $error_message = "Lo siento, hubo un error al subir tu archivo.";
            }
        }
    }
}

// Enviar mensaje de error si existe
if (!empty($error_message)) {
    echo json_encode(['error' => $error_message]);
}
?>
