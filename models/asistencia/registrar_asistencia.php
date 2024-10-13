<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('profesor');

$id_curso = $_GET['id_curso'] ?? null;
$fecha = $_POST['fecha'] ?? date('Y-m-d');

if (!$id_curso) {
    die("ID de curso no proporcionado");
}

// Obtener información del curso
$sql_curso = "SELECT nombre_curso FROM cursos WHERE id_curso = ?";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $id_curso);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();
$curso = $result_curso->fetch_assoc();

// Obtener estudiantes del curso
$sql_estudiantes = "SELECT e.id_estudiante, u.nombre, u.apellido
                    FROM estudiante e
                    INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                    INNER JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
                    WHERE i.id_curso = ?";
$stmt_estudiantes = $conn->prepare($sql_estudiantes);
$stmt_estudiantes->bind_param("i", $id_curso);
$stmt_estudiantes->execute();
$result_estudiantes = $stmt_estudiantes->get_result();

// Procesar el formulario de asistencia
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asistencias = $_POST['asistencia'] ?? [];
    foreach ($asistencias as $id_estudiante => $presente) {
        $sql_insert = "INSERT INTO asistencia (id_estudiante, id_curso, fecha, presente) 
                       VALUES (?, ?, ?, ?) 
                       ON DUPLICATE KEY UPDATE presente = ?";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iisss", $id_estudiante, $id_curso, $fecha, $presente, $presente);
        $stmt_insert->execute();
    }
    $_SESSION['mensaje'] = "Asistencia registrada exitosamente.";
    header("Location: registrar_asistencia.php?id_curso=$id_curso");
    exit();
}

// Obtener asistencias previas para la fecha seleccionada
$sql_asistencias = "SELECT id_estudiante, presente FROM asistencia WHERE id_curso = ? AND fecha = ?";
$stmt_asistencias = $conn->prepare($sql_asistencias);
$stmt_asistencias->bind_param("is", $id_curso, $fecha);
$stmt_asistencias->execute();
$result_asistencias = $stmt_asistencias->get_result();
$asistencias_previas = [];
while ($row = $result_asistencias->fetch_assoc()) {
    $asistencias_previas[$row['id_estudiante']] = $row['presente'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Asistencia - <?php echo htmlspecialchars($curso['nombre_curso']); ?></title>
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
                    <h1>Registrar Asistencia - <?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>
                </div>
                <div class="header-right">
                    <a href="asistencia.php" class="btn-back">Volver</a>
                </div>
            </header>

            <section class="content">
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="mensaje exito"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="fecha">Fecha:</label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" required>
                    </div>

                    <div class="estudiantes-list">
                        <?php while ($estudiante = $result_estudiantes->fetch_assoc()): ?>
                            <div class="estudiante-item">
                                <span><?php echo htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido']); ?></span>
                                <select name="asistencia[<?php echo $estudiante['id_estudiante']; ?>]">
                                    <option value="si" <?php echo (isset($asistencias_previas[$estudiante['id_estudiante']]) && $asistencias_previas[$estudiante['id_estudiante']] == 'si') ? 'selected' : ''; ?>>Presente</option>
                                    <option value="no" <?php echo (isset($asistencias_previas[$estudiante['id_estudiante']]) && $asistencias_previas[$estudiante['id_estudiante']] == 'no') ? 'selected' : ''; ?>>Ausente</option>
                                </select>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <button type="submit" class="btn-submit">Guardar Asistencia</button>
                </form>
            </section>
        </div>
    </div>
</body>
</html>