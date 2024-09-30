<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos Disponibles</title>
    <link rel="stylesheet" href="css/estudiante.css">
</head>

<body>
    <div class="dashboard-container">
        <?php
        include "../../scripts/sidebar.php";
        include "../../scripts/conexion.php";

        // Obtener el id_estudiante basado en el username
        $username = $_SESSION['username'];
        $query = "SELECT e.id_estudiante FROM estudiante e 
                  INNER JOIN usuario u ON e.id_usuario = u.id_usuario 
                  WHERE u.username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo '<h2 class="no-eres-estudiante">Lo sentimos, no eres un estudiante.</h2>';
            exit;
        }

        $row = $result->fetch_assoc();
        $id_estudiante = $row['id_estudiante'];

        // Obtener los cursos disponibles
        $query_cursos = "SELECT c.*, cc.nombre_categoria, 
                         GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios
                         FROM cursos c
                         LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
                         LEFT JOIN horarios h ON c.id_curso = h.id_curso
                         WHERE c.estado = 'activo' AND c.id_curso NOT IN (
                             SELECT id_curso FROM inscripciones WHERE id_estudiante = ? 
                             AND estado IN ('pendiente', 'aprobado', 'rechazado')
                         )
                         GROUP BY c.id_curso";
        $stmt_cursos = $conn->prepare($query_cursos);
        $stmt_cursos->bind_param("i", $id_estudiante);
        $stmt_cursos->execute();
        $result_cursos = $stmt_cursos->get_result();

        // Procesar la inscripción
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inscribir'])) {
            $id_curso = $_POST['id_curso'];

            $query_inscribir = "INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado) VALUES (?, ?, CURDATE(), 'pendiente')";
            $stmt_inscribir = $conn->prepare($query_inscribir);
            $stmt_inscribir->bind_param("ii", $id_curso, $id_estudiante);

            if ($stmt_inscribir->execute()) {
                $mensaje = "Inscripción realizada con éxito. Espere la aprobación.";
            } else {
                $mensaje = "Error al realizar la inscripción: " . $conn->error;
            }
        }
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <h1>Cursos Disponibles</h1>
            </header>
            <section class="content">
                <?php if (isset($mensaje)) : ?>
                    <div class="alert alert-info"><?php echo $mensaje; ?></div>
                <?php endif; ?>

                <div class="course-list">
                    <?php while ($curso = $result_cursos->fetch_assoc()) : ?>
                        <div class="course-item">
                            <img src="../../uploads/icons/<?php echo $curso['icono']; ?>" alt="<?php echo $curso['nombre_curso']; ?>">
                            <div class="course-content">
                                <div class="course-details">
                                    <h2><?php echo $curso['nombre_curso']; ?></h2>
                                    <p><?php echo $curso['descripcion']; ?></p>
                                    <p><strong>Categoría:</strong> <?php echo $curso['nombre_categoria']; ?></p>
                                    <p><strong>Nivel:</strong> <?php echo $curso['nivel_educativo']; ?></p>
                                    <p><strong>Duración:</strong> <?php echo $curso['duracion']; ?> semanas</p>
                                    <p><strong>Horarios:</strong> <?php echo $curso['horarios']; ?></p>
                                </div>
                                <div class="course-actions">
                                    <form method="POST">
                                        <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                                        <button type="submit" name="inscribir" class="inscribir-btn">Inscribirse</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </div>
    </div>
    <script src="scripts/cursos_disponibles.js"></script>
</body>

</html>