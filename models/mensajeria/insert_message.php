<?php
// Incluir archivos de conexión
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

header('Content-Type: application/json');

// Verifica si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Obtener el id_usuario y el tipo de usuario actual
        $id_remitente = $_SESSION['id_usuario'];

        // Recoger datos del formulario
        $tipo_destinatario = $_POST['tipo_destinatario'];
        $destinatario = isset($_POST['destinatario']) ? $_POST['destinatario'] : null;
        $asunto = $_POST['asunto'];
        $contenido = $_POST['contenido'];

        // Validar campos obligatorios
        if (empty($asunto) || empty($contenido)) {
            throw new Exception('El asunto y el contenido son obligatorios.');
        }

        // Determinar el id_destinatario y el id_tipo_usuario
        $id_destinatario = null;
        $id_tipo_usuario = null;

        switch ($tipo_destinatario) {
            case 'individual':
                if (empty($_POST['destinatario_email'])) {
                    throw new Exception('El correo del destinatario es obligatorio para mensajes individuales.');
                }
                $id_destinatario = obtenerIdUsuarioPorEmail($_POST['destinatario_email']);
                if ($id_destinatario === null) {
                    throw new Exception('Usuario destinatario no encontrado');
                }
                break;
            case 'estudiantes':
                $id_tipo_usuario = obtenerIdTipoUsuario('estudiante');
                break;
            case 'profesores':
                $id_tipo_usuario = obtenerIdTipoUsuario('profesor');
                break;
            case 'todos':
                // Para 'todos', dejamos id_destinatario y id_tipo_usuario como null
                break;
            default:
                throw new Exception('Tipo de destinatario no válido.');
        }

        // Modificar la consulta SQL para incluir id_tipo_usuario
        $query = "INSERT INTO mensajes (id_remitente, tipo_destinatario, id_tipo_usuario, id_destinatario, asunto, contenido) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception('Error en la preparación de la consulta: ' . $conn->error);
        }

        // Vincular parámetros
        $stmt->bind_param("isiiss", $id_remitente, $tipo_destinatario, $id_tipo_usuario, $id_destinatario, $asunto, $contenido);

        if (!$stmt->execute()) {
            throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);

        error_log("Inserting message: " . json_encode([
            'id_remitente' => $id_remitente,
            'tipo_destinatario' => $tipo_destinatario,
            'id_tipo_usuario' => $id_tipo_usuario,
            'id_destinatario' => $id_destinatario,
            'asunto' => $asunto,
            'contenido' => $contenido
        ]));
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

function obtenerIdTipoUsuario($tipo) {
    global $conn;
    $query = "SELECT id_tipo_usuario FROM tipo_usuario WHERE nombre = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta para obtener id_tipo_usuario: ' . $conn->error);
    }
    $stmt->bind_param("s", $tipo);
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta para obtener id_tipo_usuario: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    if (!$row) {
        throw new Exception('No se encontró el tipo de usuario: ' . $tipo);
    }
    return $row['id_tipo_usuario'];
}

function obtenerIdUsuarioPorEmail($email) {
    global $conn;
    $query = "SELECT id_usuario FROM usuario WHERE mail = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta para obtener id_usuario: ' . $conn->error);
    }
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta para obtener id_usuario: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    if (!$row) {
        throw new Exception('No se encontró el usuario con el email: ' . $email);
    }
    return $row['id_usuario'];
}
