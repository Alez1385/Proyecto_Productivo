<?php
session_start();
include_once '../../scripts/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
$id_tipo_usuario = $_SESSION['id_tipo_usuario'] ?? null;

// Si no tenemos el tipo de usuario en sesión, buscarlo en la base de datos
if ($id_usuario && !$id_tipo_usuario) {
    $sql_tipo = "SELECT id_tipo_usuario FROM usuario WHERE id_usuario = ?";
    $stmt_tipo = $conn->prepare($sql_tipo);
    $stmt_tipo->bind_param("i", $id_usuario);
    $stmt_tipo->execute();
    $result_tipo = $stmt_tipo->get_result();
    if ($result_tipo && $row_tipo = $result_tipo->fetch_assoc()) {
        $id_tipo_usuario = $row_tipo['id_tipo_usuario'];
        $_SESSION['id_tipo_usuario'] = $id_tipo_usuario; // Guardar en sesión para futuras consultas
    }
}

// Permitir acceso a admin (tipo 1) y user (tipo 4) para pruebas
if (!$id_usuario || ($id_tipo_usuario != 4 && $id_tipo_usuario != 1)) {
    echo '<div style="color:red;padding:20px;">Acceso restringido solo para usuarios tipo "user" y administradores.</div>';
    echo '<p>Tu tipo de usuario actual es: ' . $id_tipo_usuario . '</p>';
    exit;
}

$notificaciones = [];
$check = $conn->query("SHOW TABLES LIKE 'notificaciones_user'");
if ($check && $check->num_rows > 0) {
    $sql_notif = "SELECT * FROM notificaciones_user WHERE id_usuario = ? ORDER BY fecha DESC LIMIT 50";
    $stmt_notif = $conn->prepare($sql_notif);
    if ($stmt_notif) {
        $stmt_notif->bind_param("i", $id_usuario);
        $stmt_notif->execute();
        $res_notif = $stmt_notif->get_result();
        while ($row = $res_notif->fetch_assoc()) {
            $notificaciones[] = $row;
        }
    }
}

// Lógica para marcar como leída y registrar en historial
if (isset($_GET['marcar_leida']) && isset($_GET['id_notificacion'])) {
    $id_notif = intval($_GET['id_notificacion']);
    // Marcar como leída en notificaciones_user
    $stmt_leer = $conn->prepare("UPDATE notificaciones_user SET leido = 1 WHERE id_notificacion = ? AND id_usuario = ?");
    $stmt_leer->bind_param("ii", $id_notif, $id_usuario);
    $stmt_leer->execute();
    // Insertar en historial_notificaciones_user
    $stmt_hist = $conn->prepare("INSERT INTO historial_notificaciones_user (id_usuario, id_notificacion, leido) VALUES (?, ?, 1)");
    $stmt_hist->bind_param("ii", $id_usuario, $id_notif);
    $stmt_hist->execute();
    // Redirigir para evitar re-envío
    header("Location: notificaciones_user.php");
    exit;
}
// Lógica para mostrar historial
$historial = [];
if (isset($_GET['ver_historial'])) {
    $sql_hist = "SELECT h.*, n.titulo, n.mensaje, n.fecha as fecha_notif FROM historial_notificaciones_user h JOIN notificaciones_user n ON h.id_notificacion = n.id_notificacion WHERE h.id_usuario = ? ORDER BY h.fecha_vista DESC LIMIT 100";
    $stmt_hist = $conn->prepare($sql_hist);
    $stmt_hist->bind_param("i", $id_usuario);
    $stmt_hist->execute();
    $res_hist = $stmt_hist->get_result();
    while ($row = $res_hist->fetch_assoc()) {
        $historial[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - Usuario</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container-flex {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 20px 24px 20px 24px; /* 24px a la izquierda y derecha */
            width: 100%;
            box-sizing: border-box;
        }
        .header {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 1.8em;
        }
        .notificaciones-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .notificaciones-table {
            width: 100%;
            border-collapse: collapse;
        }
        .notificaciones-table th {
            background: #3498db;
            color: #fff;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        .notificaciones-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .notificaciones-table tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .status-badge.leido {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.no-leido {
            background: #fff3cd;
            color: #856404;
        }
        .no-notificaciones {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .no-notificaciones i {
            font-size: 3em;
            color: #ddd;
            margin-bottom: 10px;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .main-content {
                padding: 15px 8px 15px 8px;
            }
            .header h1 {
                font-size: 1.5em;
            }
            .notificaciones-table th,
            .notificaciones-table td {
                padding: 10px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 10px 2px 10px 2px;
            }
            .notificaciones-table {
                font-size: 12px;
            }
            .notificaciones-table th,
            .notificaciones-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container-flex">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <div class="header">
                <h1><i class="material-icons-sharp" style="margin-right:10px;color:#3498db;">notifications</i>Mis Notificaciones</h1>
            </div>
            
            <div class="notificaciones-container">
                <?php if (!empty($notificaciones)): ?>
                    <table class="notificaciones-table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notificaciones as $notif): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($notif['titulo']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($notif['mensaje']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($notif['fecha'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $notif['leido'] ? 'leido' : 'no-leido'; ?>">
                                            <?php echo $notif['leido'] ? 'Leído' : 'No leído'; ?>
                                        </span>
                                        <?php if (!$notif['leido']): ?>
                                            <a href="?marcar_leida=1&id_notificacion=<?php echo $notif['id_notificacion']; ?>" class="btn-mark-read" style="margin-left: 10px; padding: 5px 10px; background-color: #4CAF50; color: white; border-radius: 5px; text-decoration: none; font-size: 0.9em;">
                                                Marcar como leída
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-notificaciones">
                        <i class="material-icons-sharp">notifications_none</i>
                        <h3>No tienes notificaciones</h3>
                        <p>Aquí aparecerán las notificaciones cuando se te rechace o cancele una inscripción.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($_GET['ver_historial'])): ?>
                <div class="header" style="margin-top: 20px;">
                    <h1><i class="material-icons-sharp" style="margin-right:10px;color:#3498db;">history</i>Historial de Notificaciones</h1>
                </div>
                <div class="notificaciones-container">
                    <?php if (!empty($historial)): ?>
                        <table class="notificaciones-table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Mensaje</th>
                                    <th>Fecha de Notificación</th>
                                    <th>Fecha de Visualización</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial as $hist): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($hist['titulo']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($hist['mensaje']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($hist['fecha_notif'])); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($hist['fecha_vista'])); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $hist['leido'] ? 'leido' : 'no-leido'; ?>">
                                                <?php echo $hist['leido'] ? 'Leído' : 'No leído'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-notificaciones">
                            <i class="material-icons-sharp">history</i>
                            <h3>No tienes notificaciones en tu historial.</h3>
                            <p>Aquí se registran todas las notificaciones que has marcado como leídas.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 