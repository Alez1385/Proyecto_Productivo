<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style-login.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden; /* Evita el desplazamiento de fondo mientras el modal está abierto */
        }

        .login-container {
            background-color: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .login-form h2 {
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
        }

        .input-group input {
            padding: 12px 50px;
            border-radius: 10px;
            font-size: 18px;
        }

        .lock-icon {
            width: 25px;
            height: 25px;
        }

        .btn-login {
            padding: 12px;
            background-color: #007bff;
            border-radius: 10px;
            font-size: 18px;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-login:hover {
            background-color: #0056b3;
        }

        .social-container {
            margin-top: 30px;
        }

        .google-login img {
            width: 50px;
            height: 50px;
        }

        .options a {
            color: #0056b3;
        }

        .options label {
            font-size: 16px;
        }

        p {
            font-size: 16px;
            color: #333;
        }

        /* Estilos para el modal y el overlay */
        .overlay {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 2;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            text-align: center;
            position: relative;
        }

        .modal-header {
            font-size: 24px;
            font-weight: bold;
            color: #007BFF;
            margin-bottom: 10px;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-footer {
            font-size: 16px;
            color: #333;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .close:hover,
        .close:focus {
            color: #007BFF;
            text-decoration: none;
        }

        .loading-bar {
            width: 100%;
            height: 5px;
            background-color: #007BFF;
            margin: 10px 0;
            position: relative;
            overflow: hidden;
        }

        .loading-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background-color: #0056b3;
            animation: loading 2s linear infinite;
        }

        @keyframes loading {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }
    </style>
    <script>
        function showSuccessModal() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('message') && urlParams.get('message') === 'success') {
                document.getElementById('overlay').style.display = 'block';
                document.getElementById('successModal').style.display = 'flex';
                setTimeout(() => {
                    closeModal();
                }, 3000);
            }
        }

        function closeModal() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('successModal').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', showSuccessModal);
    </script>
</head>
<body>
    <div class="login-container">
        <form class="login-form" action="login_process.php" method="POST">
            <h2>Sign in</h2>

            <div class="input-group">
                <img src="img/user-icon.svg" alt="User Icon" class="icon">
                <input type="text" placeholder="Username" name="username" required>
            </div>

            <div class="input-group">
                <img src="img/lock-icon.svg" alt="Lock Icon" class="icon">
                <input type="password" placeholder="Password" name="password" id="password" required>
                <img src="img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword()">
            </div>

            <div class="options">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login">LOGIN</button>

            <p>Or Sign in with Google</p>
            <div class="social-container">
                <a href="#" class="google-login"><img src="img/google-icon.svg" alt="Google"></a>
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
            <div class="modal-header">Success!</div>
            <div class="modal-body">
                <p>Usuario creado con éxito. Ahora puedes iniciar sesión.</p>
                <div class="loading-bar"></div>
            </div>
            <div class="modal-footer">
                <p>¡Gracias!</p>
            </div>
        </div>
    </div>

    <script src="scripts/script_login.js"></script>
</body>
</html>
