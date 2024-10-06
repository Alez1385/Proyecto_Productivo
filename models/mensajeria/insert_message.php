<?php
include "../../scripts/conexion.php";
include "../../scripts/sesion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_remitente = $_SESSION['id_usuario']; // Obtenemos el ID del remitente desde la sesión
    $tipo_destinatario = $_POST['tipo_destinatario'];
    $id_destinatario = ($tipo_destinatario == 'individual') ? $_POST['destinatario'] : null;
    $asunto = $_POST['asunto'];
    $contenido = $_POST['contenido'];

    // Validar que el asunto y el contenido no estén vacíos
    if (empty($asunto) || empty($contenido)) {
        echo json_encode(['success' => false, 'message' => 'El asunto y el contenido son obligatorios.']);
        exit;
    }

    // Query para insertar mensaje en la base de datos
    $query = "INSERT INTO mensajes (id_remitente, tipo_destinatario, id_destinatario, asunto, contenido) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isiss", $id_remitente, $tipo_destinatario, $id_destinatario, $asunto, $contenido);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
