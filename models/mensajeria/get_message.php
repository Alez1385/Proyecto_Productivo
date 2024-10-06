<?php
include "../../scripts/conexion.php";
include "../../scripts/sesion.php";

if (isset($_GET['id'])) {
    $id_mensaje = $_GET['id'];
    $id_usuario = $_SESSION['id_usuario'];

    $query = "SELECT m.*, u.nombre AS remitente,
              CASE
                WHEN m.tipo_destinatario = 'todos' THEN 'Todos'
                WHEN m.tipo_destinatario = 'estudiantes' THEN 'Todos los Estudiantes'
                WHEN m.tipo_destinatario = 'profesores' THEN 'Todos los Profesores'
                WHEN m.tipo_destinatario = 'users' THEN 'Todos los Usuarios'
                WHEN m.tipo_destinatario = 'individual' THEN (SELECT CONCAT(nombre, ' ', apellido) FROM usuario WHERE id_usuario = m.id_destinatario)
              END AS destinatario
              FROM mensajes m 
              JOIN usuario u ON m.id_remitente = u.id_usuario 
              WHERE m.id_mensaje = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_mensaje);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($message = $result->fetch_assoc()) {
        // Registrar la lectura del mensaje
        $insert_query = "INSERT INTO lecturas_mensajes (id_mensaje, id_usuario) VALUES (?, ?) ON DUPLICATE KEY UPDATE fecha_lectura = CURRENT_TIMESTAMP";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ii", $id_mensaje, $id_usuario);
        $insert_stmt->execute();
        $insert_stmt->close();

        echo json_encode($message);
    } else {
        echo json_encode(['error' => 'Mensaje no encontrado']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'ID de mensaje no proporcionado']);
}

$conn->close();