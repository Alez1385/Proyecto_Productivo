<?php
require_once "../../../scripts/conexion.php";

header('Content-Type: application/json');

if (isset($_GET['userTypeId'])) {
    $userTypeId = intval($_GET['userTypeId']);
    
    $sql = "SELECT m.id_modulo, m.nom_modulo 
            FROM modulos m 
            INNER JOIN asig_modulo am ON m.id_modulo = am.id_modulo 
            WHERE am.id_tipo_usuario = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userTypeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $modules = [];
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }
    
    echo json_encode($modules);
} else {
    echo json_encode(['error' => 'No se proporcionó el ID del tipo de usuario']);
}
