<?php
require_once '../../scripts/conexion.php';
require_once '../../scripts/functions.php';
require_once '../../scripts/auth.php';

// Ensure the user is logged in
requireLogin();

function sendResponse($success, $message, $data = null)
{
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get user details
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    sendResponse(false, 'Error: Usuario no autenticado.');
}

$user = getUserInfo($conn, $id_usuario);
if (!$user) {
    sendResponse(false, 'Error: Usuario no encontrado. Por favor, contacte al administrador.');
}

$curso_id = filter_input(INPUT_POST, 'curso_id', FILTER_VALIDATE_INT);
if (!$curso_id) {
    sendResponse(false, 'Error: No se especificó un curso válido.');
}

// Process the inscription form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES["comprobante"]) || $_FILES["comprobante"]["error"] !== UPLOAD_ERR_OK) {
        sendResponse(false, 'Error: No se subió ningún archivo o hubo un error en la subida.');
    }

    $target_dir = "../../uploads/comprobantes/";
    $file_name = basename($_FILES["comprobante"]["name"]);
    $target_file = $target_dir . time() . '_' . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file
    $uploadOk = validateFile($_FILES["comprobante"], $imageFileType);
    if (!$uploadOk) {
        sendResponse(false, 'Archivo inválido. Por favor, asegúrate de que sea una imagen (JPG, JPEG, PNG, GIF) y no exceda 500KB.');
    }

    if (!move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {
        $error = error_get_last();
        sendResponse(false, 'Lo siento, hubo un error al subir tu archivo. Error: ' . ($error ? $error['message'] : 'Desconocido'));
    }

    $inscripcionResult = processInscripcion($conn, $curso_id, $id_usuario, $target_file);
    if ($inscripcionResult === true) {
        sendResponse(true, 'Inscripción completada con éxito.');
    } else {
        sendResponse(false, 'Error al procesar la inscripción: ' . $inscripcionResult);
    }
}

// Function to validate the file
function validateFile($file, $fileType)
{
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

// Function to process the inscription
function processInscripcion($conn, $curso_id, $id_usuario, $comprobante)
{
    $conn->begin_transaction();
    try {
        // First, check if the student exists
        $stmt = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
        if (!$stmt) {
            throw new Exception("Error preparando la consulta de estudiante: " . $conn->error);
        }
        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta de estudiante: " . $stmt->error);
        }
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // If the student doesn't exist, create one
            $stmt = $conn->prepare("INSERT INTO estudiante (id_usuario) VALUES (?)");
            if (!$stmt) {
                throw new Exception("Error preparando la inserción de estudiante: " . $conn->error);
            }
            $stmt->bind_param("i", $id_usuario);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la inserción de estudiante: " . $stmt->error);
            }
            $id_estudiante = $stmt->insert_id;
        } else {
            $row = $result->fetch_assoc();
            $id_estudiante = $row['id_estudiante'];
        }

        // Check if there's a pre-inscription for this student and course
        $stmt = $conn->prepare("SELECT id_preinscripcion FROM preinscripciones WHERE id_usuario = ? AND id_curso = ?");
        if (!$stmt) {
            throw new Exception("Error preparando la consulta de preinscripción: " . $conn->error);
        }
        $stmt->bind_param("ii", $id_usuario, $curso_id);
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta de preinscripción: " . $stmt->error);
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If there's a pre-inscription, delete it
            $row = $result->fetch_assoc();
            $id_preinscripcion = $row['id_preinscripcion'];
            $stmt = $conn->prepare("DELETE FROM preinscripciones WHERE id_preinscripcion = ?");
            if (!$stmt) {
                throw new Exception("Error preparando la eliminación de preinscripción: " . $conn->error);
            }
            $stmt->bind_param("i", $id_preinscripcion);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la eliminación de preinscripción: " . $stmt->error);
            }
        }

        // Create a new inscription as pending
        $stmt = $conn->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado, fecha_actualizacion, comprobante_pago) VALUES (?, ?, NOW(), 'Pendiente', NOW(), ?)");
        if (!$stmt) {
            throw new Exception("Error preparando la inserción de inscripción: " . $conn->error);
        }
        $stmt->bind_param("iis", $curso_id, $id_estudiante, $comprobante);
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la inserción de inscripción: " . $stmt->error);
        }

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}
