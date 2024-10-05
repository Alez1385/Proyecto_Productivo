<?php
include '../../scripts/conexion.php';

// Obtener el ID del profesor desde la solicitud POST
$id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : 0;

if ($id_profesor > 0) {
    // Preparar la consulta de eliminación
    $stmt = $conn->prepare("DELETE FROM profesor WHERE id_profesor = ?");
    $stmt->bind_param("i", $id_profesor);
    
    if ($stmt->execute()) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'message' => 'Error executing query: ' . $stmt->error];
    }
    $stmt->close();
} else {
    $response = ['success' => false, 'message' => 'Invalid professor ID'];
}

$conn->close();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);