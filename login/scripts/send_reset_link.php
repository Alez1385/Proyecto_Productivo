<?php
// File: scripts/send_reset_link.php
require_once '../../scripts/conexion.php';
require_once '../../scripts/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit;
}

try {
    $token = bin2hex(random_bytes(32));

    // Verificar si el correo existe en la base de datos
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'No account found with this email address']);
        exit;
    }
    
    // Obtener el id_usuario del resultado
    $userData = $result->fetch_assoc();
    $id_usuario = $userData['id_usuario'];
    
    // Insertar o actualizar el token en la base de datos
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, created_at, id_usuario) VALUES (?, ?, NOW(), ?) ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = NOW()");
    $stmt->bind_param("ssi", $email, $token, $id_usuario);
    $stmt->execute();
    
    
    if ($stmt->affected_rows === 0) {
        throw new Exception('Failed to save reset token');
    }
    
    // Enviar el correo de restablecimiento de contraseña
    sendEmail($email, 'reset', $token);
    
    http_response_code(200);
    echo json_encode(['message' => 'Password reset link sent successfully']);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred. Please try again later.']);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}