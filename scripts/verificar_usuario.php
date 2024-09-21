<?php
session_start();
require_once 'conexion.php';

// Verificar si la solicitud es AJAX
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    exit(json_encode(['error' => 'No se permite el acceso directo a este script.']));
}

// Verificar si se proporcionó un curso_id
if(!isset($_POST['curso_id']) || !is_numeric($_POST['curso_id'])) {
    exit(json_encode(['error' => 'No se proporcionó un ID de curso válido.']));
}

$curso_id = intval($_POST['curso_id']);
$response = ['status' => 'no_logueado', 'ya_inscrito' => false];

// Verificar si el usuario está logueado
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    try {
        // Obtener el id_usuario correspondiente al username
        $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id_usuario'];
            
            // Obtener el id_estudiante correspondiente al id_usuario
            $stmt = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
            $result = $stmt->get_result();
            
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_estudiante = $row['id_estudiante'];
                
                // Verificar si ya está inscrito en este curso
                $stmt = $conn->prepare("SELECT id_inscripcion FROM inscripciones WHERE id_estudiante = ? AND id_curso = ?");
                if (!$stmt) {
                    throw new Exception("Error en la preparación de la consulta: " . $conn->error);
                }
                $stmt->bind_param("ii", $id_estudiante, $curso_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
                }
                $result = $stmt->get_result();
                
                if($result->num_rows > 0) {
                    $response = ['status' => 'logueado', 'ya_inscrito' => true];
                } else {
                    $response = ['status' => 'logueado', 'ya_inscrito' => false];
                }
            } else {
                $response = ['error' => 'No se encontró el estudiante asociado al usuario.'];
            }
        } else {
            $response = ['error' => 'No se encontró el usuario en la base de datos.'];
        }
    } catch (Exception $e) {
        $response = ['error' => 'Error en el servidor: ' . $e->getMessage()];
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}

echo json_encode($response);
$conn->close();
?>