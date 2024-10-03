<?php
session_start();
require_once('functions.php');
include_once("conexion.php");

function isAuthenticated() {
    return isset($_SESSION['username']);
}

function createNewUser($conn, $nombre, $email, $telefono) {
    $password = bin2hex(random_bytes(8)); // Generate a secure random password
    $hashed_password = hashPassword($password);
    $id_tipo_usuario = 4; // User
    $username = explode('@', $email)[0]; // Use part of the email as username
    
    $sql = "INSERT INTO usuario (nombre, mail, telefono, id_tipo_usuario, username, clave)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiss", $nombre, $email, $telefono, $id_tipo_usuario, $username, $hashed_password);

    if ($stmt->execute()) {
        error_log("New user created with ID: " . $stmt->insert_id);
        return [
            'user_id' => $stmt->insert_id,
            'password' => $password
        ];
    }
    error_log("Error creating user: " . $stmt->error);
    return false;
}

function getUserId($conn, $email) {
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['id_usuario'];
    }
    return null;
}

function isAlreadyPreinscribed($conn, $user_id, $id_curso) {
    $stmt = $conn->prepare("SELECT id_preinscripcion FROM preinscripciones WHERE id_usuario = ? AND id_curso = ?");
    $stmt->bind_param("ii", $user_id, $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Receive and validate form data
$id_curso = $_POST['curso_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$email = $_POST['email'] ?? null;
$telefono = $_POST['telefono'] ?? null;

if (empty($id_curso) || empty($nombre) || empty($email) || empty($telefono)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "All fields are required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Invalid email address"]);
    exit;
}

try {
    $user_id = null;
    $is_new_user = false;

    if (!isAuthenticated()) {
        $user_id = getUserId($conn, $email);
        if ($user_id === null) {
            // Create new user
            $newUser = createNewUser($conn, $nombre, $email, $telefono);
            if (!$newUser) {
                throw new Exception("Error creating user");
            }
            $user_id = $newUser['user_id'];
            $temp_password = $newUser['password'];
            $is_new_user = true;
        }
    } else {
        $user_id = $_SESSION['id_usuario'];
    }

    // Check if the user is already preinscribed in this course
    if (isAlreadyPreinscribed($conn, $user_id, $id_curso)) {
        echo json_encode(["error" => "You are already preinscribed in this course"]);
        exit;
    }

    // Generate a unique token
    $token = bin2hex(random_bytes(16));

    // Insert data into the preinscripciones table
    $sql = "INSERT INTO preinscripciones (id_curso, nombre, email, telefono, fecha_preinscripcion, estado, token, id_usuario)
            VALUES (?, ?, ?, ?, NOW(), 'pendiente', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $id_curso, $nombre, $email, $telefono, $token, $user_id);
    
    if (!$stmt->execute()) {    
        throw new Exception("Error performing preinscription: " . $stmt->error);
    }

    // Fetch course name
    $stmt = $conn->prepare("SELECT nombre_curso FROM cursos WHERE id_curso = ?");
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();
    $courseName = $result->fetch_assoc()['nombre_curso'];

    // Prepare additional data for email
    $additionalData = [
        'courseName' => $courseName
    ];

    if ($is_new_user) {
        $additionalData['tempPassword'] = $temp_password;
    }

    // Send email
    sendEmail($email, 'preinscription', null, $additionalData);

    // Success message
    echo json_encode([
        "success" => "Preinscription successful. An email has been sent to you.",
        "newUser" => $is_new_user ? ["email" => $email, "tempPassword" => $temp_password] : null
    ]);

} catch (Exception $e) {
    
        error_log("Error in preinscribir.php: " . $e->getMessage());
        // Retorna el mensaje de error específico
        echo json_encode(["error" => "Interal server error"]);
    
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}