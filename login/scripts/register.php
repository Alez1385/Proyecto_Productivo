<?php
// register.php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
session_start();
require_once('../../scripts/conexion.php');
require_once('../../scripts/functions.php');

// Set security headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data:;");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Force HTTPS
if (!in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'])) {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
        exit();
    }
}

// CSRF Token validation
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    logError("CSRF token validation failed");
    redirectWithError("Invalid request. Please try again");
}

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar datos del formulario
    $mail = filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $id_tipo_usuario = 4; // Default user type

    // Validar datos
    if (empty($mail) || empty($username) || empty($password) || empty($password2)) {
        redirectWithError("All fields are required");
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        redirectWithError("Invalid email format");
    }

    if ($password !== $password2) {
        redirectWithError("Passwords do not match");
    }

    // Check password strength
    if (!isPasswordStrong($password)) {
        redirectWithError("Password is not strong enough. It should be at least 8 characters long and include uppercase, lowercase, numbers, and special characters");
    }

    // Check if email or username already exists
    if (isEmailTaken($conn, $mail)) {
        redirectWithError("Email is already in use.");
    }

    if (isUsernameTaken($conn, $username)) {
        redirectWithError("Username is already taken.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL query
    $sql = "INSERT INTO usuario (mail, id_tipo_usuario, username, clave) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siss', $mail, $id_tipo_usuario, $username, $hashedPassword);

    if ($stmt->execute()) {
        // Redirect to login page with success message
        header("Location: ../login.php?message=" . urlencode("Registration successful. Please log in."));
        exit();
    } else {
        logError("Database error: " . $stmt->error);
        // Redirect back to the registration form with an error message
        header("Location: ../login.php?error=" . urlencode("An error occurred during registration. Please try again later.") . "&show=register");
        exit();
    }

    $stmt->close();
} else {
    // If not a POST request, redirect to the registration page
    header("Location: ../login.php");
    exit();
}

$conn->close();

// Helper functions


function isEmailTaken($conn, $email) {
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE mail = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function isUsernameTaken($conn, $username) {
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function redirectWithError($message) {
    header("Location: ../login.php?error=" . urlencode($message . ". Please try again.") . "&show=register");
    exit();
}

function logError($error) {
    error_log(date('Y-m-d H:i:s') . " - " . $error . PHP_EOL, 3, '../../logs/error.log');
}