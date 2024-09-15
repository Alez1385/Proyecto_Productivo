<?php
require_once "../../../scripts/conexion.php";

$id_inscripcion = $_GET['id_inscripcion'];
$sql = "SELECT i.id_inscripcion, i.fecha_inscripcion, i.estado, c.nombre_curso, u.nombre, u.apellido
        FROM inscripciones i
        JOIN cursos c ON i.id_curso = c.id_curso
        JOIN estudiante e ON i.id_estudiante = e.id_estudiante
        JOIN usuario u ON e.id_usuario = u.id_usuario
        WHERE i.id_inscripcion = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_inscripcion);
$stmt->execute();
$result = $stmt->get_result();
$inscripcion = $result->fetch_assoc();

echo json_encode($inscripcion);
