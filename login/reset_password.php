<?php
require_once '../scripts/conexion.php';

$token = $_GET['token'] ?? '';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $reset = $result->fetch_assoc();
                $email = $reset['email'];
                
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                $stmt = $conn->prepare("UPDATE usuario SET clave = ? WHERE mail = ?");
                $stmt->bind_param("ss", $hashedPassword, $email);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                    $stmt->bind_param("s", $token);
                    $stmt->execute();
                    
                    $success = "Your password has been updated successfully.";
                } else {
                    $errors[] = "Failed to update password. Please try again.";
                }
            } else {
                $errors[] = "Invalid or expired reset link.";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        } finally {
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/style-reset.css">
</head>
<body>
    <div class="login-container">
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">
                <p><?php echo htmlspecialchars($success); ?></p>
                <a href="login.php">Return to Login</a>
            </div>
        <?php else: ?>
            <form class="login-form" action="reset_password.php" method="POST">
                <h2>Reset Password</h2>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="input-group">
                    <img src="img/lock-icon.svg" alt="Lock-Icon" class="icon">
                    <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock1" onclick="togglePassword('password1', 'toggleLock1')">
                    <input type="password" placeholder="New Password" name="password" id="password1" required>
                </div>
                <div class="input-group">
                    <img src="img/lock-icon.svg" alt="Lock-Icon" class="icon">
                    <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock2" onclick="togglePassword('password2', 'toggleLock2')">
                    <input type="password" placeholder="Confirm Password" name="confirm_password" id="password2" required>
                </div>
                <button type="submit" class="btn-login">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="scripts/password.js"></script>
</body>
</html>

