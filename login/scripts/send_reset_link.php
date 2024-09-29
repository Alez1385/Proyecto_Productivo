<?php
// File: scripts/send_reset_link.php (previously request_reset.php)
require_once '../../scripts/conexion.php';
require_once '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'No account found with this email address']);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = NOW()");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception('Failed to save reset token');
    }
    
    $resetLink = "https://corsacor.com/login/reset_password.php?token=" . urlencode($token);
    $subject = "Password Reset Request";
    $message = "Click the following link to reset your password: $resetLink";
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'alejo13852@gmail.com';
    $mail->Password = 'nqpg trtb sidj awvg';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('alejo13852@gmail.com', 'CorsaCor');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->AltBody = strip_tags($message);
    
    $mail->send();
    
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