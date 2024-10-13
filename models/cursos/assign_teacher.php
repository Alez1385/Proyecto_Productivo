<?php
require_once '../../scripts/conexion.php';
require_once '../../scripts/auth.php';
requireLogin();
checkPermission('admin');

header('Content-Type: application/json');

error_log("Iniciando assign_teacher.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseId = isset($_POST['courseId']) ? intval($_POST['courseId']) : 0;
    $teacherId = isset($_POST['teacherId']) ? intval($_POST['teacherId']) : 0;

    error_log("Datos recibidos - CourseId: $courseId, TeacherId: $teacherId");

    if ($courseId <= 0 || $teacherId <= 0) {
        error_log("Datos inválidos proporcionados");
        echo json_encode([
            'success' => false,
            'message' => 'Datos inválidos proporcionados.'
        ]);
        exit;
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        error_log("Error de conexión a la base de datos: " . $conn->connect_error);
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos: ' . $conn->connect_error
        ]);
        exit;
    }

    // Insertar en asignacion_curso
    $sql_insert = "INSERT INTO asignacion_curso (id_curso, id_profesor, fecha_asignacion, estado) VALUES (?, ?, CURDATE(), 'activo')";
    $stmt_insert = $conn->prepare($sql_insert);
    
    if (!$stmt_insert) {
        error_log("Error en la preparación de la consulta: " . $conn->error);
        echo json_encode([
            'success' => false,
            'message' => 'Error en la preparación de la consulta: ' . $conn->error
        ]);
        exit;
    }

    $stmt_insert->bind_param("ii", $courseId, $teacherId);

    error_log("Ejecutando consulta: $sql_insert con courseId=$courseId y teacherId=$teacherId");

    if ($stmt_insert->execute()) {
        error_log("Inserción exitosa. ID de inserción: " . $stmt_insert->insert_id);
        
        // Obtener el nombre del profesor
        $sql_teacher = "SELECT u.nombre, u.apellido FROM usuario u JOIN profesor p ON u.id_usuario = p.id_usuario WHERE p.id_profesor = ?";
        $stmt_teacher = $conn->prepare($sql_teacher);
        $stmt_teacher->bind_param("i", $teacherId);
        $stmt_teacher->execute();
        $result_teacher = $stmt_teacher->get_result();
        $teacher = $result_teacher->fetch_assoc();

        error_log("Profesor encontrado: " . json_encode($teacher));

        echo json_encode([
            'success' => true,
            'teacherName' => $teacher['nombre'] . ' ' . $teacher['apellido']
        ]);
    } else {
        error_log("Error al insertar: " . $stmt_insert->error);
        echo json_encode([
            'success' => false,
            'message' => 'Error al asignar el profesor: ' . $stmt_insert->error
        ]);
    }

    $stmt_insert->close();
    $conn->close();
} else {
    error_log("Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
}
?>
