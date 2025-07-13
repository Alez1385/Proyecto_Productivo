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

try {
    // Contar cursos disponibles
    $cursos_disponibles = getDatabaseData($conn, "
        SELECT COUNT(*) as count
        FROM cursos
        WHERE estado = 'activo'
    ");

    $count = $cursos_disponibles[0]['count'] ?? 0;

    echo json_encode([
        'success' => true,
        'count' => $count
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener conteo de cursos: ' . $e->getMessage()
    ]);
}
?> 