<?php
// Include database connection
require_once '../../../scripts/conexion.php';
require_once '../../../scripts/error_logger.php';
// Function to log errors

// Set header to return JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if course ID is provided
if (!isset($_POST['id_curso']) || !is_numeric($_POST['id_curso'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
    exit;
}

$id_curso = intval($_POST['id_curso']);

try {
    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM cursos WHERE id_curso = ?");
    $stmt->bind_param("i", $id_curso);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Curso eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontro el curso']);
        }
    } else {
        throw new Exception("Error executing delete query: " . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    logException($e);
    echo json_encode(['success' => false, 'message' => 'Ocurrio un error al eliminar el curso: ' . $e->getMessage()]);
}

$conn->close();
?>