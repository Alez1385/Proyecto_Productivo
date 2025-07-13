<?php
require_once 'conexion.php';
require_once 'auth.php';
require_once 'functions.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Obtener datos del POST
$input = json_decode(file_get_contents('php://input'), true);
$id_usuario = $input['id_usuario'] ?? $_SESSION['id_usuario'];
$limit = $input['limit'] ?? 5;

try {
    // Obtener mensajes del usuario
    $mensajes = getDatabaseData($conn, "
        SELECT m.*, u.nombre, u.apellido
        FROM mensajes m
        INNER JOIN usuario u ON m.id_remitente = u.id_usuario
        WHERE m.id_destinatario = ? OR (m.tipo_destinatario = 'todos' AND m.id_remitente != ?)
        ORDER BY m.fecha_envio DESC
        LIMIT ?
    ", [$id_usuario, $id_usuario, $limit]);

    echo json_encode([
        'success' => true,
        'mensajes' => $mensajes
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener mensajes: ' . $e->getMessage()
    ]);
}
?> 