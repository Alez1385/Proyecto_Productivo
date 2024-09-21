<?php
// scripts/obtener_datos_usuario.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'conexion.php';

function getUserDataFromDatabase($userId, $conn) {
    $query = "SELECT nombre, apellido, mail, telefono FROM usuario WHERE id_usuario = ? AND estado = 'activo'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    try {
        // Asumiendo que $conn es una conexión MySQLi válida
        $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $id_usuario = $row['id_usuario'];
            $userData = getUserDataFromDatabase($id_usuario, $conn);
            
            if ($userData) {
                $responseData = [
                    'nombre' => $userData['nombre'] . ' ' . $userData['apellido'],
                    'email' => $userData['mail'],
                    'telefono' => $userData['telefono']
                ];
                header('Content-Type: application/json');
                echo json_encode($responseData);
            } else {
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Usuario no encontrado']);
            }
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
    }
} else {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Usuario no autenticado']);
}