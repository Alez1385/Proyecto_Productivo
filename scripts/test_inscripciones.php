<?php
// Script de prueba para verificar inscripciones
require_once "conexion.php";

echo "<h2>Verificación de Inscripciones</h2>";

// Verificar inscripciones en la base de datos
$sql = "SELECT i.id_inscripcion, i.fecha_inscripcion, i.estado,
                c.nombre_curso, e.id_estudiante, u.nombre, u.apellido, u.id_tipo_usuario
                FROM inscripciones i
                JOIN cursos c ON i.id_curso = c.id_curso
                JOIN estudiante e ON i.id_estudiante = e.id_estudiante
                JOIN usuario u ON e.id_usuario = u.id_usuario
                ORDER BY i.fecha_inscripcion DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<h3>Inscripciones encontradas: " . $result->num_rows . "</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Estudiante</th><th>Curso</th><th>Fecha</th><th>Estado</th><th>Tipo Usuario</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_inscripcion'] . "</td>";
        echo "<td>" . $row['nombre'] . " " . $row['apellido'] . "</td>";
        echo "<td>" . $row['nombre_curso'] . "</td>";
        echo "<td>" . $row['fecha_inscripcion'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "<td>" . $row['id_tipo_usuario'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No se encontraron inscripciones.</p>";
}

// Verificar preinscripciones
echo "<h3>Preinscripciones</h3>";
$sql_pre = "SELECT p.*, c.nombre_curso, u.username, u.id_tipo_usuario 
             FROM preinscripciones p 
             INNER JOIN cursos c ON p.id_curso = c.id_curso 
             LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
             ORDER BY p.fecha_preinscripcion DESC";

$result_pre = $conn->query($sql_pre);

if ($result_pre && $result_pre->num_rows > 0) {
    echo "<p>Preinscripciones encontradas: " . $result_pre->num_rows . "</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Curso</th><th>Estado</th><th>Usuario</th><th>Tipo</th></tr>";
    
    while ($row = $result_pre->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_preinscripcion'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['nombre_curso'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "<td>" . ($row['username'] ?? 'No registrado') . "</td>";
        echo "<td>" . ($row['id_tipo_usuario'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No se encontraron preinscripciones.</p>";
}

$conn->close();
?> 