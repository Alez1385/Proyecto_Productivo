<?php
require '../../scripts/conexion.php';

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $sql = "SELECT id_usuario FROM usuario WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // El nombre de usuario ya existe
        echo "taken";
    } else {
        // El nombre de usuario está disponible
        echo "available";
    }

    $stmt->close();
    $conn->close();
}
