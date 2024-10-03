<?php
// dashboard_estudiante.php

// Verificar permisos de estudiante
checkPermission('estudiante');

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

// Obtener las inscripciones del estudiante
$inscripciones = getDatabaseData($conn, "
    SELECT i.*, c.nombre_curso
    FROM inscripciones i
    INNER JOIN cursos c ON i.id_curso = c.id_curso
    WHERE i.id_estudiante = $id_estudiante
");

// Obtener las preinscripciones del estudiante
$preinscripciones = getDatabaseData($conn, "
    SELECT p.*, c.nombre_curso
    FROM preinscripciones p
    INNER JOIN cursos c ON p.id_curso = c.id_curso
    WHERE p.id_usuario = (SELECT id_usuario FROM estudiante WHERE id_estudiante = $id_estudiante)
");

// Obtener los cursos disponibles
$cursos_disponibles = getDatabaseData($conn, "
    SELECT c.*, cc.nombre_categoria, 
    GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios
    FROM cursos c
    LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
    LEFT JOIN horarios h ON c.id_curso = h.id_curso
    WHERE c.estado = 'activo' AND c.id_curso NOT IN (
        SELECT id_curso FROM inscripciones WHERE id_estudiante = $id_estudiante 
        AND estado IN ('pendiente', 'aprobado', 'rechazado')
    )
    GROUP BY c.id_curso
");
?>

<section class="student-dashboard">
    <h2>Mis Inscripciones</h2>
    <div class="inscripciones-list">
        <?php if (count($inscripciones) > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Fecha de Inscripción</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscripciones as $inscripcion) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inscripcion['nombre_curso']); ?></td>
                            <td><?php echo htmlspecialchars($inscripcion['fecha_inscripcion']); ?></td>
                            <td><?php echo htmlspecialchars($inscripcion['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No tienes inscripciones activas.</p>
        <?php endif; ?>
    </div>

    <h2>Mis Preinscripciones</h2>
    <div class="preinscripciones-list">
        <?php if (count($preinscripciones) > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Fecha de Preinscripción</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preinscripciones as $preinscripcion) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($preinscripcion['nombre_curso']); ?></td>
                            <td><?php echo htmlspecialchars($preinscripcion['fecha_preinscripcion']); ?></td>
                            <td><?php echo htmlspecialchars($preinscripcion['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No tienes preinscripciones pendientes.</p>
        <?php endif; ?>
    </div>

    <h2>Cursos Disponibles</h2>
    <div class="course-list">
        <?php foreach ($cursos_disponibles as $curso) : ?>
            <div class="course-item">
                <img src="../../uploads/icons/<?php echo htmlspecialchars($curso['icono']); ?>" alt="<?php echo htmlspecialchars($curso['nombre_curso']); ?>">
                <div class="course-content">
                    <div class="course-details">
                        <h3><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                        <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($curso['nombre_categoria']); ?></p>
                        <p><strong>Nivel:</strong> <?php echo htmlspecialchars($curso['nivel_educativo']); ?></p>
                        <p><strong>Duración:</strong> <?php echo htmlspecialchars($curso['duracion']); ?> semanas</p>
                        <p><strong>Horarios:</strong> <?php echo htmlspecialchars($curso['horarios']); ?></p>
                    </div>
                    <div class="course-actions">
                        <form method="POST">
                            <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                            <button type="submit" name="inscribir" class="inscribir-btn">Inscribirse</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script src="js/cursos_disponibles.js"></script>