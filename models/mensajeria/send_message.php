<?php
include "../../scripts/conexion.php";
include "../../scripts/sesion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_remitente = $_SESSION['id_usuario'];
    $tipo_destinatario = $_POST['tipo_destinatario'];
    $id_destinatario = ($tipo_destinatario == 'individual') ? $_POST['destinatario'] : null;
    $asunto = $_POST['asunto'];
    $contenido = $_POST['contenido'];

    $query = "INSERT INTO mensajes (id_remitente, tipo_destinatario, id_destinatario, asunto, contenido) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isiss", $id_remitente, $tipo_destinatario, $id_destinatario, $asunto, $contenido);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}