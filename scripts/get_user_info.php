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
    // Obtener información del usuario
    $user_info = getDatabaseData($conn, "
        SELECT u.*, tu.nombre as tipo_nombre
        FROM usuario u
        INNER JOIN tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
        WHERE u.id_usuario = ?
    ", [$id_usuario]);

    if (empty($user_info)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }

    $user = $user_info[0];

    // Calcular días registrado
    $fecha_registro = new DateTime($user['fecha_registro']);
    $hoy = new DateTime();
    $diferencia = $hoy->diff($fecha_registro);
    $user['dias_registrado'] = $diferencia->days;

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener información del usuario: ' . $e->getMessage()
    ]);
}
?> 