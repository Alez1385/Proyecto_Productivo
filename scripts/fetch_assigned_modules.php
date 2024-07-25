<?php
include "conexion.php";

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    $sql = "SELECT m.nombre_modulo 
            FROM modulos m 
            JOIN asig_modulo am ON m.id_modulo = am.id_modulo 
            WHERE am.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $modules = [];

    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }

    echo json_encode($modules);

    $stmt->close();
}

$conn->close();
