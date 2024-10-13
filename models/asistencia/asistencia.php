<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('profesor');

$id_profesor = $_SESSION['id_usuario'];

// Obtener los cursos asignados al profesor con información adicional
$sql_cursos = "SELECT c.id_curso, c.nombre_curso, c.descripcion, c.icono, c.nivel_educativo, c.duracion,
               GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios,
               COUNT(DISTINCT i.id_estudiante) AS num_estudiantes
               FROM cursos c
               INNER JOIN asignacion_curso ac ON c.id_curso = ac.id_curso
               INNER JOIN profesor p ON ac.id_profesor = p.id_profesor
               LEFT JOIN horarios h ON c.id_curso = h.id_curso
               LEFT JOIN inscripciones i ON c.id_curso = i.id_curso
               WHERE p.id_usuario = ?
               GROUP BY c.id_curso";
$stmt_cursos = $conn->prepare($sql_cursos);
$stmt_cursos->bind_param("i", $id_profesor);
$stmt_cursos->execute();
$result_cursos = $stmt_cursos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asistencias</title>
    <link rel="stylesheet" href="../cursos/cursos.css">
    <link rel="stylesheet" href="css/asistencia.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Gestión de Asistencias</h1>
                </div>
            </header>

            <section class="content">
                <div class="course-list">
                    <?php 
                    if ($result_cursos->num_rows > 0):
                        while ($curso = $result_cursos->fetch_assoc()): 
                    ?>
                        <div class="course-item">
                            <img src="<?php echo !empty($curso['icono']) ? '../../uploads/icons/' . $curso['icono'] : '../../img/curso_default.png'; ?>" alt="Icono del curso">
                            <div class="course-content">
                                <div class="course-details">
                                    <h2><?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
                                    <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                                    <p><strong>Nivel: </strong><?php echo htmlspecialchars($curso['nivel_educativo']); ?></p>
                                    <p><strong>Duración: </strong><?php echo htmlspecialchars($curso['duracion']); ?> semanas</p>
                                    <p><strong>Horarios: </strong><?php echo htmlspecialchars($curso['horarios']); ?></p>
                                    <p><strong>Estudiantes inscritos: </strong><?php echo $curso['num_estudiantes']; ?></p>
                                </div>
                            </div>
                            <div class="course-actions">
                                <a href="registrar_asistencia.php?id_curso=<?php echo $curso['id_curso']; ?>" class="btn-registrar">
                                    <span class="material-icons-sharp">how_to_reg</span>
                                    Registrar Asistencia
                                </a>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <p>No tienes cursos asignados actualmente.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
