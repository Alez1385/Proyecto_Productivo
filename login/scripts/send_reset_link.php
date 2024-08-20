<?php
require '../../scripts/conexion.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50)); // Generar un token seguro

    // Verificar si el email existe en la base de datos
    $sql = "SELECT * FROM usuario WHERE mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Guardar el token en la base de datos
        $sql = "INSERT INTO password_resets (email, token) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();

        // Enviar el enlace de restablecimiento de contraseña por correo electrónico usando PHPMailer
        $resetLink = "http://localhost/proyecto_Productivo/login/reset_password.php?token=$token";  //cambiar al desplegar
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $resetLink";

        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alejo13852@gmail.com'; // Cambia a tu dirección de correo
            $mail->Password = 'Santi/08'; // Cambia a tu contraseña de correo
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuración de los destinatarios
            $mail->setFrom('alejo13852@gmail.com', 'Tu Nombre o Empresa'); //cambiar al desplegar
            $mail->addAddress($email); // Destinatario

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            echo "Se ha enviado un enlace de restablecimiento a tu correo electrónico.";
        } catch (Exception $e) {
            echo "Error al enviar el correo electrónico: {$mail->ErrorInfo}";
        }
    } else {
        echo "No existe una cuenta asociada con este correo electrónico.";
    }

    $stmt->close();
    $conn->close();
}
