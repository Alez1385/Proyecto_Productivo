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
$id_mensaje = $input['id_mensaje'] ?? null;
$id_usuario = $input['id_usuario'] ?? $_SESSION['id_usuario'];

if (!$id_mensaje) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de mensaje requerido']);
    exit;
}

try {
    // Verificar si ya existe la lectura
    $existe_lectura = getDatabaseData($conn, "
        SELECT id FROM db_gescursoslecturas_mensajes 
        WHERE id_mensaje = ? AND id_usuario = ?
    ", [$id_mensaje, $id_usuario]);

    if (empty($existe_lectura)) {
        // Insertar nueva lectura
        $stmt = $conn->prepare("
            INSERT INTO db_gescursoslecturas_mensajes (id_mensaje, id_usuario, fecha_lectura) 
            VALUES (?, ?, NOW())
        ");
        $stmt->bind_param("ii", $id_mensaje, $id_usuario);
        $stmt->execute();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Mensaje marcado como leído'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al marcar mensaje como leído: ' . $e->getMessage()
    ]);
}
?> 