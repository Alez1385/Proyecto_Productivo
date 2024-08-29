<?php
require_once "../../../scripts/conexion.php";

if (isset($_GET['user_type_id'])) {
    $userTypeId = intval($_GET['user_type_id']);

    // Consulta para obtener los módulos asignados al tipo de usuario
    $sql = "
        SELECT m.nom_modulo 
        FROM modulos m
        JOIN asig_modulo am ON m.id_modulo = am.id_modulo
        WHERE am.id_tipo_usuario = ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userTypeId);
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
?>
