<?php
require_once 'conexion.php';
require_once 'config.php';
require_once BASE_PATH . 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function isValidRedirect($url) {
    if (empty($url) || !is_string($url)) {
        return false;
    }

    $parsedUrl = parse_url($url);

    if (isset($parsedUrl['path'])) {
        $basePath = strtok($parsedUrl['path'], '?');
        if (strpos($basePath, '/') === 0) {
            $allowedPaths = [
                '/dashboard/dashboard.php',
                '/inscripcion/inscripcion_completa.php',
            ];
            
            if (in_array($basePath, $allowedPaths)) {
                return true;
            }
        }
    }
    
    $isLocalDevelopment = in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);

    if (isset($parsedUrl['host'])) {
        if ($isLocalDevelopment) {
            return in_array($parsedUrl['host'], ['localhost', '127.0.0.1']);
        } else {
            $allowedHosts = ['yourdomain.com', 'www.yourdomain.com'];
            return in_array($parsedUrl['host'], $allowedHosts);
        }
    }

    return false;
}

/**
 * Generates a secure random token for "Remember Me" functionality
 *
 * @return string A 64-character hexadecimal string
 */
function generateRememberToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Stores a remember token in the database
 *
 * @param int $userId The user ID
 * @param string $token The remember token
 */
function storeRememberToken($userId, $token) {
    global $conn;
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);
    $sql = "INSERT INTO remember_tokens (id_usuario, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userId, $hashedToken);
    $stmt->execute();
}

/**
 * Validates a remember token
 *
 * @param int $id_usuario The user ID
 * @param string $token The remember token
 * @return bool True if the token is valid, false otherwise
 */

 function validateRememberToken($id_usuario, $token) {
    global $conn;
    $sql = "SELECT token FROM remember_tokens WHERE id_usuario = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return password_verify($token, $row['token']);
    }
    return false;
}

/**
 * Removes a remember token from the database
 *
 * @param int $id_usuario The user ID
 */
function removeRememberToken($id_usuario) {
    global $conn;
    $sql = "DELETE FROM remember_tokens WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    setcookie('remember_token', '', time() - 3600, '/', '', isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on', true);
}


/**
 * Hashes a password using a secure algorithm (Argon2id)
 *
 * @param string $password The password to hash
 * @return string The hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID);
}

/**
 * Verifies a password against a hash
 *
 * @param string $password The password to verify
 * @param string $hash The hash to verify against
 * @return bool True if the password is correct, false otherwise
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generates a secure random CSRF token
 *
 * @return string A 64-character hexadecimal string
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validates a CSRF token
 *
 * @param string $token The token to validate
 * @return bool True if the token is valid, false otherwise
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitizes user input to prevent XSS attacks
 *
 * @param string $input The input to sanitize
 * @return string The sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

/**
 * Logs an event for security purposes
 *
 * @param string $event The event to log
 * @param string $username The username associated with the event (optional)
 */
function logSecurityEvent($event, $username = '') {
    $logFile = '/path/to/security.log'; // Update this path
    $timestamp = date('Y-m-d H:i:s');
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $logMessage = "[$timestamp] $event | IP: $ipAddress | User: $username\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

/**
 * Checks if an IP address is allowed to make more login attempts
 *
 * @param string $ip The IP address to check
 * @return bool True if the IP is allowed, false if it's rate-limited
 */
function checkLoginAttempts($ip) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND timestamp > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count < 5; // Allow only 5 attempts per 15 minutes
}

/**
 * Records a failed login attempt
 *
 * @param string $ip The IP address to record
 */
function recordFailedLoginAttempt($ip) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO login_attempts (ip, timestamp) VALUES (?, NOW())");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();
}

/**
 * Validates password strength
 *
 * @param string $password The password to validate
 * @return bool True if the password is strong enough, false otherwise
 */
function isPasswordStrong($password) {
    $minLength = 12;
    $hasUpper = preg_match('@[A-Z]@', $password);
    $hasLower = preg_match('@[a-z]@', $password);
    $hasNumber = preg_match('@[0-9]@', $password);
    $hasSpecial = preg_match('@[^\w]@', $password);
    
    // Check against common passwords (you should expand this list)
    $commonPasswords = ['password', '123456', 'qwerty', 'letmein'];
    
    return strlen($password) >= $minLength && 
           $hasUpper && $hasLower && $hasNumber && $hasSpecial &&
           !in_array(strtolower($password), $commonPasswords);
}

function getUserIdFromToken($token) {
    global $conn;
    $sql = "SELECT id_usuario, token FROM remember_tokens WHERE expires_at > NOW() ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        if (password_verify($token, $row['token'])) {
            return $row['id_usuario'];
        }
    }
    return false;
}


function getUserInfo($conn, $id_usuario) {
    try {
        $sql = "SELECT u.*, t.nombre AS tipo_nombre FROM usuario u 
                JOIN tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario 
                WHERE u.id_usuario = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error preparing user query: ' . $conn->error);
        }
        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            throw new Exception('Error executing user query: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
}


/**
 * Summary of getUserById
 * @param mixed $conn
 * @param mixed $id_usuario
 * @return mixed
 */
function getUserById($conn, $id_usuario) {
    $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error preparing statement: " . $conn->error);
        return null;
    }
    $stmt->bind_param("i", $id_usuario);
    if (!$stmt->execute()) {
        error_log("Error executing statement: " . $stmt->error);
        return null;
    }
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        error_log("No user found with ID: $id_usuario");
        return null;
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}


/**
 * Generates a secure random password reset token
 *
 * @return string A 64-character hexadecimal string
 */
function generatePasswordResetToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Creates a password reset token and stores it in the database
 *
 * @param string $email The user's email address
 * @return string The generated token
 */
function createPasswordResetToken($email) {
    global $conn;
    $token = generatePasswordResetToken();
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->close();
    return $token;
}

/**
 * Validates a password reset token
 *
 * @param string $email The user's email address
 * @param string $token The token to validate
 * @return bool True if the token is valid, false otherwise
 */
function validatePasswordResetToken($email, $token) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $isValid = $result->num_rows > 0;
    $stmt->close();
    return $isValid;
}

function sendEmail($email, $type, $token = null, $additionalData = []) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alejo13852@gmail.com'; // Tu correo de Gmail
        $mail->Password = 'nqpg trtb sidj awvg';  // Tu contraseña de aplicación de Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('alejo13852@gmail.com', 'CorsaCor');
        $mail->addAddress($email);

        switch ($type) {
            case 'reset':
                if ($token === null) {
                    throw new Exception('Token is required for password reset emails');
                }
                $resetLink = "https://yourdomain.com/reset_password.php?token=" . urlencode($token) . "&email=" . urlencode($email);
                $subject = "Password Reset Request";
                $message = "Click the following link to reset your password: $resetLink";
                break;

            case 'lockout':
                $subject = "Account Locked - Unusual Activity Detected";
                $message = "Your account has been temporarily locked due to multiple failed login attempts. If this wasn't you, please contact our support team.";
                break;

            case 'preinscription':
                $subject = "Preinscripción Confirmada";
                $courseName = $additionalData['courseName'] ?? 'el curso';
                $message = "Gracias por preinscribirte en $courseName. Tu preinscripción ha sido recibida y está siendo procesada.";
                if (isset($additionalData['tempPassword'])) {
                    $message .= "\n\nComo nuevo usuario, tu contraseña temporal es: " . $additionalData['tempPassword'] . "\nPor favor, cambia esta contraseña la próxima vez que inicies sesión.";
                }
                break;

            default:
                throw new Exception('Invalid email type');
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        $mail->send();
    } catch (Exception $e) {
        throw new Exception('Failed to send email: ' . $mail->ErrorInfo);
    }
}


/**
 * Locks a user account
 *
 * @param int $userId The user ID to lock
 */
function lockUserAccount($userId) {
    global $conn;
    $stmt = $conn->prepare("UPDATE usuario SET is_locked = 1, lock_timestamp = NOW() WHERE id_usuario = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}

/**
 * Unlocks a user account
 *
 * @param int $userId The user ID to unlock
 */
function unlockUserAccount($userId) {
    global $conn;
    $stmt = $conn->prepare("UPDATE usuario SET is_locked = 0, lock_timestamp = NULL WHERE id_usuario = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}



/**
 * Checks if a user account is locked
 *
 * @param int $userId The user ID to check
 * @return bool True if the account is locked, false otherwise
 */
function isAccountLocked($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT is_locked FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($isLocked);
    $stmt->fetch();
    $stmt->close();
    return $isLocked;
}

/**
 * Updates the last access timestamp for a user
 *
 * @param int $userId The user ID to update
 */
function updateLastAccess($userId) {
    global $conn;
    $stmt = $conn->prepare("UPDATE usuario SET ultimo_acceso = NOW() WHERE id_usuario = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}

/**
 * Retrieves user information by username or email
 *
 * @param string $usernameOrEmail The username or email to look up
 * @return array|null The user information or null if not found
 */
function getUserByUsernameOrEmail($usernameOrEmail) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE username = ? OR mail = ?");
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

/**
 * Creates a new user account
 *
 * @param array $userData An array containing user information
 * @return int|false The new user ID or false on failure
 */
function createUser($userData) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO usuario (nombre, apellido, mail, telefono, id_tipo_usuario, username, clave) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $hashedPassword = hashPassword($userData['password']);
        $stmt->bind_param("ssssiss", $userData['nombre'], $userData['apellido'], $userData['mail'], $userData['telefono'], $userData['id_tipo_usuario'], $userData['username'], $hashedPassword);
        
        if ($stmt->execute()) {
            $newUserId = $stmt->insert_id;
            $stmt->close();
            return $newUserId;
        } else {
            throw new Exception("Error creating user: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}


/**
 * Updates user information
 *
 * @param int $userId The user ID to update
 * @param array $userData An array containing the updated user information
 * @return bool True on success, false on failure
 */
function updateUser($userId, $userData) {
    global $conn;
    $stmt = $conn->prepare("UPDATE usuario SET nombre = ?, apellido = ?, mail = ?, telefono = ? WHERE id_usuario = ?");
    $stmt->bind_param("ssssi", $userData['nombre'], $userData['apellido'], $userData['mail'], $userData['telefono'], $userId);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Changes a user's password
 *
 * @param int $userId The user ID
 * @param string $newPassword The new password
 * @return bool True on success, false on failure
 */
function changeUserPassword($userId, $newPassword) {
    global $conn;
    $hashedPassword = hashPassword($newPassword);
    $stmt = $conn->prepare("UPDATE usuario SET clave = ? WHERE id_usuario = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Deletes a user account
 *
 * @param int $userId The user ID to delete
 * @return bool True on success, false on failure
 */
function deleteUser($userId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $userId);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}