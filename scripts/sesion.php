<?php
// sesion.php
session_start();
// Suponiendo que has verificado las credenciales del usuario


// Función para verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['id_usuario']) && isset($_SESSION['id_tipo_usuario']);
}

// Función para obtener el ID del usuario actual
function getCurrentUserId() {
    return $_SESSION['id_usuario'] ?? null;
}

// Función para obtener el tipo de usuario actual
function getCurrentUserType() {
    return $_SESSION['id_tipo_usuario'] ?? null;
}

// Función para iniciar sesión
function login($id_usuario, $id_tipo_usuario) {
    $_SESSION['id_usuario'] = $id_usuario;
    $_SESSION['id_tipo_usuario'] = $id_tipo_usuario;
}

// Función para cerrar sesión
function logout() {
    session_unset();
    session_destroy();
}
?>