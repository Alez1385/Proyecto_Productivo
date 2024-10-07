<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../../../scripts/conexion.php";
    
    $count_success = 0;
    $id_tipo_usuario = $_POST['userType'];
    $error_messages = [];

    // Obtener todos los usuarios del tipo de usuario seleccionado
    $sql_get_users = "SELECT id_usuario FROM usuario WHERE id_tipo_usuario = ?";
    if ($stmt_get_users = $conn->prepare($sql_get_users)) {
        $stmt_get_users->bind_param("i", $id_tipo_usuario);
        $stmt_get_users->execute();
        $result_users = $stmt_get_users->get_result();
        $user_ids = $result_users->fetch_all(MYSQLI_ASSOC);
        $stmt_get_users->close();
    } else {
        $error_messages[] = "Error al preparar la consulta para obtener usuarios.";
    }

    if (!empty($user_ids)) {
        // Comenzar transacción
        $conn->begin_transaction();

        try {
            // Eliminar asignaciones existentes para este tipo de usuario
            $sql_delete = "DELETE FROM asig_modulo WHERE id_tipo_usuario = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id_tipo_usuario);
            $stmt_delete->execute();
            $stmt_delete->close();

            // Insertar nuevas asignaciones
            $sql_insert = "INSERT INTO asig_modulo (id_modulo, id_tipo_usuario) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);

            foreach ($_POST['id_modulo'] as $id_modulo) {
                $stmt_insert->bind_param("ii", $id_modulo, $id_tipo_usuario);
                if ($stmt_insert->execute()) {
                    $count_success++;
                } else {
                    throw new Exception("Error al insertar la asignación del módulo ID $id_modulo.");
                }
            }

            $stmt_insert->close();

            // Confirmar transacción
            $conn->commit();
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conn->rollback();
            $error_messages[] = $e->getMessage();
        }
    } else {
        $error_messages[] = "No se encontraron usuarios para el tipo de usuario seleccionado.";
    }

    $conn->close();

    // Preparar respuesta JSON
    $response = [
        'success' => empty($error_messages),
        'message' => empty($error_messages) 
            ? "Se han asignado $count_success módulos correctamente." 
            : implode("\n", $error_messages)
    ];

    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>