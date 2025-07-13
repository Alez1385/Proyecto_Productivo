<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;

if (!$id_curso) {
    die("ID de curso no válido.");
}

// Consulta SQL simplificada para obtener un solo registro de horario por curso
$sql = "SELECT c.nombre_curso, h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado, 
               CONCAT(u.nombre, ' ', u.apellido) as nombre_profesor
        FROM cursos c
        LEFT JOIN horarios h ON c.id_curso = h.id_curso
        LEFT JOIN profesor p ON h.id_profesor = p.id_profesor
        LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
        WHERE c.id_curso = ?
        ORDER BY h.id_horario DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();
$curso = $result->fetch_assoc();

if (!$curso) {
    die("Curso no encontrado.");
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario del Curso</title>
    <link rel="stylesheet" href="css/horarios_asignados.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <style>
        .horario-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .horario-table th, .horario-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .horario-table th {
            background-color: #f2f2f2;
        }
        .curso-info {
            background-color: #e6f7ff;
            padding: 5px;
            border-radius: 4px;
        }
        .profesor-info {
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Horario: <?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>
                </div>
                <div class="header-right">
                    <button onclick="window.location.href='cursos_listado.php'" class="btn-back">Volver a Mis Cursos</button>
                </div>
            </header>

            <section class="content">
                <table class="horario-table">
                    <tr>
                        <th>Hora</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                        <th>Sábado</th>
                    </tr>
                    <?php
                    $horas = [];
                    for ($i = 6; $i <= 20; $i++) {
                        $horas[] = sprintf("%02d:00", $i);
                    }
                    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

                    foreach ($horas as $hora) {
                        echo "<tr>";
                        echo "<td>" . date("h:i A", strtotime($hora)) . "</td>";
                        foreach ($dias as $dia) {
                            echo "<td>";
                            if (!empty($curso[$dia])) {
                                list($inicio, $fin) = explode(' - ', $curso[$dia]);
                                $hora_actual = strtotime($hora);
                                $hora_siguiente = strtotime("+1 hour", $hora_actual);
                                $hora_inicio = strtotime($inicio);
                                $hora_fin = strtotime($fin);
                                
                                if (($hora_actual >= $hora_inicio && $hora_actual < $hora_fin) ||
                                    ($hora_siguiente > $hora_inicio && $hora_siguiente <= $hora_fin) ||
                                    ($hora_actual <= $hora_inicio && $hora_siguiente >= $hora_fin)) {
                                    echo "<div class='curso-info'>";
                                    echo htmlspecialchars($curso['nombre_curso']) . "<br>";
                                    echo "<p class='profesor-info'>Profesor: " . htmlspecialchars($curso['nombre_profesor']) . "</p>";
                                    echo "<p class='horario-info'>" . date("h:i A", $hora_inicio) . " - " . date("h:i A", $hora_fin) . "</p>";
                                    echo "</div>";
                                }
                            }
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </table>
            </section>
        </div>
    </div>
</body>
</html>
