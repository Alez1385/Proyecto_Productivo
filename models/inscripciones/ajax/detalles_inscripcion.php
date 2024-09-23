<?php
require_once "../../../scripts/conexion.php";

$id_inscripcion = $_GET['id_inscripcion'];

// Obtener detalles de la inscripción
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

// Obtener historial de cambios
$sql_historial = "SELECT estado_anterior, estado_nuevo, fecha_cambio
                  FROM historial_inscripciones
                  WHERE id_inscripcion = ?
                  ORDER BY fecha_cambio DESC";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("i", $id_inscripcion);
$stmt_historial->execute();
$result_historial = $stmt_historial->get_result();
$historial_cambios = $result_historial->fetch_all(MYSQLI_ASSOC);

// Combinar los resultados
$inscripcion['historial_cambios'] = $historial_cambios;

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($inscripcion);