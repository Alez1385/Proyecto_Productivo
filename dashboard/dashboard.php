<?php
require_once "../scripts/conexion.php";
require_once "../scripts/auth.php";

requireLogin();

// Obtener el tipo de usuario
$user = getUserInfo($conn, $_SESSION['id_usuario']);

// Debug: Log user information
error_log("Dashboard - User ID: " . $_SESSION['id_usuario']);
error_log("Dashboard - User Role from Session: " . ($_SESSION['user_role'] ?? 'Not set'));
error_log("Dashboard - User Type from DB: " . ($user['tipo_nombre'] ?? 'Not set'));

// Contenido HTML común
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo ucfirst($user['tipo_nombre']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/dashboard_profesor.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include "../scripts/sidebar.php"; ?>

        <div class="main-content">
            <header class="header">
                <section class="user-info">
                    <h2>Información del Usuario</h2>
                    <div class="user-details">
                        <?php
                        $foto_path = '../uploads/' . $user['foto'];
                        if (!empty($user['foto']) && file_exists($foto_path)) {
                            echo '<img src="' . htmlspecialchars($foto_path) . '" alt="Foto de perfil" class="user-photo">';
                        } else {
                            echo '<i class="fas fa-user-circle user-icon"></i>';
                        }
                        ?>
                        <div class="user-text">
                            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['mail']); ?></p>
                            <p><strong>Tipo de Usuario:</strong> <?php echo htmlspecialchars($user['tipo_nombre']); ?></p>
                            <p><strong>Último Acceso:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($user['ultimo_acceso']))); ?></p>
                        </div>
                    </div>
                </section>
            </header>

            <?php
            // Contenido específico según el tipo de usuario
            error_log("Dashboard - Processing user type: " . $user["tipo_nombre"]);
            
            switch ($user["tipo_nombre"]) {
                case 'admin':
                    error_log("Dashboard - Including admin dashboard");
                    include 'dashboard_admin.php';
                    echo "<script src='../js/loadCss.js'></script>";
                    echo "<script>loadCSS('css/dash.css');</script>";
                    break;
                case 'estudiante':
                    error_log("Dashboard - Including student dashboard");
                    include 'dashboard_estudiante.php';
                    echo "<script src='../js/loadCss.js'></script>";
                    echo "<script>
                    loadCSS('/dist/css/styles.css');loadCSS('css/estudiante.css');
                    </script>";
                    break;
                case 'profesor':
                    error_log("Dashboard - Including professor dashboard");
                    include 'dashboard_profesor.php';
                    echo "<script src='../js/loadCss.js'></script>";
                    echo "<script>loadCSS('css/dash.css');</script>";
                    break;
                case 'user':
                    error_log("Dashboard - Including user dashboard");
                    include 'dashboard_user.php';
                    echo "<script src='../js/loadCss.js'></script>";
                    echo "<script>loadCSS('css/user.css');</script>";
                    break;
                default:
                    error_log("Dashboard - Unknown user type: " . $user["tipo_nombre"]);
                    echo "<p>Tipo de usuario no reconocido: " . htmlspecialchars($user["tipo_nombre"]) . "</p>";
            }
            ?>

        </div>
    </div>

    <script src="js/dashboard.js"></script>
</body>

</html>
<?php
$conn->close();
?>
