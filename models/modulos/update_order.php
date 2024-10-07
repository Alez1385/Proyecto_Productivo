<?php
include "../../scripts/conexion.php";

// Obtener los datos del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    // Iniciar una transacción
    $conn->begin_transaction();
    try {
        foreach ($data as $module) {
            $id_modulo = $module['id_modulo'];
            $old_orden = $module['old_orden'];
            $new_orden = $module['new_orden'];

            // Verificar que los campos no estén vacíos
            if (empty($id_modulo) || !isset($old_orden) || !isset($new_orden)) {
                throw new Exception("Datos incompletos: id_modulo o orden vacío.");
            }

            // Actualizar el orden en la tabla modulos
            $sql = "UPDATE modulos SET orden = ? WHERE id_modulo = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
            }

            $stmt->bind_param("ii", $new_orden, $id_modulo);
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta SQL: " . $stmt->error);
            }

            $stmt->close();
        }

        // Confirmar la transacción
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos']);
}

$conn->close();
?>