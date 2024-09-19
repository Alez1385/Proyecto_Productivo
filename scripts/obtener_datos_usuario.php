<?php
// scripts/obtener_datos_usuario.php

// Iniciar sesión
session_start();
include 'conexion.php';

// Función para obtener los datos del usuario de la base de datos
function getUserDataFromDatabase($userId, $pdo) {
    $query = "SELECT nombre, apellido, mail, telefono FROM usuario WHERE id_usuario = :userId AND estado = 'activo'";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['userId' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verificar si el usuario está autenticado
if (isset($_SESSION['id_usuario'])) {
    try {
        // Conectar a la base de datos
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Obtener los datos del usuario
        $userId = $_SESSION['id_usuario'];
        $userData = getUserDataFromDatabase($userId, $pdo);

        if ($userData) {
            // Preparar los datos para enviar
            $responseData = [
                'nombre' => $userData['nombre'] . ' ' . $userData['apellido'],
                'email' => $userData['mail'],
                'telefono' => $userData['telefono']
            ];

            // Devolver los datos como JSON
            header('Content-Type: application/json');
            echo json_encode($responseData);
        } else {
            // Si no se encontraron datos del usuario
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
    } catch (PDOException $e) {
        // Error en la base de datos
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
    }
} else {
    // Si el usuario no está autenticado
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Usuario no autenticado']);
}
