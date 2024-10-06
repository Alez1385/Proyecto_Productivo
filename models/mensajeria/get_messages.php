<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

// Verificar autenticación
if (!authenticateUser()) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

// El resto del código para obtener mensajes...
$query = "SELECT m.id_mensaje, m.asunto, m.fecha_envio, u.nombre AS remitente_nombre, u.apellido AS remitente_apellido
          FROM mensajes m
          JOIN usuario u ON m.id_remitente = u.id_usuario
          WHERE m.id_destinatario = ? OR m.tipo_destinatario IN ('todos', 'estudiantes', 'profesores')
          ORDER BY m.fecha_envio DESC"; // Añadimos ORDER BY para ordenar por fecha descendente

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $messages]);
?>