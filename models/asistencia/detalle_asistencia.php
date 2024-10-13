<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('estudiante');

// Obtener el id_estudiante correcto
$id_usuario = $_SESSION['id_usuario'];
$sql_estudiante = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ?";
$stmt_estudiante = $conn->prepare($sql_estudiante);
$stmt_estudiante->bind_param("i", $id_usuario);
$stmt_estudiante->execute();
$result_estudiante = $stmt_estudiante->get_result();
$estudiante = $result_estudiante->fetch_assoc();
$id_estudiante = $estudiante['id_estudiante'];

$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;

if ($id_curso == 0) {
    die("ID de curso no válido");
}

// Obtener información del curso
$sql_curso = "SELECT nombre_curso FROM cursos WHERE id_curso = ?";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $id_curso);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();
$curso = $result_curso->fetch_assoc();

// Obtener detalles de asistencia
$sql_asistencias = "SELECT a.fecha, a.estado
                    FROM asistencia a
                    WHERE a.id_estudiante = ? AND a.id_curso = ?
                    ORDER BY a.fecha DESC";
$stmt_asistencias = $conn->prepare($sql_asistencias);
if (!$stmt_asistencias) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt_asistencias->bind_param("ii", $id_estudiante, $id_curso);
$stmt_asistencias->execute();
$result_asistencias = $stmt_asistencias->get_result();

// Calcular estadísticas
$total_clases = $result_asistencias->num_rows;
$asistencias = 0;
$inasistencias = 0;
$retardos = 0;


while ($row = $result_asistencias->fetch_assoc()) {
    switch ($row['estado']) {
        case 'presente':
            $asistencias++;
            break;
        case 'ausente':
            $inasistencias++;
            break;
        case 'retardo':
            $retardos++;
            break;
    }
}

$porcentaje_asistencia = $total_clases > 0 ? (($asistencias + $retardos) / $total_clases) * 100 : 0;



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Asistencia - <?php echo htmlspecialchars($curso['nombre_curso']); ?></title>
    <link rel="stylesheet" href="../cursos/cursos.css">
    <link rel="stylesheet" href="css/asistencia.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <style>
        .asistencia-presente { color: green; }
        .asistencia-ausente { color: red; }
        .asistencia-retardo { color: orange; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Detalle de Asistencia - <?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>
                </div>
                <div class="header-right">
                    <a href="asistencia_estudiante.php" class="btn-back">
                        <span class="material-icons-sharp">arrow_back</span>
                        Volver
                    </a>
                </div>
            </header>

            <section class="content">
                <div class="asistencia-resumen">
                    <h2>Resumen de Asistencia</h2>
                    <p><strong>Total de clases:</strong> <?php echo $total_clases; ?></p>
                    <p><strong>Asistencias:</strong> <?php echo $asistencias; ?></p>
                    <p><strong>Inasistencias:</strong> <?php echo $inasistencias; ?></p>
                    <p><strong>Retardos:</strong> <?php echo $retardos; ?></p>
                    <p><strong>Porcentaje de asistencia:</strong> <?php echo number_format($porcentaje_asistencia, 2); ?>%</p>
                </div>

                <div class="asistencia-detalle">
                    <h2>Registro de Asistencias</h2>
                    <table class="asistencia-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result_asistencias->data_seek(0); // Reiniciar el puntero del resultado
                            while ($asistencia = $result_asistencias->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($asistencia['fecha'])); ?></td>
                                <td>
                                    <?php
                                    switch ($asistencia['estado']) {
                                        case 'presente':
                                            echo '<span class="asistencia-presente">Presente</span>';
                                            break;
                                        case 'ausente':
                                            echo '<span class="asistencia-ausente">Ausente</span>';
                                            break;
                                        case 'retardo':
                                            echo '<span class="asistencia-retardo">Retardo</span>';
                                            break;
                                        default:
                                            echo 'Desconocido';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
