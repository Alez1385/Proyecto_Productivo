<?php
require_once "../../scripts/conexion.php";
require_once "../../scripts/config.php";

session_start();

// Verificar si el usuario está logueado y es un estudiante
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'estudiante') {
    header("Location: login.php");
    exit();
}

$id_estudiante = $_SESSION['id_estudiante'];

// Obtener cursos disponibles
function getCursosDisponibles($conn) {
    $sql = "SELECT id_curso, nombre_curso, descripcion FROM cursos WHERE estado = 'activo'";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching courses: " . $conn->error);
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Procesar la inscripción
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_curso = $_POST['id_curso'];
    
    // Verificar si ya está inscrito
    $stmt = $conn->prepare("SELECT id_inscripcion FROM inscripciones WHERE id_estudiante = ? AND id_curso = ?");
    $stmt->bind_param("ii", $id_estudiante, $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $mensaje = "Ya estás inscrito en este curso.";
    } else {
        // Realizar la inscripción
        $stmt = $conn->prepare("INSERT INTO inscripciones (id_estudiante, id_curso, fecha_inscripcion, estado) VALUES (?, ?, CURDATE(), 'pendiente')");
        $stmt->bind_param("ii", $id_estudiante, $id_curso);
        
        if ($stmt->execute()) {
            $mensaje = "Inscripción realizada con éxito. Estado: pendiente de aprobación.";
        } else {
            $mensaje = "Error al realizar la inscripción: " . $conn->error;
        }
    }
}

try {
    $cursos = getCursosDisponibles($conn);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción a Cursos</title>
    <link rel="stylesheet" href="css/inscripcion.css">
</head>
<body>
    <div class="container">
        <h1>Inscripción a Cursos</h1>
        
        <?php if (isset($mensaje)): ?>
            <div class="mensaje"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="id_curso">Selecciona un curso:</label>
            <select name="id_curso" id="id_curso" required>
                <option value="">-- Selecciona un curso --</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo htmlspecialchars($curso['id_curso']); ?>">
                        <?php echo htmlspecialchars($curso['nombre_curso']); ?> - <?php echo htmlspecialchars($curso['descripcion']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Inscribirse</button>
        </form>
    </div>
</body>
</html>