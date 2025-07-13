<?php
// auth.php
error_log("Starting auth.php");

// Verificar si la sesión ya está activa antes de llamarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('conexion.php');
require_once('config.php');
require_once('functions.php');


/**
 * Verificar si el usuario ha iniciado sesión
 *
 * @return bool true si el usuario ha iniciado sesión, false en caso contrario
 */
function authenticateUser() {
    error_log("Attempting to authenticate user");
    error_log("Session data: " . print_r($_SESSION, true));

    if (isset($_SESSION['id_usuario'])) {
        error_log("User authenticated via session: {$_SESSION['id_usuario']}");
        return true;
    }
    global $conn; // Asegúrate de que $conn esté disponible
    error_log("Attempting to authenticate user");
    error_log("Session data: " . print_r($_SESSION, true));

    if (isset($_SESSION['username'])) {
        $user = getUserByUsernameOrEmail($_SESSION['username']);
        if ($user) {
            echo "<script>logAuthInfo('User authenticated via session: {$_SESSION['username']}');</script>";
            return true;
        } else {
            session_unset();
            session_destroy();
            error_log("User not found in database. Session cleared.");
            return false;
            
        }
    }

    // Manejo del token de recordar sesión
    if (isset($_COOKIE['remember_token'])) {
        echo "<script>logAuthInfo('Remember token found: {$_COOKIE['remember_token']}');</script>";
        $token = $_COOKIE['remember_token'];
        $id_usuario = getUserIdFromToken($token);
        error_log("User ID from token: " . ($id_usuario ?: 'Not found'));

        if ($id_usuario && validateRememberToken($id_usuario, $token)) {
            error_log("Remember token validated successfully");

            $user = getUserById($conn, $id_usuario); // Asegúrate de pasar $conn
            if ($user) {
                echo "<script>logAuthInfo('User authenticated via remember token');</script>";
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['id_tipo_usuario'] = $user['id_tipo_usuario'];

                error_log("Session data set: " . print_r($_SESSION, true));

                // Generar un nuevo token de recordar sesión
                $newToken = generateRememberToken();
                storeRememberToken($id_usuario, $newToken);
                setcookie('remember_token', $newToken, time() + (86400 * 30), "/", "", isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on', true);
                error_log("New remember token set: $newToken");

                return true;
            } else {
                error_log("User not found in database for ID: $id_usuario");
            }
        } else {
            error_log("Remember token validation failed");
            setcookie('remember_token', '', time() - 3600, '/', '', isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on', true);
            error_log("Remember token cookie cleared");
        }
    } else {
        error_log("No remember token found in cookies");
    }

    error_log("Authentication failed");
    return false;
}

function requireLogin() {
    error_log("Checking if login is required");
    if (!authenticateUser()) {
        error_log("User not authenticated. Redirecting to login page");
        header('Location: ' . BASE_URL . 'login/login.php');
        exit;
    }
    error_log("User authenticated successfully");
}

function checkPermission($requiredRole, $redirect = true) {
    error_log("Checking permission for role: $requiredRole");
    error_log("Current user role: " . ($_SESSION['user_role'] ?? 'Not set'));
    
    if ($requiredRole === 'any') {
        error_log("Any role is allowed. Permission granted.");
        return true;
    }
    
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $requiredRole) {
        error_log("Permission denied for role: $requiredRole");
        if ($redirect) {
            error_log("Redirecting to access denied page");
            header('Location: ' . BASE_URL . 'scripts/access-denied.php?role=' . $requiredRole);
            exit;
        }
        return false;
    }
    error_log("Permission granted");
    return true;
}


// Generar CSRF token si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log("New CSRF token generated: " . $_SESSION['csrf_token']);
}



error_log("auth.php completed");
