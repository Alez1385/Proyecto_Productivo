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

try {
    // Contar mensajes no leídos
    $mensajes_no_leidos = getDatabaseData($conn, "
        SELECT COUNT(*) as count
        FROM mensajes m
        LEFT JOIN db_gescursoslecturas_mensajes ml ON m.id_mensaje = ml.id_mensaje AND ml.id_usuario = ?
        WHERE (m.id_destinatario = ? OR (m.tipo_destinatario = 'todos' AND m.id_remitente != ?))
        AND ml.id_mensaje IS NULL
    ", [$id_usuario, $id_usuario, $id_usuario]);

    $count = $mensajes_no_leidos[0]['count'] ?? 0;

    echo json_encode([
        'success' => true,
        'count' => $count
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener conteo de mensajes: ' . $e->getMessage()
    ]);
}
?> 