<?php
include "../../scripts/conexion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se recibió el id_modulo
    if (!isset($_POST['id_modulo']) || empty($_POST['id_modulo'])) {
        echo json_encode(['success' => false, 'message' => 'Error: No se proporcionó el ID del módulo.']);
        exit;
    }

    $id_modulo = intval($_POST['id_modulo']);

    // Preparar la declaración SQL para eliminar el módulo
    $sql = "DELETE FROM modulos WHERE id_modulo = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error: No se pudo preparar la declaración SQL: ' . $conn->error]);
        exit;
    }

    // Vincular parámetros y ejecutar
    if (!$stmt->bind_param("i", $id_modulo)) {
        echo json_encode(['success' => false, 'message' => 'Error: Falló la vinculación de parámetros: ' . $stmt->error]);
        $stmt->close();
        exit;
    }

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error al ejecutar la declaración SQL: ' . $stmt->error]);
        $stmt->close();
        exit;
    }

    // Verificar si se eliminó algún registro
    if ($stmt->affected_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Advertencia: No se encontró un módulo con el ID proporcionado.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Módulo eliminado exitosamente.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error: Método de solicitud no permitido.']);
}

$conn->close();
