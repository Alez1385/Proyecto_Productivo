<?php
// Versión de prueba simplificada del dashboard de usuario
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';

// Habilitar logging de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

error_log("Dashboard user test - Iniciando");

// Verificar sesión básica
if (!isset($_SESSION['id_usuario'])) {
    error_log("Dashboard user test - No hay sesión activa");
    echo '<h2>No hay sesión activa</h2>';
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
error_log("Dashboard user test - ID usuario: " . $id_usuario);

try {
    // Consulta simple para obtener información del usuario
    $sql_user = "SELECT * FROM usuario WHERE id_usuario = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $id_usuario);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();
    
    error_log("Dashboard user test - Usuario encontrado: " . ($user ? 'Sí' : 'No'));
    
    if (!$user) {
        echo '<h2>Usuario no encontrado</h2>';
        exit;
    }
    
    // Mostrar información básica
    echo '<h1>Dashboard de Usuario - Versión de Prueba</h1>';
    echo '<p>Usuario: ' . htmlspecialchars($user['username']) . '</p>';
    echo '<p>Email: ' . htmlspecialchars($user['mail']) . '</p>';
    echo '<p>Tipo de usuario: ' . htmlspecialchars($user['id_tipo_usuario']) . '</p>';
    
    // Probar la función getProfileIncompleteInfo
    error_log("Dashboard user test - Probando getProfileIncompleteInfo");
    $profileInfo = getProfileIncompleteInfo($user);
    error_log("Dashboard user test - Resultado getProfileIncompleteInfo: " . print_r($profileInfo, true));
    
    echo '<h2>Información del Perfil</h2>';
    echo '<p>Perfil incompleto: ' . ($profileInfo['incompleto'] ? 'Sí' : 'No') . '</p>';
    echo '<p>Campos faltantes: ' . implode(', ', $profileInfo['campos_faltantes']) . '</p>';
    
} catch (Exception $e) {
    error_log("Dashboard user test - Error: " . $e->getMessage());
    echo '<h2>Error: ' . htmlspecialchars($e->getMessage()) . '</h2>';
}
?> 