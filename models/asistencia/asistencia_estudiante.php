<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('estudiante');

// Obtener el id_estudiante basado en el id_usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];
$sql_estudiante = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ?";
$stmt_estudiante = $conn->prepare($sql_estudiante);
$stmt_estudiante->bind_param("i", $id_usuario);
$stmt_estudiante->execute();
$result_estudiante = $stmt_estudiante->get_result();
$estudiante = $result_estudiante->fetch_assoc();
$id_estudiante = $estudiante['id_estudiante'];

// Modificar la consulta SQL para obtener más información de los cursos
$sql_cursos = "SELECT c.id_curso, c.nombre_curso, c.descripcion, c.icono, c.nivel_educativo, c.duracion, c.estado,
               cc.nombre_categoria,
               COUNT(DISTINCT a.fecha) as total_clases,
               SUM(CASE WHEN a.presente = 1 THEN 1 ELSE 0 END) as asistencias,
               SUM(CASE WHEN a.presente = 0 THEN 1 ELSE 0 END) as inasistencias
               FROM cursos c
               INNER JOIN inscripciones i ON c.id_curso = i.id_curso
               LEFT JOIN asistencia a ON c.id_curso = a.id_curso AND i.id_estudiante = a.id_estudiante
               LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
               WHERE i.id_estudiante = ?
               GROUP BY c.id_curso";

$stmt_cursos = $conn->prepare($sql_cursos);
$stmt_cursos->bind_param("i", $id_estudiante);
$stmt_cursos->execute();
$result_cursos = $stmt_cursos->get_result();


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Asistencias</title>
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
                    <h1>Mis Asistencias</h1>
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
                                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($curso['descripcion']); ?></p>
                                    <p><strong>Nivel educativo:</strong> <?php echo htmlspecialchars(ucfirst($curso['nivel_educativo'])); ?></p>
                                    <p><strong>Duración:</strong> <?php echo htmlspecialchars($curso['duracion']); ?> semanas</p>
                                    <p><strong>Estado:</strong> <?php echo htmlspecialchars(ucfirst($curso['estado'])); ?></p>
                                    <a href="detalle_asistencia.php?id_curso=<?php echo $curso['id_curso']; ?>" class="btn-detalles">
                                        Ver detalles de asistencia
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <p>No estás inscrito en ningún curso actualmente.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
