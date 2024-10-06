<?php
session_start();
include "../../scripts/conexion.php";

header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Verificar si se proporcionó un ID de mensaje
if (!isset($_POST['id_mensaje'])) {
    echo json_encode(['success' => false, 'message' => 'ID de mensaje no proporcionado']);
    exit;
}

$id_mensaje = intval($_POST['id_mensaje']);
$id_usuario = $_SESSION['id_usuario'];

// Primero, verificar si el usuario tiene permiso para eliminar este mensaje
$check_query = "SELECT id_remitente FROM mensajes WHERE id_mensaje = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $id_mensaje);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Mensaje no encontrado']);
    exit;
}

$message = $result->fetch_assoc();

// Verificar si el usuario actual es el remitente del mensaje
if ($message['id_remitente'] != $id_usuario) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar este mensaje']);
    exit;
}

// Eliminar el mensaje
$delete_query = "DELETE FROM mensajes WHERE id_mensaje = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("i", $id_mensaje);

if ($delete_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Mensaje eliminado con éxito']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el mensaje: ' . $conn->error]);
}

$delete_stmt->close();
$conn->close();