<?php
// Incluir archivos de conexión
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

// Verifica si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $tipo_destinatario = $_POST['tipo_destinatario'];
    $destinatario = isset($_POST['destinatario']) ? $_POST['destinatario'] : null;
    $asunto = $_POST['asunto'];
    $contenido = $_POST['contenido'];

    // Validar campos obligatorios
    if (empty($asunto) || empty($contenido)) {
        echo json_encode(['success' => false, 'message' => 'El asunto y el contenido son obligatorios.']);
        exit;
    }

    // Obtener el id_usuario y el tipo de usuario actual
    $id_usuario_actual = $_SESSION['id_usuario'];

    // Determinar el id_remitente (siempre será el usuario actual)
    $id_remitente = $id_usuario_actual;

    // Preparar la consulta
    $sql = "INSERT INTO mensajes (id_remitente, tipo_destinatario, id_destinatario, asunto, contenido, fecha_envio) 
            VALUES (?, ?, ?, ?, ?, NOW())";

    if ($stmt = $conn->prepare($sql)) {
        // Manejar el tipo de destinatario
        switch ($tipo_destinatario) {
            case 'todos':
                $id_destinatario = null;
                break;
            case 'profesores':
                $id_destinatario = 2; // ID para profesores
                break;
            case 'estudiantes':
                $id_destinatario = 3; // ID para estudiantes
                break;
                case 'individual':
                    // Verificar si el destinatario existe en la tabla usuario
                    $check_sql = "SELECT id_usuario FROM usuario WHERE mail = ?";
                    $check_stmt = $conn->prepare($check_sql);
                    $check_stmt->bind_param("s", $destinatario);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    if ($check_result->num_rows === 0) {
                        echo json_encode(['success' => false, 'message' => 'El correo electrónico especificado no existe.']);
                        exit;
                    } else {
                        $id_destinatario = $check_result->fetch_assoc()['id_usuario'];
                    }
                    $check_stmt->close();
                    break;
            default:
                echo json_encode(['success' => false, 'message' => 'Tipo de destinatario no válido.']);
                exit;
        }

        // Vincular parámetros
        $stmt->bind_param("isiss", $id_remitente, $tipo_destinatario, $id_destinatario, $asunto, $contenido);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta: ' . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>