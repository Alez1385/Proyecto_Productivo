<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    include "../../scripts/conexion.php";
    include "../../scripts/auth.php";

    $response = ['error' => 'ID de mensaje no proporcionado'];

    if (isset($_GET['id'])) {
        $id_mensaje = $_GET['id'];
        $id_usuario = $_SESSION['id_usuario'] ?? null;

        if ($id_usuario === null) {
            throw new Exception('Usuario no autenticado');
        }

        $query = "SELECT m.*, u.nombre AS remitente_nombre, u.apellido AS remitente_apellido,
                  CASE
                    WHEN m.tipo_destinatario = 'todos' THEN 'Todos'
                    WHEN m.tipo_destinatario = 'estudiantes' THEN 'Todos los Estudiantes'
                    WHEN m.tipo_destinatario = 'profesores' THEN 'Todos los Profesores'
                    WHEN m.tipo_destinatario = 'individual' THEN (SELECT CONCAT(nombre, ' ', apellido) FROM usuario WHERE id_usuario = m.id_destinatario)
                  END AS destinatario
                  FROM mensajes m 
                  JOIN usuario u ON m.id_remitente = u.id_usuario 
                  WHERE m.id_mensaje = ?";

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception('Error en la preparación de la consulta: ' . $conn->error);
        }

        $stmt->bind_param("i", $id_mensaje);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($message = $result->fetch_assoc()) {
            // Registrar la lectura del mensaje
            $insert_query = "INSERT INTO db_gescursoslecturas_mensajes (id_mensaje, id_usuario) VALUES (?, ?) ON DUPLICATE KEY UPDATE fecha_lectura = CURRENT_TIMESTAMP";
            $insert_stmt = $conn->prepare($insert_query);
            if ($insert_stmt === false) {
                throw new Exception('Error en la preparación de la consulta de inserción: ' . $conn->error);
            }
            $insert_stmt->bind_param("ii", $id_mensaje, $id_usuario);
            $insert_stmt->execute();
            $insert_stmt->close();
        
            $response = $message;
        } else {
            $response = ['error' => 'Mensaje no encontrado'];
        }

        $stmt->close();
    }

    $conn->close();
} catch (Exception $e) {
    $response = ['error' => 'Error del servidor: ' . $e->getMessage()];
}

echo json_encode($response);
exit;