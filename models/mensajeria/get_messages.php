<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {
    $query = "SELECT m.id_mensaje, m.asunto, m.contenido, m.fecha_envio, 
              CASE 
                WHEN m.id_destinatario = ? THEN 'recibido'
                WHEN m.id_remitente = ? THEN 'enviado'
                WHEN m.tipo_destinatario = 'todos' THEN 'recibido'
                ELSE 'otro'
              END AS tipo,
              u.nombre AS nombre_remitente, u.apellido AS apellido_remitente
              FROM mensajes m
              LEFT JOIN usuario u ON m.id_remitente = u.id_usuario
              WHERE (m.id_destinatario = ? 
                 OR m.id_remitente = ? 
                 OR m.id_tipo_usuario IN (SELECT id_tipo_usuario FROM usuario WHERE id_usuario = ?)
                 OR m.tipo_destinatario = 'todos')
              AND m.id_mensaje NOT IN (SELECT id_mensaje FROM mensajes_eliminados WHERE id_usuario = ?)
              ORDER BY m.fecha_envio DESC";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("iiiiii", $id_usuario, $id_usuario, $id_usuario, $id_usuario, $id_usuario, $id_usuario);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true, 'data' => $messages]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}