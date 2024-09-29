<?php

session_start();
require_once '../../scripts/conexion.php';
require_once '../../scripts/functions.php';
require_once '../../scripts/config.php';

// Log CSRF token information
error_log("Session CSRF Token: " . ($_SESSION['csrf_token'] ?? 'Not set'));
error_log("Posted CSRF Token: " . ($_POST['csrf_token'] ?? 'Not set'));

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    error_log("CSRF Token validation failed");
    redirectWithError('invalid_token');
} else {
    error_log("CSRF Token validation passed");
}

// Validate and sanitize inputs
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

// Handle and validate the redirect URL
$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL) ?? 
            filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_URL) ?? 
            '/dashboard/dashboard.php';

// Validar que el redirect no sea una URL maliciosa
if (!isValidRedirect($redirect)) {
    $redirect = '/dashboard/dashboard.php';
}

// Verify if there are empty fields
if (empty($username) || empty($password)) {
    redirectWithError('emptyfields');
}

// Prepare SQL query
$sql = "SELECT id_usuario, username, clave, mail, is_locked, lock_timestamp FROM usuario WHERE username = ? OR mail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

// Verify if the user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Check if the account is locked
    if ($user['is_locked']) {
        $lockDuration = 15 * 60; // 15 minutes in seconds
        $timeSinceLock = time() - strtotime($user['lock_timestamp']);
        
        if ($timeSinceLock < $lockDuration) {
            redirectWithError('account_locked');
        } else {
            // Unlock the account if lock duration has passed
            unlockUserAccount($user['id_usuario']);
        }
    }
   
    // Verify the hashed password with bcrypt
    if (password_verify($password, $user['clave'])) {
        // Successful authentication, create sessions
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_usuario'] = $user['id_usuario'];
       
        // Remember user with cookies if selected
        if ($remember) {
            $token = generateRememberToken();
            storeRememberToken($user['id_usuario'], $token);
            
            $secure = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on';
            $httponly = true;
            setcookie('remember_token', $token, time() + (86400 * 30), "/", "", $secure, $httponly);
        }
       
        // Set a cookie to indicate successful login
        setcookie('login_success', 'true', time() + 60, '/', '', isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on', true);
       
        // Log successful login
        logSecurityEvent('Successful login', $user['username']);

        // Depuración
        error_log("Redirecting to: " . $redirect);
        
        // Redirección final
        header("Location: " . $redirect);
        exit();
    } else {
        // Failed login attempt
        recordFailedLoginAttempt($_SERVER['REMOTE_ADDR']);
        if (!checkLoginAttempts($_SERVER['REMOTE_ADDR'])) {
            lockUserAccount($user['id_usuario']);
            sendLockoutNotificationEmail($user['mail']);    
            logSecurityEvent('Failed login attempt', $user['username']);
            redirectWithError('account_locked');
        }
        redirectWithError('invalidpassword');
    }
} else {
    // User not found
    redirectWithError('nouser');
}

// Close the connection
$stmt->close();
$conn->close();

/**
 * Redirect to login page with an error message and preserve the redirect URL
 *
 * @param string $error The error type to display
 */
function redirectWithError($error) {
    global $redirect;
    $encodedRedirect = urlencode($redirect);
    $errorMessage = '';
    switch ($error) {
        case 'invalid_token':
            $errorMessage = 'Invalid request. Please try again.';
            break;
        case 'account_locked':
            $errorMessage = 'Your account has been temporarily locked due to multiple failed login attempts. Please try again later or contact support.';
            break;
        case 'emptyfields':
            $errorMessage = 'Please fill in all fields.';
            break;
        case 'invalidpassword':
            $errorMessage = 'Invalid username or password.';
            break;
        case 'nouser':
            $errorMessage = 'Invalid username or password.';
            break;
        default:
            $errorMessage = 'An error occurred. Please try again.';
    }
    $redirectUrl = "/login/login.php?error=" . urlencode($errorMessage) . "&redirect=" . $encodedRedirect;
    
    header("Location: " . $redirectUrl);
    exit();
}