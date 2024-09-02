<?php
require_once "../../scripts/conexion.php";

if (isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);

    // Expresión regular para validar el formato del correo electrónico en el servidor
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'invalid';
        exit;
    }

    $query = "SELECT COUNT(*) as count FROM usuario WHERE mail = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo 'taken';
    } else {
        echo 'available';
    }

    $stmt->close();
}

$conn->close();
