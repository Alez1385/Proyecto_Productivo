<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_remitente = $_SESSION['id_usuario'];
    $tipo_destinatario = $_POST['tipo_destinatario'];
    $id_destinatario = ($tipo_destinatario == 'individual') ? $_POST['destinatario'] : null; // Puede ser null

    // Validar que los campos requeridos no estén vacíos
    if (empty($asunto) || empty($contenido)) {
        echo json_encode(['success' => false, 'message' => 'Asunto y contenido son obligatorios.']);
        exit;
    }

    $asunto = $_POST['asunto'];
    $contenido = $_POST['contenido'];
 
  

    // Preparar la consulta
    $query = "INSERT INTO mensajes (id_remitente, tipo_destinatario, id_destinatario, asunto, contenido) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Cambiar el tipo de binding para permitir null en id_destinatario
    $stmt->bind_param("ssiss", $id_remitente, $tipo_destinatario, $id_destinatario, $asunto, $contenido);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado con éxito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']);
}
