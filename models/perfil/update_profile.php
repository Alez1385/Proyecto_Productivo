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

    // Verificar si todos los campos requeridos están completos
    $perfil_completo = !empty($nombre) && !empty($apellido) && !empty($telefono) && 
                       !empty($direccion) && !empty($fecha_nac);

    $query = "UPDATE usuario SET nombre = ?, apellido = ?, mail = ?, telefono = ?, direccion = ?, fecha_nac = ?, perfil_incompleto = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $perfil_incompleto = $perfil_completo ? 0 : 1; // 0 si está completo, 1 si está incompleto
    $stmt->bind_param("ssssssii", $nombre, $apellido, $mail, $telefono, $direccion, $fecha_nac, $perfil_incompleto, $id_usuario);

    if ($stmt->execute()) {
        $message = $perfil_completo ? 
            'Perfil actualizado correctamente. ¡Tu perfil está completo!' : 
            'Perfil actualizado correctamente';
        
        echo json_encode([
            'success' => true, 
            'message' => $message,
            'profile_complete' => $perfil_completo
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el perfil: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}