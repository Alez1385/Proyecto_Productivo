<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_remitente = $_SESSION['id_usuario'];
    $tipo_destinatario = $_POST['tipo_destinatario'];
    $asunto = $_POST['asunto'];
    $contenido = $_POST['contenido'];

    // Validar que los campos requeridos no estén vacíos
    if (empty($asunto) || empty($contenido)) {
        echo json_encode(['success' => false, 'message' => 'Asunto y contenido son obligatorios.']);
        exit;
    }

    // Determinar el id_tipo_usuario basado en el tipo_destinatario
    $id_tipo_usuario = null;
    $id_destinatario = null;

    switch ($tipo_destinatario) {
        case 'individual':
            // Verifica que el campo 'destinatario_email' esté presente
            if (empty($_POST['destinatario_email'])) {
                echo json_encode(['success' => false, 'message' => 'El correo del destinatario es obligatorio para mensajes individuales.']);
                exit;
            }
            $id_destinatario = obtenerIdUsuarioPorEmail($_POST['destinatario_email']);
            if ($id_destinatario === null) {
                echo json_encode(['success' => false, 'message' => 'Usuario destinatario no encontrado']);
                exit;
            }
            break;
        case 'estudiantes':
            $id_tipo_usuario = obtenerIdTipoUsuario('estudiante');
            break;
        case 'profesores':
            $id_tipo_usuario = obtenerIdTipoUsuario('profesor');
            break;
        case 'todos':
            $id_tipo_usuario = null;
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Tipo de destinatario no válido.']);
            exit;
    }

    // Preparar la consulta
    $query = "INSERT INTO mensajes (id_remitente, tipo_destinatario, id_tipo_usuario, id_destinatario, asunto, contenido) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta: ' . $conn->error]);
        exit;
    }

    // Asegúrate de que id_destinatario sea NULL cuando no es un mensaje individual
    if ($tipo_destinatario !== 'individual') {
        $id_destinatario = null;
    } else {
        // Verifica que esta función exista y funcione correctamente
        $destinatario_email = $_POST['destinatario'];
        $id_destinatario = obtenerIdUsuarioPorEmail($destinatario_email);
        if ($id_destinatario === null) {
            echo json_encode(['success' => false, 'message' => 'Usuario destinatario no encontrado']);
            exit;
        }
    }

    $stmt->bind_param("isiiss", $id_remitente, $tipo_destinatario, $id_tipo_usuario, $id_destinatario, $asunto, $contenido);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado con éxito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']);
}

function obtenerIdTipoUsuario($tipo) {
    global $conn;
    $query = "SELECT id_tipo_usuario FROM tipo_usuario WHERE nombre = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['id_tipo_usuario'] : null;
}

function obtenerIdUsuarioPorEmail($email) {
    global $conn;
    $query = "SELECT id_usuario FROM usuario WHERE mail = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['id_usuario'] : null;
}

