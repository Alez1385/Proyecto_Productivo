<?php
// Iniciar la sesión
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se utiliza una cookie de sesión, eliminarla
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Eliminar la cookie 'username'
if (isset($_COOKIE['username'])) {
    setcookie('username', '', time() - 3600, "/"); // Expirarla
}
if (isset($_COOKIE['id_usuario'])) {
    setcookie('id_usuario', '', time() - 3600, "/"); // Expirarla
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: ../../../login/login.php");
exit();
