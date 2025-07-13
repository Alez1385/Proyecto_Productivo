<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

// Verificar que sea admin
if (!checkPermission('admin', false)) {
    die("Acceso denegado. Solo administradores pueden ejecutar esta función.");
}

echo "<h2>Limpieza de Horarios Duplicados</h2>";

// Obtener todos los cursos que tienen horarios
$sql_cursos = "SELECT DISTINCT id_curso FROM horarios";
$result_cursos = $conn->query($sql_cursos);

if ($result_cursos && $result_cursos->num_rows > 0) {
    while ($row = $result_cursos->fetch_assoc()) {
        $id_curso = $row['id_curso'];
        
        // Obtener todos los horarios para este curso
        $sql_horarios = "SELECT id_horario, fecha_creacion FROM horarios WHERE id_curso = ? ORDER BY fecha_creacion DESC";
        $stmt_horarios = $conn->prepare($sql_horarios);
        $stmt_horarios->bind_param("i", $id_curso);
        $stmt_horarios->execute();
        $result_horarios = $stmt_horarios->get_result();
        
        $horarios = [];
        while ($horario = $result_horarios->fetch_assoc()) {
            $horarios[] = $horario;
        }
        
        // Si hay más de un horario, mantener solo el más reciente
        if (count($horarios) > 1) {
            $horario_mantener = $horarios[0]; // El más reciente (ordenado DESC)
            
            echo "<p>Curso ID: $id_curso - Encontrados " . count($horarios) . " horarios</p>";
            
            // Eliminar los horarios duplicados (todos excepto el más reciente)
            for ($i = 1; $i < count($horarios); $i++) {
                $id_horario_eliminar = $horarios[$i]['id_horario'];
                $sql_eliminar = "DELETE FROM horarios WHERE id_horario = ?";
                $stmt_eliminar = $conn->prepare($sql_eliminar);
                $stmt_eliminar->bind_param("i", $id_horario_eliminar);
                
                if ($stmt_eliminar->execute()) {
                    echo "<p style='color: green;'>✓ Eliminado horario ID: $id_horario_eliminar</p>";
                } else {
                    echo "<p style='color: red;'>✗ Error eliminando horario ID: $id_horario_eliminar</p>";
                }
            }
            
            echo "<p style='color: blue;'>✓ Mantenido horario ID: " . $horario_mantener['id_horario'] . " (más reciente)</p>";
        } else {
            echo "<p>Curso ID: $id_curso - Sin duplicados</p>";
        }
    }
} else {
    echo "<p>No se encontraron horarios en la base de datos.</p>";
}

echo "<br><p><strong>Limpieza completada.</strong></p>";
echo "<p><a href='cursos_listado.php'>Volver a Cursos</a></p>";
?> 