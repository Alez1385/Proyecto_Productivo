<?php
include '../../scripts/conexion.php';
// Obtener el ID del usuario desde la solicitud POST
$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;

if ($id_usuario > 0) {
    // Preparar la consulta de eliminación
    $stmt = $conn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);

    if ($stmt->execute()) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'message' => 'Error executing query: ' . $stmt->error];
    }

    $stmt->close();
} else {
    $response = ['success' => false, 'message' => 'Invalid user ID'];
}

$conn->close();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);

