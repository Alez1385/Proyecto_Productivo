<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('admin');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_horario'])) {
    $id_horario = intval($_POST['id_horario']);

    // Preparar la consulta SQL para eliminar el horario
    $stmt = $conn->prepare("DELETE FROM horarios WHERE id_horario = ?");
    $stmt->bind_param("i", $id_horario);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Horario eliminado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el horario especificado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el horario: ' . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}

$conn->close();