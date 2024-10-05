<?php
require_once "../scripts/conexion.php";
require_once "../scripts/auth.php";

requireLogin();

// Obtener el tipo de usuario
$user = getUserInfo($conn, $_SESSION['id_usuario']);

function getDatabaseData($conn, $query)
{
    $result = $conn->query($query);
    if (!$result) {
        die("Error al ejecutar la consulta: " . $conn->error);
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

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
</head>

<body>
    <div class="dashboard-container">
        <?php include "../scripts/sidebar.php"; ?>

        <div class="main-content">
            <header class="header">
                <section class="user-info">
                    <h2>Información del Usuario</h2>
                    <div class="user-details">
                        <img src="../../uploads/<?php echo htmlspecialchars($user['foto']); ?>" alt="Foto de perfil">
                        <div>
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
            switch ($user["tipo_nombre"]) {
                case 'admin':
                    include 'dashboard_admin.php';
                    echo "<script src='../js/loadCss.js'></script>";
                    echo "<script>loadCSS('css/dash.css');</script>";
                    break;
                case 'estudiante':
                    include 'dashboard_estudiante.php';
                    echo "<script src='../js/loadCss.js'></script>";
                    echo "<script>loadCSS('css/estudiante.css');</script>";
                    break;
                case 'profesor':
                    include 'dashboard_profesor.php';
                    echo "<script src='../js/loadCss.js'></script>";
                    echo "<script>loadCSS('css/profesor.css');</script>"; // Assuming there's a profesor.css file
                    break;
                default:
                    echo "<p>Tipo de usuario no reconocido.</p>";
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