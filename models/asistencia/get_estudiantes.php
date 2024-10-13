<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
requireLogin();
checkPermission('profesor');

$id_curso = $_GET['id_curso'];
$fecha = $_GET['fecha'];

$sql = "SELECT e.id_estudiante, u.nombre, u.apellido, a.presente
        FROM estudiante e
        INNER JOIN usuario u ON e.id_usuario = u.id_usuario
        INNER JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
        LEFT JOIN asistencia a ON e.id_estudiante = a.id_estudiante AND a.id_curso = ? AND a.fecha = ?
        WHERE i.id_curso = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $id_curso, $fecha, $id_curso);
$stmt->execute();
$result = $stmt->get_result();

$estudiantes = [];
while ($row = $result->fetch_assoc()) {
    $estudiantes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($estudiantes);
