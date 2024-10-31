<?php
require_once '../../scripts/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_profesor = $_POST['id_profesor'];
    $dia = $_POST['dia'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    $sql = "SELECT * FROM horarios 
            WHERE id_profesor = ? 
            AND $dia IS NOT NULL 
            AND (
                (STR_TO_DATE($dia, '%H:%i') < ? AND STR_TO_DATE($dia, '%H:%i') > ?)
                OR (STR_TO_DATE($dia, '%H:%i') >= ? AND STR_TO_DATE($dia, '%H:%i') < ?)
                OR (? >= STR_TO_DATE($dia, '%H:%i') AND ? < STR_TO_DATE($dia, '%H:%i'))
            )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $id_profesor, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin);
    $stmt->execute();
    $result = $stmt->get_result();

    $disponible = $result->num_rows === 0;

    echo json_encode(['disponible' => $disponible]);
} else {
    echo json_encode(['error' => 'Método no permitido']);
}