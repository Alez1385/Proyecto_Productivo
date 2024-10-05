<?php
include "../../scripts/conexion.php";

// Obtener los datos del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        foreach ($data as $module) {
            $old_id_modulo = $module['old_id_modulo']; // ID anterior del módulo
            $new_id_modulo = $module['new_id_modulo']; // Nuevo ID del módulo
            $orden = $module['orden'];

            // Verificar que los campos no estén vacíos
            if (empty($old_id_modulo) || empty($new_id_modulo) || empty($orden)) {
                throw new Exception("Datos incompletos: id_modulo o orden vacío.");
            }

            // Verificar que el nuevo id_modulo no exista ya en la base de datos
            $check_sql = "SELECT id_modulo FROM modulos WHERE id_modulo = ?";
            $stmt_check = $conn->prepare($check_sql);
            $stmt_check->bind_param("i", $new_id_modulo);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0 && $new_id_modulo != $old_id_modulo) {
                throw new Exception("El nuevo ID del módulo ya existe.");
            }

            // Actualizar el campo 'id_modulo' en la tabla modulos
            $sql = "UPDATE modulos SET id_modulo = ?, orden = ? WHERE id_modulo = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
            }

            // Actualizar el nuevo id_modulo, su orden y reemplazar el viejo id_modulo
            $stmt->bind_param("iii", $new_id_modulo, $orden, $old_id_modulo);

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta SQL: " . $stmt->error);
            }

            // Aquí puedes añadir más consultas para actualizar el id_modulo en otras tablas relacionadas
            // Actualizar 'id_modulo' en otras tablas relacionadas, por ejemplo, 'modulo_relacionado':
            $sql_update_related = "UPDATE modulo_relacionado SET id_modulo = ? WHERE id_modulo = ?";
            $stmt_related = $conn->prepare($sql_update_related);
            $stmt_related->bind_param("ii", $new_id_modulo, $old_id_modulo);
            $stmt_related->execute();
        }

        // Confirmar la transacción
        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $stmt->close();
}

$conn->close();
?>
