<?php
session_start();
include('../scripts/conexion.php');

if (isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    header("Location: ../dashboard/dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT clave FROM usuario WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;

            if (isset($_POST['remember'])) {
                setcookie('username', $username, time() + (86400 * 30), "/", "", true, true);
            }

            header("Location: ../dashboard/dashboard.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style-login.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <script src="scripts/password.js"></script>
    <script>
        function showSuccessModal() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('message') && urlParams.get('message') === 'success') {
                document.getElementById('overlay').style.display = 'block';
                document.getElementById('successModal').style.display = 'flex';
                setTimeout(() => {
                    closeModal();
                    removeURLParameter('message');
                }, 3000);
            } else {
                document.getElementById('overlay').style.display = 'none';
                document.getElementById('successModal').style.display = 'none';
            }
        }

        function closeModal() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('successModal').style.display = 'none';
        }

        function removeURLParameter(param) {
            let url = new URL(window.location.href);
            url.searchParams.delete(param);
            window.history.replaceState(null, '', url);
        }



        document.addEventListener('DOMContentLoaded', showSuccessModal);
    </script>
</head>

<body>
    <div class="login-container">
        <form class="login-form" action="" method="POST" enctype="application/x-www-form-urlencoded">

            <h2>Sign in</h2>

            <div class="input-group">
                <img src="img/user-icon.svg" alt="User Icon" class="icon">
                <input type="text" placeholder="Username" name="username" required>
            </div>

            <div class="input-group">
                <img src="img/lock-icon.svg" alt="Lock-Icon" class="icon">
                <input type="password" placeholder="Password" name="password" id="password" required>
                <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
            </div>

            <div class="options">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="forgot_password.html" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login">LOGIN</button>

            <p>Or Sign in with Google</p>
            <div class="social-container">
                <a href="scripts/google_login.php" class="google-login"><img src="img/google-icon.svg" alt="Google"></a>
            </div>

            <p>¿No tienes una cuenta? <a href="register.html" class="register-link">Regístrate aquí</a>.</p>
        </form>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="overlay"></div>

    <!-- Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="modal-header">Completado</div>
            <div class="modal-body">Tu registro ha sido exitoso!</div>
            <div class="loading-bar"></div>
        </div>
    </div>
</body>

</html>