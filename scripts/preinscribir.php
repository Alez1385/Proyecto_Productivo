<?php
// scripts/preinscribir.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("conexion.php");
session_start();

function isAuthenticated() {
    return isset($_SESSION['username']);
}

function createUser($conn, $nombre, $email, $telefono) {
    $password = 'Corsaje2024'; // Contraseña por defecto
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $id_tipo_usuario = 4; // User
    $username = explode('@', $email)[0]; // Usar parte del email como username
    
    $sql = "INSERT INTO usuario (nombre, mail, telefono, id_tipo_usuario, username, clave)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiss", $nombre, $email, $telefono, $id_tipo_usuario, $username, $hashed_password);
   
    if ($stmt->execute()) {
        error_log("Nuevo usuario creado con ID: " . $stmt->insert_id);
        return [
            'user_id' => $stmt->insert_id,
            'password' => $password
        ];
    }
    error_log("Error al crear usuario: " . $stmt->error);
    return false;
}

// Recibir y validar datos del formulario
$id_curso = $_POST['curso_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$email = $_POST['email'] ?? null;
$telefono = $_POST['telefono'] ?? null;

if (empty($id_curso) || empty($nombre) || empty($email) || empty($telefono)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Todos los campos son obligatorios"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Dirección de correo electrónico no válida"]);
    exit;
}

try {
    if (!isAuthenticated()) {
        // Verificar si el email ya existe
        $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE mail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
       
        if ($result->num_rows > 0) {
            header('HTTP/1.1 409 Conflict');
            echo json_encode(["error" => "Ya existe un usuario con este email. Por favor, inicia sesión."]);
            exit;
        }
       
        // Crear nuevo usuario
        $newUser = createUser($conn, $nombre, $email, $telefono);
        if (!$newUser) {
            throw new Exception("Error al crear el usuario");
        }
       
        $user_id = $newUser['user_id'];
        $temp_password = $newUser['password'];
    }

    // Generar un token único
    $token = bin2hex(random_bytes(16));

    // Insertar datos en la tabla de preinscripciones
    $sql = "INSERT INTO preinscripciones (id_curso, nombre, email, telefono, fecha_preinscripcion, estado, token)
            VALUES (?, ?, ?, ?, NOW(), 'pendiente', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $id_curso, $nombre, $email, $telefono, $token);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al realizar la preinscripción: " . $stmt->error);
    }

    // Preparar respuesta
    $response = "Preinscripción exitosa";

    if (!isAuthenticated()) {
        $response["newUser"] = [
            "email" => $email,
            "tempPassword" => $temp_password
        ];
    }
    // Usar JSON_UNESCAPED_UNICODE para que se muestren correctamente los caracteres especiales
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    

} catch (Exception $e) {
    error_log("Error en preinscripción: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>