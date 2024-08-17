<?php
require '../../scripts/conexion.php';

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

        // Enviar el enlace de restablecimiento de contraseña por correo electrónico
        $resetLink = "localhost/proyecto_Productivo/login/reset_password.php?token=$token";  //cambiar al desplegar
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $resetLink";
        $headers = "From: no-reply@tu-dominio.com"; //cambiar al desplegar

        if (mail($email, $subject, $message, $headers)) {
            echo "Se ha enviado un enlace de restablecimiento a tu correo electrónico.";
        } else {
            echo "Error al enviar el correo electrónico.";
        }
    } else {
        echo "No existe una cuenta asociada con este correo electrónico.";
    }

    $stmt->close();
    $conn->close();
}
