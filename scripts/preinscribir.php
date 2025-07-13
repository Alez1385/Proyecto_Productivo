<?php
session_start();
require_once('functions.php');
include_once("conexion.php");

// Si es una petición GET, mostrar el formulario de preinscripción
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $curso_id = $_GET['curso_id'] ?? null;
    
    if (!$curso_id) {
        die('Error: No se especificó un curso válido.');
    }
    
    // Obtener información del curso
    $stmt = $conn->prepare("SELECT nombre_curso, descripcion FROM cursos WHERE id_curso = ?");
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $curso = $result->fetch_assoc();
    
    if (!$curso) {
        die('Error: Curso no encontrado.');
    }
    
    // Obtener información del usuario si está logueado
    $userData = [];
    if (isset($_SESSION['id_usuario'])) {
        $stmt = $conn->prepare("SELECT nombre, mail, telefono FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $_SESSION['id_usuario']);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Preinscripción - <?php echo htmlspecialchars($curso['nombre_curso']); ?></title>
        <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Poppins', sans-serif;
            }
            
            body {
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #e3f2fd 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                color: #333;
            }
            
            .container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                padding: 40px;
                max-width: 500px;
                width: 100%;
                position: relative;
                overflow: hidden;
                border: 1px solid #e3f2fd;
            }
            
            .container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #00bcff, #007bff);
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
            }
            
            .header h1 {
                color: #333;
                margin-bottom: 10px;
                font-size: 28px;
                font-weight: 600;
            }
            
            .header p {
                color: #666;
                font-size: 16px;
                line-height: 1.6;
            }
            
            .header .course-name {
                color: #00bcff;
                font-weight: 600;
            }
            
            .form-group {
                margin-bottom: 25px;
                position: relative;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: #333;
                font-weight: 500;
                font-size: 14px;
            }
            
            .form-group input {
                width: 100%;
                padding: 15px;
                border: 2px solid #e1e5e9;
                border-radius: 8px;
                font-size: 16px;
                transition: all 0.3s ease;
                background: #fff;
            }
            
            .form-group input:focus {
                outline: none;
                border-color: #00bcff;
                box-shadow: 0 0 0 3px rgba(0, 188, 255, 0.1);
            }
            
            .form-group input:valid {
                border-color: #28a745;
            }
            
            .submit-btn {
                width: 100%;
                padding: 15px;
                background: linear-gradient(135deg, #00bcff 0%, #007bff 100%);
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .submit-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(0, 188, 255, 0.3);
            }
            
            .submit-btn:active {
                transform: translateY(0);
            }
            
            .back-link {
                text-align: center;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 1px solid #e1e5e9;
            }
            
            .back-link a {
                color: #00bcff;
                text-decoration: none;
                font-weight: 500;
                font-size: 14px;
                transition: color 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            
            .back-link a:hover {
                color: #007bff;
            }
            
            .back-link a i {
                font-size: 12px;
            }
            
            .form-icon {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #999;
                font-size: 16px;
            }
            
            .form-group:focus-within .form-icon {
                color: #00bcff;
            }
            
            .success-message {
                background: #d4edda;
                color: #155724;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                border: 1px solid #c3e6cb;
                display: none;
            }
            
            .error-message {
                background: #f8d7da;
                color: #721c24;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                border: 1px solid #f5c6cb;
                display: none;
            }
            
            @media (max-width: 768px) {
                .container {
                    padding: 30px 20px;
                    margin: 10px;
                }
                
                .header h1 {
                    font-size: 24px;
                }
                
                .form-group input {
                    padding: 12px;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1><i class="fas fa-user-plus"></i> Preinscripción Rápida</h1>
                <p>Completa el formulario para preinscribirte en: <span class="course-name"><?php echo htmlspecialchars($curso['nombre_curso']); ?></span></p>
            </div>
            
            <div id="success-message" class="success-message">
                <i class="fas fa-check-circle"></i> Preinscripción enviada exitosamente
            </div>
            
            <div id="error-message" class="error-message">
                <i class="fas fa-exclamation-circle"></i> <span id="error-text"></span>
            </div>
            
            <form method="POST" action="" id="preinscripcion-form">
                <input type="hidden" name="curso_id" value="<?php echo htmlspecialchars($curso_id); ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($userData['nombre'] ?? ''); ?>" required>
                    <i class="fas fa-user form-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['mail'] ?? ''); ?>" required>
                    <i class="fas fa-envelope form-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($userData['telefono'] ?? ''); ?>" required>
                    <i class="fas fa-phone form-icon"></i>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Enviar Preinscripción
                </button>
            </form>
            
            <div class="back-link">
                <a href="/dashboard/dashboard.php">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
        
        <script>
            document.getElementById('preinscripcion-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = document.querySelector('.submit-btn');
                const originalText = submitBtn.innerHTML;
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                submitBtn.disabled = true;
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('success-message').style.display = 'block';
                        document.getElementById('preinscripcion-form').style.display = 'none';
                        
                        if (data.newUser) {
                            setTimeout(() => {
                                alert('Se ha creado una cuenta temporal. Revisa tu correo para obtener tu contraseña temporal.');
                            }, 1000);
                        }
                    } else {
                        document.getElementById('error-text').textContent = data.error;
                        document.getElementById('error-message').style.display = 'block';
                    }
                })
                .catch(error => {
                    document.getElementById('error-text').textContent = 'Error de conexión. Inténtalo de nuevo.';
                    document.getElementById('error-message').style.display = 'block';
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Código original para manejar POST requests
function isAuthenticated() {
    return isset($_SESSION['username']);
}

function createNewUser($conn, $nombre, $email, $telefono) {
    $password = bin2hex(random_bytes(8)); // Generate a secure random password
    $hashed_password = hashPassword($password);
    $id_tipo_usuario = 4; // User
    $username = explode('@', $email)[0]; // Use part of the email as username
    
    $sql = "INSERT INTO usuario (nombre, mail, telefono, id_tipo_usuario, username, clave, perfil_incompleto) 
            VALUES (?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiss", $nombre, $email, $telefono, $id_tipo_usuario, $username, $hashed_password);

    if ($stmt->execute()) {
        $id_usuario = $stmt->insert_id;
        error_log("New user created with ID: " . $id_usuario);
        return [
            'user_id' => $id_usuario,
            'password' => $password,
            'profile_incomplete' => true
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