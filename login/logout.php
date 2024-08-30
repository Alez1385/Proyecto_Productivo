<?php
// logout.php

// Inicia la sesión
session_start();

// Destruye todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión por completo, también se debe eliminar la cookie de sesión.
// Nota: Esta parte sólo es necesaria si se utiliza una cookie de sesión
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

// Finalmente, destruye la sesión
session_destroy();

// Redirige al usuario a la página de inicio de sesión (o a la página principal)
header("Location: ../../../login/login.php"); // Cambia la ubicación según sea necesario
exit;
