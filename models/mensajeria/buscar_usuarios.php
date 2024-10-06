<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

if (isset($_GET['q'])) {
    $busqueda = '%' . $_GET['q'] . '%';
    $query = "SELECT id_usuario, nombre, apellido, mail FROM usuario WHERE mail LIKE ? LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $busqueda);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    
    echo json_encode($usuarios);
    
    $stmt->close();
} else {
    echo json_encode([]);
}

$conn->close();
?>