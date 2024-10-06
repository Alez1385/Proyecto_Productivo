<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (!isset($_SESSION['id_usuario'])) {
            throw new Exception('Usuario no autenticado.');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id_mensaje = $_POST['id_mensaje'];

        if (empty($id_mensaje)) {
            throw new Exception('ID de mensaje no proporcionado.');
        }

        // Verificar si el mensaje ya está marcado como eliminado para este usuario
        $query = "SELECT * FROM mensajes_eliminados WHERE id_usuario = ? AND id_mensaje = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id_usuario, $id_mensaje);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            throw new Exception('El mensaje ya ha sido eliminado por este usuario.');
        }

        // Marcar el mensaje como eliminado para este usuario
        $query = "INSERT INTO mensajes_eliminados (id_usuario, id_mensaje) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id_usuario, $id_mensaje);

        if (!$stmt->execute()) {
            throw new Exception('Error al marcar el mensaje como eliminado: ' . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        echo json_encode(['success' => true, 'message' => 'Mensaje eliminado correctamente.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}