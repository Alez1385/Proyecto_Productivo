<?php
require_once '../../scripts/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validación y sanitización de datos
    $id_curso = filter_input(INPUT_POST, 'id_curso', FILTER_VALIDATE_INT);
    $id_profesor = filter_input(INPUT_POST, 'id_profesor', FILTER_VALIDATE_INT);
    $id_estudiante = filter_input(INPUT_POST, 'id_estudiante', FILTER_VALIDATE_INT);
    $fecha_asignacion = filter_input(INPUT_POST, 'fecha_asignacion', FILTER_SANITIZE_STRING);
    $comentarios = filter_input(INPUT_POST, 'comentarios', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

    // Verificar que los datos requeridos no estén vacíos
    if ($id_curso && $id_profesor && $id_estudiante && $fecha_asignacion && $estado) {
        try {
            // Preparar la consulta
            $stmt = $conn->prepare("INSERT INTO asignacion_curso (id_curso, id_profesor, id_estudiante, fecha_asignacion, comentarios, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisss", $id_curso, $id_profesor, $id_estudiante, $fecha_asignacion, $comentarios, $estado);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Redireccionar a una página de éxito o mostrar un mensaje de éxito
                header("Location: success.php");
                exit;
            } else {
                // Manejo de errores en caso de fallo en la ejecución
                echo "Error: No se pudo asignar el curso. Por favor, inténtelo de nuevo.";
            }
        } catch (Exception $e) {
            // Manejo de excepciones para errores inesperados
            error_log($e->getMessage());
            echo "Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.";
        } finally {
            // Cerrar la declaración
            $stmt->close();
        }
    } else {
        // Mensaje de error si algún campo obligatorio no está presente
        echo "Por favor, complete todos los campos obligatorios.";
    }
}

// Cerrar la conexión
$conn->close();
?>
