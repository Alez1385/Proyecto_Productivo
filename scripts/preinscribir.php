<?php
include_once("conexion.php");
session_start();

// Función para verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Función para crear un nuevo usuario
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
        return [
            'user_id' => $stmt->insert_id,
            'password' => $password
        ];
    }
    return false;
}

// Recibir datos del formulario
$id_curso = $_POST['curso_id'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];

// Validar datos
if (empty($id_curso) || empty($nombre) || empty($email) || empty($telefono)) {
    die(json_encode(["error" => "Todos los campos son obligatorios"]));
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(["error" => "Dirección de correo electrónico no válida"]));
}

// Verificar si el usuario está autenticado
if (!isAuthenticated()) {
    // Verificar si el email ya existe en la base de datos
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
   
    if ($result->num_rows > 0) {
        die(json_encode(["error" => "Ya existe un usuario con este email. Por favor, inicia sesión."]));
    }
   
    // Crear nuevo usuario
    $newUser = createUser($conn, $nombre, $email, $telefono);
    if (!$newUser) {
        die(json_encode(["error" => "Error al crear el usuario"]));
    }
   
    $user_id = $newUser['user_id'];
    $temp_password = $newUser['password'];
} else {
    $user_id = $_SESSION['user_id'];
}

// Generar un token único
$token = bin2hex(random_bytes(16));

// Insertar datos en la tabla de preinscripciones
$sql = "INSERT INTO preinscripciones (id_curso, nombre, email, telefono, fecha_preinscripcion, estado, token)
        VALUES (?, ?, ?, ?, NOW(), 'pendiente', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $id_curso, $nombre, $email, $telefono, $token);
if ($stmt->execute()) {
    // Enviar email de confirmación
    $to = $email;
    $subject = "Confirmación de Preinscripción";
    $message = "Hola $nombre,\n\nGracias por tu preinscripción al curso. Tu token de preinscripción es: $token\n\n";
   
    if (!isAuthenticated()) {
        $message .= "Se ha creado una cuenta para ti con los siguientes datos:\n";
        $message .= "Email: $email\n";
        $message .= "Contraseña temporal: $temp_password\n";
        $message .= "Por favor, cambia tu contraseña después de iniciar sesión por primera vez.\n\n";
    }
   
    $message .= "Pronto nos pondremos en contacto contigo para completar el proceso de inscripción.\n\nSaludos,\nEquipo de Cursos";
    $headers = "From: noreply@tucurso.com";
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(["success" => "Preinscripción realizada con éxito. Se ha enviado un correo de confirmación."]);
    } else {
        echo json_encode(["warning" => "Preinscripción realizada, pero hubo un problema al enviar el correo de confirmación."]);
    }
} else {
    echo json_encode(["error" => "Error al realizar la preinscripción: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>