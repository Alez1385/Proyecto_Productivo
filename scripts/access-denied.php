<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="../js/auth-debug.js"></script>
    <title>Acceso Denegado</title>
    <style>
        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animated-bg {
            background: linear-gradient(-45deg, #e3f2fd, #bbdefb, #90caf9, #64b5f6);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }
    </style>
</head>

<body class="h-full flex items-center justify-center p-4 animated-bg">
<div class="w-full max-w-md">
        <div class="bg-white shadow-2xl rounded-lg overflow-hidden">
            <div class="p-8">
                <div class="flex justify-center mb-6">
                    <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-center text-3xl font-extrabold text-gray-900 mb-4">
                    Acceso Denegado
                </h2>
                <p class="text-center text-sm text-gray-600 mb-2">
                    Lo sentimos, no tienes permiso para acceder a esta página.
                </p>
                <p class="text-center text-sm text-gray-600 mb-4">
                    Por favor, contacta al administrador si crees que esto es un error.
                </p>
                <?php
                $requiredRole = isset($_GET['role']) ? htmlspecialchars($_GET['role']) : 'unknown';
                ?>
                <p class="text-center text-sm font-medium text-gray-900 mb-6">
                    Se requiere el rol de "<span class="font-bold text-red-600"><?php echo $requiredRole; ?></span>" para acceder a esta página.
                </p>
                <div class="space-y-4">
                    <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '/dashboard/dashboard.php'; ?>" class="w-full block text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Volver
                    </a>
                    <a href="/login/logout.php" class="w-full block text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        console.log('Access denied page loaded');
        logAuthInfo('Access denied page loaded');
        logAuthInfo('Redirecting to: ' + window.location.href);
    </script>
</body>

</html>