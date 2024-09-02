<?php
// Database connection
require_once '../../../scripts/conexion.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id_curso = isset($_POST['id_curso']) ? intval($_POST['id_curso']) : 0;
    $nombre_curso = isset($_POST['nombre_curso']) ? trim($_POST['nombre_curso']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $nivel_educativo = isset($_POST['nivel_educativo']) ? trim($_POST['nivel_educativo']) : '';
    $duracion = isset($_POST['duracion']) ? intval($_POST['duracion']) : 0;
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';

    // Validate input
    if ($id_curso <= 0 || empty($nombre_curso) || empty($descripcion) || empty($nivel_educativo) || $duracion <= 0 || empty($estado)) {
        die("Invalid input. Please fill all required fields.");
    }

    // Prepare and execute the update query
    $stmt = $conn->prepare("UPDATE cursos SET nombre_curso = ?, descripcion = ?, nivel_educativo = ?, duracion = ?, estado = ? WHERE id_curso = ?");
    $stmt->bind_param("sssisi", $nombre_curso, $descripcion, $nivel_educativo, $duracion, $estado, $id_curso);

    if ($stmt->execute()) {
        // Redirect to a success page or back to the course list
        header("Location: ../cursos.php?update=success");
        exit();
    } else {
        // If there was an error, display it
        die("Error updating record: " . $conn->error);
    }

    $stmt->close();
} else {
    // If the script was accessed directly without form submission
    die("Invalid access method. Please submit the form.");
}

$conn->close();
?>