<!DOCTYPE html>
<html lang="es" class="tw-h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="../js/auth-debug.js"></script>
    <link rel="stylesheet" href="/dist/css/styles.css">
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

        .tw-animated-bg {
            background: linear-gradient(-45deg, #e3f2fd, #bbdefb, #90caf9, #64b5f6);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }
    </style>
</head>

<body class="tw-h-full tw-flex tw-items-center tw-justify-center tw-p-4 tw-animated-bg">
<div class="tw-w-full tw-max-w-md">
        <div class="tw-bg-white tw-shadow-2xl tw-rounded-lg tw-overflow-hidden">
            <div class="tw-p-8">
                <div class="tw-flex tw-justify-center tw-mb-6">
                    <svg class="tw-w-16 tw-h-16 tw-text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="tw-text-center tw-text-3xl tw-font-extrabold tw-text-gray-900 tw-mb-4">
                    Acceso Denegado
                </h2>
                <p class="tw-text-center tw-text-sm tw-text-gray-600 tw-mb-2">
                    Lo sentimos, no tienes permiso para acceder a esta página.
                </p>
                <p class="tw-text-center tw-text-sm tw-text-gray-600 tw-mb-4">
                    Por favor, contacta al administrador si crees que esto es un error.
                </p>
                <?php
                $requiredRole = isset($_GET['role']) ? htmlspecialchars($_GET['role']) : 'unknown';
                ?>
                <p class="tw-text-center tw-text-sm tw-font-medium tw-text-gray-900 tw-mb-6">
                    Se requiere el rol de "<span class="tw-font-bold tw-text-red-600"><?php echo $requiredRole; ?></span>" para acceder a esta página.
                </p>
                <div class="tw-space-y-4">
                    <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '/dashboard/dashboard.php'; ?>" class="tw-w-full tw-block tw-text-center tw-py-2 tw-px-4 tw-border tw-border-transparent tw-rounded-md tw-shadow-sm tw-text-sm tw-font-medium tw-text-white tw-bg-indigo-600 hover:tw-bg-indigo-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500 transition duration-150 ease-in-out">
                        Volver
                    </a>
                    <a href="/login/logout.php" class="tw-w-full tw-block tw-text-center tw-py-2 tw-px-4 tw-border tw-border-transparent tw-rounded-md tw-shadow-sm tw-text-sm tw-font-medium tw-text-white tw-bg-red-600 hover:tw-bg-red-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-red-500 transition duration-150 ease-in-out">
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
