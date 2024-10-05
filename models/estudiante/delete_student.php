<?php
include '../../scripts/conexion.php';

// Obtener el ID del estudiante desde la solicitud POST
$id_estudiante = isset($_POST['id_estudiante']) ? intval($_POST['id_estudiante']) : 0;

if ($id_estudiante > 0) {
    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Primero, obtener el id_usuario asociado al estudiante
        $stmt = $conn->prepare("SELECT id_usuario FROM estudiante WHERE id_estudiante = ?");
        $stmt->bind_param("i", $id_estudiante);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $id_usuario = $row['id_usuario'];
            
            // Eliminar el registro de la tabla estudiante
            $stmt = $conn->prepare("DELETE FROM estudiante WHERE id_estudiante = ?");
            $stmt->bind_param("i", $id_estudiante);
            $stmt->execute();
            
            // Eliminar el registro de la tabla usuario
            $stmt = $conn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            
            // Confirmar la transacción
            $conn->commit();
            
            $response = ['success' => true];
        } else {
            throw new Exception("Estudiante no encontrado");
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'message' => 'ID de estudiante inválido'];
}

$conn->close();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);