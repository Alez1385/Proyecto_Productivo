<?php
// dashboard_data.php
// Include necessary files and initialize database connection
require_once '../scripts/config.php';
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';
require_once '../scripts/error_logger.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to send error response
function sendErrorResponse($message, $details = '', $type = 'ERROR') {
    logError($message . ($details ? ': ' . $details : ''), $type);
    http_response_code(400); // Bad Request
    echo json_encode([
        'error' => true,
        'message' => $message,
        'details' => $details,
        'type' => $type
    ]);
    exit;
}

// Check if the user is logged in and is a student
if (!isset($_SESSION['username']) || !checkPermission('estudiante')) {
    sendErrorResponse(
        "No tienes permiso para acceder a esta información.",
        "Unauthorized access attempt: " . ($_SESSION['username'] ?? 'No username'),
        'WARNING'
    );
}

try {
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT e.id_estudiante, e.id_usuario FROM estudiante e
                            INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                            WHERE u.username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        sendErrorResponse(
            "No eres un estudiante.",
            "Non-student user attempted to access dashboard: $username",
            'WARNING'
        );
    }
    
    $row = $result->fetch_assoc();
    $id_estudiante = $row['id_estudiante'];
    $id_usuario = $row['id_usuario'];

    // Fetch inscriptions
    $inscripciones = getDatabaseData($conn, "
        SELECT i.*, c.nombre_curso
        FROM inscripciones i
        INNER JOIN cursos c ON i.id_curso = c.id_curso
        WHERE i.id_estudiante = ?
    ", [$id_estudiante]);

    // Fetch pre-enrollments
    $preinscripciones = getDatabaseData($conn, "
        SELECT p.*, c.nombre_curso
        FROM preinscripciones p
        INNER JOIN cursos c ON p.id_curso = c.id_curso
        WHERE p.id_usuario = ?
    ", [$id_usuario]);

    // Fetch available courses
    $cursos = getDatabaseData($conn, "
        SELECT c.*, cc.nombre_categoria,
        GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios,
        i.estado AS estado_inscripcion,
        p.estado AS estado_preinscripcion,
        p.id_preinscripcion
        FROM cursos c
        LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
        LEFT JOIN horarios h ON c.id_curso = h.id_curso
        LEFT JOIN inscripciones i ON c.id_curso = i.id_curso AND i.id_estudiante = ?
        LEFT JOIN preinscripciones p ON c.id_curso = p.id_curso AND p.id_usuario = ?
        WHERE c.estado = 'activo'
        GROUP BY c.id_curso
    ", [$id_estudiante, $id_usuario]);

    // Prepare the response
    $response = [
        'inscripciones' => $inscripciones,
        'preinscripciones' => $preinscripciones,
        'cursos' => $cursos
    ];

    // Send JSON response
    logError("Dashboard data fetched successfully for user: $username", '', 'INFO');
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    sendErrorResponse(
        "Ha ocurrido un error al procesar tu solicitud.",
        $e->getMessage(),
        'ERROR'
    );
}