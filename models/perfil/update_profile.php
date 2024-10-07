<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $mail = $_POST['mail']; // Cambiado de 'email' a 'mail'
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $fecha_nac = $_POST['fecha_nac']; // Cambiado de 'fecha_nacimiento' a 'fecha_nac'

    $query = "UPDATE usuario SET nombre = ?, apellido = ?, mail = ?, telefono = ?, direccion = ?, fecha_nac = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $nombre, $apellido, $mail, $telefono, $direccion, $fecha_nac, $id_usuario);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Perfil actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el perfil: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}