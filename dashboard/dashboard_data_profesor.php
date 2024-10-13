<?php
// Desactivar la salida del búfer
ob_start();

// Incluir archivos necesarios
require_once '../scripts/config.php';
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Preparar la respuesta
$response = [];

// Verificar si el usuario es un profesor
if (!isset($_SESSION['username']) || !checkPermission('profesor')) {
    $response = [
        'error' => true,
        'message' => 'No tienes permiso para acceder a esta información.'
    ];
} else {
    try {
        $username = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT p.id_profesor FROM profesor p 
                                INNER JOIN usuario u ON p.id_usuario = u.id_usuario 
                                WHERE u.username = ?");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
        }
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('No se encontró el profesor en la base de datos.');
        }

        $row = $result->fetch_assoc();
        $id_profesor = $row['id_profesor'];

        // Obtener cursos asignados al profesor
        $cursos = getDatabaseData($conn, "
            SELECT c.*, cc.nombre_categoria,
            (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso) as num_estudiantes,
            GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios
            FROM cursos c
            LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
            LEFT JOIN horarios h ON c.id_curso = h.id_curso
            LEFT JOIN asignacion_curso ac ON c.id_curso = ac.id_curso
            WHERE ac.id_profesor = ? OR c.id_profesor = ?
            GROUP BY c.id_curso
        ", [$id_profesor, $id_profesor]);

        $response['cursos'] = $cursos;
        $response['id_profesor'] = $id_profesor; // Añadimos esto para depuración

    } catch (Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Ha ocurrido un error al procesar tu solicitud.',
            'details' => $e->getMessage()
        ];
    }
}

// Limpiar cualquier salida anterior
ob_end_clean();

// Establecer el encabezado de contenido JSON
header('Content-Type: application/json');

// Agregar log para depuración
error_log("Respuesta JSON para el profesor: " . json_encode($response));

// Enviar la respuesta JSON
echo json_encode($response);
