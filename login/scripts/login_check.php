<!-- login_check.php -->
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


error_log("Login attempt for username: $username");
error_log("Remember me option: " . ($remember ? 'Yes' : 'No'));

// Handle and validate the redirect URL
$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL) ?? 
            filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_URL) ?? 
            '/dashboard/dashboard.php';

error_log("Redirect URL: $redirect");

// Validar que el redirect no sea una URL maliciosa
if (!isValidRedirect($redirect)) {
    error_log("Invalid redirect URL detected. Defaulting to dashboard.");
    $redirect = '/dashboard/dashboard.php';
}

// Verify if there are empty fields
if (empty($username) || empty($password)) {
    error_log("Empty fields detected");
    redirectWithError('emptyfields');
}

// Prepare SQL query
$sql = "SELECT u.id_usuario, u.username, u.clave, u.mail, u.is_locked, u.lock_timestamp, t.nombre AS tipo_nombre
        FROM usuario u
        JOIN tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario
        WHERE u.username = ? OR u.mail = ?";
error_log("Executing SQL query: $sql");

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

// Verify if the user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    error_log("User found: " . print_r($user, true));
    
    // Check if the account is locked
    if ($user['is_locked']) {
        $lockDuration = 15 * 60; // 15 minutes in seconds
        $timeSinceLock = time() - strtotime($user['lock_timestamp']);
        
        error_log("Account is locked. Time since lock: $timeSinceLock seconds");
        
        if ($timeSinceLock < $lockDuration) {
            error_log("Account still locked. Redirecting.");
            redirectWithError('account_locked');
        } else {
            error_log("Lock duration passed. Unlocking account.");
            unlockUserAccount($user['id_usuario']);
        }
    }

    // Verify the hashed password with bcrypt
    if (password_verify($password, $user['clave'])) {
        error_log("Password verified successfully");
        // Successful authentication, create sessions
        if (isset($user['id_usuario'])) {
            $_SESSION['id_usuario'] = $user['id_usuario'];
            error_log("Session ID set successfully");
        } else {
            error_log("Error: User ID not found in database.");
            redirectWithError('invalid_user');
        }
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['tipo_nombre'];  // Using tipo_nombre as user_role
        
        error_log("Session data set: " . print_r($_SESSION, true));
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        error_log("Session ID regenerated");

        // Remember user with cookies if selected
        if ($remember) {
            $token = generateRememberToken();
            storeRememberToken($user['id_usuario'], $token);

            $secure = false; // Para pruebas locales, cambia esto a true en producción
            setcookie('remember_token', $token, [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'secure' => $secure, // Asegúrate de que esto esté correcto
                'httponly' => true,
                'samesite' => 'Strict' // O 'Lax' si la cookie es usada en subdominios
            ]);            


            error_log("Remember token set: $token");
        } else {
            error_log("Removing any existing remember token");
            removeRememberToken($user['id_usuario']);
        }
       
        // Set a cookie to indicate successful login
        setcookie('login_success', 'true', time() + 60, '/', '', isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on', true);
       
        // Log successful login
        logSecurityEvent('Successful login', $user['username']);
    
        error_log("Login successful. Redirecting to: $redirect");
        echo "<script>
        logAuthInfo('Login successful');
        logAuthInfo('Redirecting to: $redirect');
        </script>";  
        // Redirect to dashboard
        header("Location: " . $redirect);
        exit();
    } else {
        echo "<script>
            logAuthInfo('Login failed: $errorMessage');
        </script>";
        error_log("Password verification failed");
        // Failed login attempt
        recordFailedLoginAttempt($_SERVER['REMOTE_ADDR']);
        if (!checkLoginAttempts($_SERVER['REMOTE_ADDR'])) {
            error_log("Too many failed attempts. Locking account.");
            lockUserAccount($user['id_usuario']);
            sendLockoutNotificationEmail($user['mail']);    
            logSecurityEvent('Failed login attempt', $user['username']);
            redirectWithError('account_locked');
        }
        redirectWithError('invalidpassword');
    }
} else {
    error_log("No user found with username: $username");
    // User not found
    redirectWithError('nouser');
}

// Close the connection
$stmt->close();
$conn->close();
error_log("Database connection closed");



function getErrorMessage($error) {
    $errorMessages = [
        'invalid_token' => 'Invalid request. Please try again.',
        'account_locked' => 'Your account has been temporarily locked due to multiple failed login attempts. Please try again later or contact support.',
        'emptyfields' => 'Please fill in all fields.',
        'invalidpassword' => 'Invalid username or password.',
        'nouser' => 'Invalid username or password.',
        'default' => 'An error occurred. Please try again.'
    ];

    return $errorMessages[$error] ?? $errorMessages['default'];
}


/**
 * Redirect to login page with an error message and preserve the redirect URL
 *
 * @param string $error The error type to display
 */
function redirectWithError($error) {
    global $redirect;
    $encodedRedirect = urlencode($redirect);
    $errorMessage = getErrorMessage($error);
    $redirectUrl = "/login/login.php?error=" . urlencode($errorMessage) . "&redirect=" . $encodedRedirect;
    
    error_log("Redirecting with error: $error. URL: $redirectUrl");
    header("Location: " . $redirectUrl);
    exit();
}