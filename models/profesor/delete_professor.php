<?php
include '../../scripts/conexion.php';

// Obtener el ID del profesor desde la solicitud POST
$id_profesor = isset($_POST['id_profesor']) ? intval($_POST['id_profesor']) : 0;

if ($id_profesor > 0) {
    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Primero, eliminar las asignaciones de cursos del profesor
        $stmt_delete_assignments = $conn->prepare("DELETE FROM asignacion_curso WHERE id_profesor = ?");
        $stmt_delete_assignments->bind_param("i", $id_profesor);
        $stmt_delete_assignments->execute();
        $stmt_delete_assignments->close();

        // Luego, obtener el id_usuario del profesor
        $stmt_get_user_id = $conn->prepare("SELECT id_usuario FROM profesor WHERE id_profesor = ?");
        $stmt_get_user_id->bind_param("i", $id_profesor);
        $stmt_get_user_id->execute();
        $result = $stmt_get_user_id->get_result();
        $user_row = $result->fetch_assoc();
        $id_usuario = $user_row['id_usuario'];
        $stmt_get_user_id->close();

        // Eliminar al profesor
        $stmt_delete_professor = $conn->prepare("DELETE FROM profesor WHERE id_profesor = ?");
        $stmt_delete_professor->bind_param("i", $id_profesor);
        $stmt_delete_professor->execute();
        $stmt_delete_professor->close();

        // Actualizar el tipo de usuario a "usuario normal" (asumiendo que el id_tipo_usuario para usuario normal es 1)
        $stmt_update_user = $conn->prepare("UPDATE usuario SET id_tipo_usuario = 1 WHERE id_usuario = ?");
        $stmt_update_user->bind_param("i", $id_usuario);
        $stmt_update_user->execute();
        $stmt_update_user->close();

        // Confirmar la transacción
        $conn->commit();
        $response = ['success' => true];
    } catch (Exception $e) {
        // Si hay un error, revertir los cambios
        $conn->rollback();
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid professor ID'];
}

$conn->close();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
