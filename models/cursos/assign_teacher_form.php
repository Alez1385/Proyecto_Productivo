<?php
require_once '../../scripts/conexion.php';
require_once '../../scripts/auth.php';
requireLogin();
checkPermission('admin');

$courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;

if ($courseId <= 0) {
    echo "ID de curso inválido.";
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el profesor actual del curso
$sql_current_teacher = "SELECT ac.id_profesor, u.nombre, u.apellido 
                        FROM asignacion_curso ac 
                        JOIN profesor p ON ac.id_profesor = p.id_profesor 
                        JOIN usuario u ON p.id_usuario = u.id_usuario 
                        WHERE ac.id_curso = ? AND ac.estado = 'activo'";
$stmt_current = $conn->prepare($sql_current_teacher);
$stmt_current->bind_param("i", $courseId);
$stmt_current->execute();
$result_current = $stmt_current->get_result();
$current_teacher = $result_current->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_teacher ? 'Cambiar' : 'Asignar'; ?> Profesor</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="container">
        <h2><?php echo $current_teacher ? 'Cambiar' : 'Asignar'; ?> Profesor al Curso</h2>
        <form id="assignTeacherForm">
            <input type="hidden" id="courseId" name="courseId" value="<?php echo $courseId; ?>">
            <select id="teacherSelect" name="teacherId" required>
                <option value="">Seleccione un profesor</option>
                <?php
                $sql_teachers = "SELECT p.id_profesor, u.nombre, u.apellido 
                                 FROM profesor p 
                                 JOIN usuario u ON p.id_usuario = u.id_usuario";
                $result_teachers = $conn->query($sql_teachers);
                if ($result_teachers->num_rows > 0) {
                    while ($teacher = $result_teachers->fetch_assoc()) {
                        $selected = ($current_teacher && $current_teacher['id_profesor'] == $teacher['id_profesor']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($teacher['id_profesor']) . "' $selected>" 
                             . htmlspecialchars($teacher['nombre'] . " " . $teacher['apellido']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay profesores disponibles</option>";
                }
                ?>
            </select>
            <button type="submit"><?php echo $current_teacher ? 'Cambiar' : 'Asignar'; ?> Profesor</button>
        </form>
    </div>
    <script src="scripts/assign_teacher.js"></script>
</body>
</html>
<?php
$conn->close();
?>
