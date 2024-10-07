<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $file = $_FILES['avatar'];

    // Verificar si se subió correctamente
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        $fileName = uniqid() . '_' . basename($file['name']);
        $uploadFile = $uploadDir . $fileName;

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            // Actualizar la base de datos con la nueva ruta del avatar
            $query = "UPDATE usuario SET foto = ? WHERE id_usuario = ?";
            $stmt = $conn->prepare($query);
            $relativePath = $fileName; // Solo guardamos el nombre del archivo
            $stmt->bind_param("si", $relativePath, $id_usuario);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Avatar actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el avatar en la base de datos']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo subido']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido o no se recibió ningún archivo']);
}