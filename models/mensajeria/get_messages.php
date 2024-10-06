<?php
include "../../scripts/conexion.php";
include "../../scripts/sesion.php";

$id_usuario = $_SESSION['id_usuario'];  // ID del usuario actual
$tipo_usuario = $_SESSION['tipo_usuario'];  // Tipo de usuario (profesor, estudiante, etc.)

// Consulta para obtener los mensajes
$query = "SELECT m.id_mensaje, u.nombre AS remitente, m.asunto, m.fecha_envio, m.tipo_destinatario 
          FROM mensajes m 
          JOIN usuario u ON m.id_remitente = u.id_usuario 
          WHERE m.id_remitente = ? 
             OR m.tipo_destinatario = 'todos' 
             OR (m.tipo_destinatario = 'estudiantes' AND ? = 'estudiante') 
             OR (m.tipo_destinatario = 'profesores' AND ? = 'profesor') 
             OR (m.tipo_destinatario = 'users' AND ? = 'user')
             OR (m.tipo_destinatario = 'individual' AND m.id_destinatario = ?)
          ORDER BY m.fecha_envio DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("isssi", $id_usuario, $tipo_usuario, $tipo_usuario, $tipo_usuario, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Devolver los mensajes como JSON
echo json_encode($messages);

$stmt->close();
$conn->close();
?>
