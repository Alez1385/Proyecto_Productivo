<?php
require_once('../../scripts/conexion.php');
require_once('../../scripts/functions.php');
require_once('../../scripts/auth.php');

// Ensure user is logged in
requireLogin();

// Initialize variables
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and validate form data
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $curso_id = filter_input(INPUT_GET, 'curso_id', FILTER_VALIDATE_INT);
    
    // Get user ID
    $username = $_SESSION['username'] ?? '';
    $user = getUserDetails($conn, $username);

    if (!$user) {
        respondWithError("Error: No se pudo encontrar el usuario.");
    }

    $id_estudiante = $user['id_estudiante'];

    // Validations
    if (!$nombre || !$email || !$telefono || !$curso_id) {
        respondWithError('Error: Todos los campos son obligatorios y deben ser válidos.');
    }

    // Process file upload
    $target_dir = "../../uploads/comprobantes/";
    $file_name = basename($_FILES["comprobante"]["name"]);
    $target_file = $target_dir . time() . '_' . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file
    $uploadOk = validateFile($_FILES["comprobante"], $imageFileType);

    if ($uploadOk) {
        if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {
            try {
                // Start transaction
                $conn->begin_transaction();

                // Save inscription
                $stmt = $conn->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado, fecha_actualizacion, comprobante_pago) VALUES (?, ?, NOW(), 'pendiente', NOW(), ?)");
                $stmt->bind_param("iis", $curso_id, $id_estudiante, $file_name);

                if ($stmt->execute()) {
                    // Commit transaction
                    $conn->commit();

                    $inscripcion_id = $stmt->insert_id;
                    $success_message = "Inscripción completada con éxito. ID de inscripción: " . $inscripcion_id;

                    // Redirect after success
                    header("Location: ../../dashboard/dashboard.php?success=" . urlencode($success_message));
                    exit;
                } else {
                    throw new Exception("Error al procesar la inscripción: " . $stmt->error);
                }
            } catch (Exception $e) {
                // Rollback in case of error
                $conn->rollback();
                respondWithError("Error en la inscripción: " . $e->getMessage());
            }
        } else {
            respondWithError("Lo siento, hubo un error al subir tu archivo.");
        }
    } else {
        respondWithError("El archivo no cumple con los requisitos.");
    }
}

// Helper functions
function validateFile($file, $fileType) {
    $maxFileSize = 500000; // 500KB
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }

    if ($file['size'] > $maxFileSize) {
        return false;
    }

    $check = getimagesize($file["tmp_name"]);
    return $check !== false;
}

function respondWithError($message) {
    echo json_encode(['error' => $message]);
    exit;
}

function getUserDetails($conn, $username) {
    $stmt = $conn->prepare("SELECT u.id_usuario, e.id_estudiante FROM usuario u LEFT JOIN estudiante e ON u.id_usuario = e.id_usuario WHERE u.username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>