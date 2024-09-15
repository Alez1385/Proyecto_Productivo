<?php
session_start();
require_once('conexion.php');
require_once('config.php');

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: " . BASE_URL . "login/login.php");
    exit();
}

$username = $_SESSION['username'];

// Get user information
function getUserInfo($conn, $username) {
    $sql = "SELECT u.*, t.nombre AS tipo_nombre FROM usuario u 
            JOIN tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario 
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error preparing user query: ' . $conn->error);
    }
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        throw new Exception('Error executing user query: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('User not found.');
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Get modules for user type
function getModules($conn, $userTypeId) {
    $sql = "SELECT m.nom_modulo, m.icono, m.url 
            FROM modulos m 
            JOIN asig_modulo am ON m.id_modulo = am.id_modulo 
            WHERE am.id_tipo_usuario = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error preparing modules query: ' . $conn->error);
    }
    $stmt->bind_param("i", $userTypeId);
    if (!$stmt->execute()) {
        throw new Exception('Error executing modules query: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $modules = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $modules;
}

try {
    $user = getUserInfo($conn, $username);
    $modules = getModules($conn, $user['id_tipo_usuario']);
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>dashboard/css/style.css">
</head>
<body>
    <aside>
        <div class="sidebar">
            <div class="profile">
                <div class="info">
                    <p><b><?php echo htmlspecialchars($user['nombre']); ?></b></p>
                    <small class="text-muted"><?php echo htmlspecialchars($user['tipo_nombre']); ?></small>
                </div>
                <div class="profile-photo">
                    <img src="<?php echo BASE_URL . 'uploads/' . htmlspecialchars($user['foto']); ?>" alt="User Image">
                </div>
            </div>

            <?php if (!empty($modules)): ?>
                <?php foreach ($modules as $module): ?>
                    <a href="<?php echo BASE_URL . htmlspecialchars($module['url'], ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="material-icons-sharp"><?php echo htmlspecialchars($module['icono'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3><?php echo htmlspecialchars($module['nom_modulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay módulos disponibles para este tipo de usuario.</p>
            <?php endif; ?>

            <a href="<?php echo BASE_URL . 'login/logout.php'; ?>" class="logout">
                <span class="material-icons-sharp">logout</span>
                <h3>Salir</h3>
            </a>

            <div class="toggle">
                <a href="/dashboard/dashboard.php">
                    <div class="logo">
                        <img src="https://corsaje.gnosoft.com.co/general-ws/imgGeneral?imagen=2" alt="Logo">
                        <h2>Corsa<span style="color: #3498db;">Cor</span></h2>
                    </div>
                    <div class="close" id="close-btn">
                        <span class="material-icons-sharp">close</span>
                    </div>
                </a>
            </div>
        </div>
    </aside>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            var elements = document.querySelectorAll('.sidebar a');
            if (elements.length === 0) {
                throw new Error('No se encontraron elementos en el sidebar.');
            }

            elements.forEach(function(element) {
                if (!element.href) {
                    console.warn('Un enlace en el sidebar no tiene una URL.');
                }
            });
        } catch (error) {
            console.error('Error en el script del sidebar:', error.message);
            alert('Hubo un problema al cargar el contenido del sidebar. Por favor, intenta de nuevo más tarde.');
        }
    });
    </script>
</body>
</html>