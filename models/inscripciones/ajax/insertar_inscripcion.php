<?php
require_once '../../../scripts/conexion.php';
// Asumiendo que ya has establecido una conexión a la base de datos
// $conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y sanitizar la entrada
    $id_curso = filter_input(INPUT_POST, 'id_curso', FILTER_SANITIZE_NUMBER_INT);
    $id_estudiante = filter_input(INPUT_POST, 'id_estudiante', FILTER_SANITIZE_NUMBER_INT);

    // Validar entrada
    if (!$id_curso || !$id_estudiante) {
        sendJsonResponse(false, "Datos de entrada inválidos. Por favor, verifica los campos del formulario.");
        exit;
    }

    // Verificar si el estudiante ya está inscrito en el curso
    $sql_check = "SELECT COUNT(*) FROM inscripciones WHERE id_curso = ? AND id_estudiante = ?";
    $stmt_check = $conn->prepare($sql_check);
    if (!$stmt_check) {
        sendJsonResponse(false, "Error al preparar la verificación: " . $conn->error);
        exit;
    }
    $stmt_check->bind_param("ii", $id_curso, $id_estudiante);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        sendJsonResponse(false, "El estudiante ya está inscrito en este curso.");
        exit;
    }

    // Preparar declaración SQL
    $sql = "INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado) VALUES (?, ?, CURDATE(), 'pendiente')";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        sendJsonResponse(false, "Error al preparar la declaración: " . $conn->error);
        exit;
    }

    // Vincular parámetros y ejecutar
    $stmt->bind_param("ii", $id_curso, $id_estudiante);
    $inscripcionExitosa = $stmt->execute();

    if ($inscripcionExitosa) {
        $inscripcion_id = $stmt->insert_id;
        sendJsonResponse(true, "Inscripción creada exitosamente", ['id_inscripcion' => $inscripcion_id]);
    } else {
        // Manejar errores específicos
        if ($stmt->errno == 1062) {
            sendJsonResponse(false, "Ya existe una inscripción para este estudiante en este curso.");
        } elseif ($stmt->errno == 1452) {
            sendJsonResponse(false, "El curso o el estudiante especificado no existe.");
        } else {
            sendJsonResponse(false, "Error al ejecutar la declaración: " . $stmt->error . " (Código: " . $stmt->errno . ")");
        }
    }

    $stmt->close();
} else {
    sendJsonResponse(false, "Método de solicitud inválido.");
}

function sendJsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
}