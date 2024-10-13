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
$sql_estudiantes = "SELECT DISTINCT e.id_estudiante, u.nombre, u.apellido
                    FROM estudiante e
                    INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                    INNER JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
                    WHERE i.id_curso = ?
                    ORDER BY u.apellido, u.nombre";
$stmt_estudiantes = $conn->prepare($sql_estudiantes);
$stmt_estudiantes->bind_param("i", $id_curso);
$stmt_estudiantes->execute();
$result_estudiantes = $stmt_estudiantes->get_result();

// Procesar el formulario de asistencia
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asistencias = $_POST['asistencia'] ?? [];
    foreach ($result_estudiantes as $estudiante) {
        $id_estudiante = $estudiante['id_estudiante'];
        $estado = $asistencias[$id_estudiante] ?? 'ausente';
        $sql_insert = "INSERT INTO asistencia (id_estudiante, id_curso, fecha, estado) 
                       VALUES (?, ?, ?, ?) 
                       ON DUPLICATE KEY UPDATE estado = ?";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iisss", $id_estudiante, $id_curso, $fecha, $estado, $estado);
        $stmt_insert->execute();
    }
    $_SESSION['mensaje'] = "Asistencia registrada exitosamente.";
    header("Location: registrar_asistencia.php?id_curso=$id_curso");
    exit();
}

// Modificar la consulta SQL para obtener asistencias previas
$sql_asistencias = "SELECT id_estudiante, estado FROM asistencia WHERE id_curso = ? AND fecha = ?";
$stmt_asistencias = $conn->prepare($sql_asistencias);
$stmt_asistencias->bind_param("is", $id_curso, $fecha);
$stmt_asistencias->execute();
$result_asistencias = $stmt_asistencias->get_result();
$asistencias_previas = [];
while ($row = $result_asistencias->fetch_assoc()) {
    $asistencias_previas[$row['id_estudiante']] = $row['estado'];
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
    <style>
        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .estudiantes-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
        }
        .estudiante-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .estudiante-item:last-child {
            border-bottom: none;
        }
        .estudiante-item span {
            flex-grow: 1;
        }
        .asistencia-options {
            display: flex;
            gap: 10px;
        }
        .asistencia-option {
            display: flex;
            align-items: center;
        }
        .asistencia-option input[type="radio"] {
            margin-right: 5px;
        }
        /* Estilo para el checkbox personalizado */
        .custom-checkbox {
            width: 20px;
            height: 20px;
            background-color: #fff;
            border: 2px solid #4CAF50;
            border-radius: 3px;
            display: inline-block;
            position: relative;
            cursor: pointer;
        }
        .custom-checkbox::after {
            content: '\2714';
            font-size: 14px;
            color: #4CAF50;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
        }
        input[type="checkbox"]:checked + .custom-checkbox::after {
            display: block;
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
        .mensaje {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .exito {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
    </style>
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
                    <a href="asistencia.php" class="btn-back">
                        <span class="material-icons-sharp">arrow_back</span>
                        Volver
                    </a>
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
                                <span><?php echo htmlspecialchars($estudiante['apellido'] . ', ' . $estudiante['nombre']); ?></span>
                                <div class="asistencia-options">
                                    <label class="asistencia-option">
                                        <input type="radio" 
                                               name="asistencia[<?php echo $estudiante['id_estudiante']; ?>]" 
                                               value="presente"
                                               <?php echo (isset($asistencias_previas[$estudiante['id_estudiante']]) && $asistencias_previas[$estudiante['id_estudiante']] == 'presente') ? 'checked' : ''; ?>>
                                        Presente
                                    </label>
                                    <label class="asistencia-option">
                                        <input type="radio" 
                                               name="asistencia[<?php echo $estudiante['id_estudiante']; ?>]" 
                                               value="ausente"
                                               <?php echo (isset($asistencias_previas[$estudiante['id_estudiante']]) && $asistencias_previas[$estudiante['id_estudiante']] == 'ausente') ? 'checked' : ''; ?>>
                                        Ausente
                                    </label>
                                         <label class="asistencia-option">
                                        <input type="radio" 
                                               name="asistencia[<?php echo $estudiante['id_estudiante']; ?>]" 
                                               value="retardo"
                                               <?php echo (isset($asistencias_previas[$estudiante['id_estudiante']]) && $asistencias_previas[$estudiante['id_estudiante']] == 'retardo') ? 'checked' : ''; ?>>
                                        Retardo
                                    </label>
                                </div>
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
