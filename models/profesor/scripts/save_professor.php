<?php
// Adjust the path to correctly point to conexion.php
require_once __DIR__ . '/../../../scripts/conexion.php';

// Check if the connection is established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos del formulario
    $id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : 0;
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    $especialidad = sanitizeInput($_POST['especialidad']);
    $experiencia = sanitizeInput($_POST['experiencia']);
    $descripcion = sanitizeInput($_POST['descripcion']);

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Actualizar el tipo de usuario a profesor (id_tipo_usuario = 2)
        $stmt_update_user = $conn->prepare("UPDATE usuario SET id_tipo_usuario = 2 WHERE id_usuario = ?");
        $stmt_update_user->bind_param("i", $id_usuario);
        if (!$stmt_update_user->execute()) {
            throw new Exception("Error al actualizar el tipo de usuario: " . $stmt_update_user->error);
        }
        $stmt_update_user->close();

        // Si no hay id_profesor, insertamos en la tabla profesor
        if ($id_profesor == 0) {
            $stmt = $conn->prepare("INSERT INTO profesor (id_usuario, especialidad, experiencia, descripcion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $id_usuario, $especialidad, $experiencia, $descripcion);
        } else {
            // Actualizar profesor existente
            $stmt = $conn->prepare("UPDATE profesor SET especialidad = ?, experiencia = ?, descripcion = ? WHERE id_profesor = ?");
            $stmt->bind_param("sssi", $especialidad, $experiencia, $descripcion, $id_profesor);
        }

        if ($stmt->execute()) {
            $conn->commit();
            $response = ['success' => true, 'message' => 'Profesor guardado con éxito'];
        } else {
            throw new Exception("Error al guardar profesor: " . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        $response = ['success' => false, 'message' => $e->getMessage()];
    }

    $conn->close();

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

function sanitizeInput($data) {
    global $conn;
    if ($conn) {
        return $conn->real_escape_string(htmlspecialchars(trim($data)));
    }
    return htmlspecialchars(trim($data));
}
