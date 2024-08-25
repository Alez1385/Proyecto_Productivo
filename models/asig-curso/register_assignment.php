<?php
require_once '../../scripts/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validación y sanitización de datos
    $id_curso = filter_input(INPUT_POST, 'id_curso', FILTER_VALIDATE_INT);
    $id_profesor = filter_input(INPUT_POST, 'id_profesor', FILTER_VALIDATE_INT);
    $id_estudiante = filter_input(INPUT_POST, 'id_estudiante', FILTER_VALIDATE_INT);
    $fecha_asignacion = trim(filter_input(INPUT_POST, 'fecha_asignacion', FILTER_SANITIZE_SPECIAL_CHARS));
    $comentarios = trim(filter_input(INPUT_POST, 'comentarios', FILTER_SANITIZE_SPECIAL_CHARS));
    $estado = trim(filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS));

    // Verificar que los datos requeridos no estén vacíos
    if ($id_curso && $id_profesor && $id_estudiante && $fecha_asignacion && $estado) {
        // Validación de la fecha
        $fecha_valida = DateTime::createFromFormat('Y-m-d', $fecha_asignacion);
        if ($fecha_valida && $fecha_valida->format('Y-m-d') === $fecha_asignacion) {
            try {
                // Preparar la consulta
                if ($stmt = $conn->prepare("INSERT INTO asignacion_curso (id_curso, id_profesor, id_estudiante, fecha_asignacion, comentarios, estado) VALUES (?, ?, ?, ?, ?, ?)")) {
                    $stmt->bind_param("iiisss", $id_curso, $id_profesor, $id_estudiante, $fecha_asignacion, $comentarios, $estado);

                    // Ejecutar la consulta
                    if ($stmt->execute()) {
                        header("Location: success.php");
                        exit;
                    } else {
                        echo "Error: No se pudo asignar el curso. Por favor, inténtelo de nuevo.";
                    }
                } else {
                    echo "Error en la preparación de la consulta. Por favor, inténtelo de nuevo.";
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                echo "Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde. Detalles: " . $e->getMessage();
            
            } finally {
                if (isset($stmt) && $stmt !== false) {
                    $stmt->close();
                }
            }
        } else {
            echo "Fecha de asignación no es válida. Debe estar en el formato 'YYYY-MM-DD'.";
        }
    } else {
        echo "Por favor, complete todos los campos obligatorios.";
    }
}

$conn->close();
?>
