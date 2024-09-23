<?php
include_once "../../../scripts/conexion.php";

function getInscripciones(mysqli $conn, array $filtros = []): array
{
    $sql = "SELECT i.id_inscripcion, i.fecha_inscripcion, i.estado,
                    c.nombre_curso, e.id_estudiante, u.nombre, u.apellido
                    FROM inscripciones i
                    JOIN cursos c ON i.id_curso = c.id_curso
                    JOIN estudiante e ON i.id_estudiante = e.id_estudiante
                    JOIN usuario u ON e.id_usuario = u.id_usuario";

            $whereConditions = [];
            $params = [];
            $types = '';

            foreach ($filtros as $key => $value) {
                $whereConditions[] = "$key = ?";
                $params[] = $value;
                $types .= getParamType($value);
            }

            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }

            $sql .= " ORDER BY i.fecha_inscripcion DESC";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparing query: " . $conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error executing query: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $inscripciones = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();

            return $inscripciones;
        }

        function getParamType($value): string
        {
            if (is_int($value)) {
                return 'i';
            } elseif (is_float($value)) {
                return 'd';
            } elseif (is_string($value)) {
                return 's';
            } else {
                return 'b';
            }
        }

        
$filtros = [];
if (isset($_GET['estado']) && $_GET['estado'] !== '') {
    $filtros['i.estado'] = $_GET['estado'];
}
if (isset($_GET['curso']) && $_GET['curso'] !== '') {
    $filtros['c.id_curso'] = $_GET['curso'];
}

try {
    $inscripciones = getInscripciones($conn, $filtros);
    echo json_encode($inscripciones);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}