<?php
include '../../../scripts/conexion.php';

// Función para validar y limpiar datos de entrada
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Función para manejar errores
function handleError($message) {
    // En un entorno de producción, deberías registrar el error en un archivo o base de datos
    // y mostrar un mensaje amigable al usuario.
    echo "<p>Error: $message</p>";
    exit();
}

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar datos del formulario
    $id_usuario = isset($_POST['id_usuario']) ? sanitizeInput($_POST['id_usuario']) : '';
    $genero = isset($_POST['genero']) ? sanitizeInput($_POST['genero']) : '';
    $fecha_registro = isset($_POST['fecha_registro']) ? sanitizeInput($_POST['fecha_registro']) : '';
    $estado = isset($_POST['estado']) ? sanitizeInput($_POST['estado']) : '';
    $nivel_educativo = isset($_POST['nivel_educativo']) ? sanitizeInput($_POST['nivel_educativo']) : '';
    $observaciones = isset($_POST['observaciones']) ? sanitizeInput($_POST['observaciones']) : '';

    // Validaciones básicas
    if (empty($id_usuario) || empty($genero) || empty($fecha_registro) || empty($estado) || empty($nivel_educativo)) {
        handleError("All fields are required.");
    }

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM estudiante WHERE id_usuario = ?");
    if ($stmt === false) {
        handleError("Error preparing the statement: " . $conn->error);
    }
    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // El usuario ya existe
        $stmt->close();
        $conn->close();
        echo "<script>alert('El usuario con ID " . $id_usuario ." ya existe'); window.location.href='../estudiante.php';</script>";
    }
    $stmt->close();

    // Preparar la consulta SQL para insertar el nuevo registro
    $stmt = $conn->prepare("INSERT INTO estudiante (id_usuario, genero, fecha_registro, estado, nivel_educativo, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        handleError("Error preparing the statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssssss", $id_usuario, $genero, $fecha_registro, $estado, $nivel_educativo, $observaciones);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "<script>alert('Student registered successfully!'); window.location.href='../estudiante.php';</script>";
    } else {
        handleError("Error executing the query: " . $stmt->error);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
} else {
    handleError("Invalid request method.");
}
