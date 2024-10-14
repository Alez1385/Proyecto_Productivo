<?php
require_once '../../../scripts/conexion.php';
require_once '../../../scripts/error_logger.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y sanitizar la entrada
    $id_curso = filter_input(INPUT_POST, 'id_curso', FILTER_SANITIZE_NUMBER_INT);
    $id_estudiante = filter_input(INPUT_POST, 'id_estudiante', FILTER_SANITIZE_NUMBER_INT);
    $comprobante_pago = $_FILES['comprobante_pago'] ?? null;  

    if (!$comprobante_pago) {
        logError("No se ha cargado ningún comprobante de pago.", "");
        sendJsonResponse(false, "No se ha cargado ningún comprobante de pago.");
        exit;
    }

    // Validar entrada
    if (!$id_curso || !$id_estudiante) {
        logError("Datos de entrada inválidos: id_curso = $id_curso, id_estudiante = $id_estudiante", "");
        sendJsonResponse(false, "Datos de entrada inválidos. Por favor, verifica los campos del formulario.");
        exit;
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Verificar si el estudiante ya está inscrito en el curso
        $sql_check = "SELECT estado FROM inscripciones WHERE id_curso = ? AND id_estudiante = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $id_curso, $id_estudiante);
        $stmt_check->execute();
        $stmt_check->bind_result($estado);
        $stmt_check->fetch();
        $stmt_check->close();

        // Permitir crear un nuevo registro si el estado es 'cancelado' o 'rechazado'
        if ($estado === 'aprobada' || $estado === 'pendiente') {
            logError("El estudiante ya está inscrito en este curso.", "", "INFO");
            throw new Exception("El estudiante ya está inscrito en este curso.");
        }

        // Manejo del archivo de comprobante de pago
        $comprobante_pago_path = handleFileUpload($comprobante_pago);

        if (!$comprobante_pago_path) {
            throw new Exception("Error al subir el comprobante de pago.");
        }

        // Preparar declaración SQL
        $sql = "INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado, comprobante_pago) 
                VALUES (?, ?, CURDATE(), 'pendiente', ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $id_curso, $id_estudiante, $comprobante_pago_path);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la declaración: " . $stmt->error);
        }

        // Confirmar transacción
        $conn->commit();
        logError("Inscripción creada con éxito.");
        sendJsonResponse(true, "Inscripción creada con éxito.");

    } catch (Exception $e) {
        // En caso de error, hacer rollback
        $conn->rollback();
        logError($e->getMessage(), "");
        sendJsonResponse(false, "Error: " . $e->getMessage());
    } finally {
        // Cerrar la declaración
        if (isset($stmt)) $stmt->close();
    }
} else {
    sendJsonResponse(false, "Método de solicitud inválido.");
}

// Función para enviar respuesta JSON
function sendJsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
}

function handleFileUpload($file) {
    $target_dir = "../../../uploads/comprobantes/";
    $file_name = uniqid() . '_' . basename($file["name"]);
    $target_file = $target_dir . $file_name;

    if ($file['error'] != 0) {
        logError("Error en el archivo: " . $file['error'], "");
        return null;
    }

    if (!move_uploaded_file($file["tmp_name"], $target_file)) {
        logError("Error al mover el archivo: " . $file["name"], "");
        return null;
    }

    return $target_file;
}
