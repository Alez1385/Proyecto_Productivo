<?php
require_once 'conexion.php';

echo "<h1>Verificación de Columna perfil_incompleto</h1>";

try {
    // Verificar si la columna existe
    $result = $conn->query("SHOW COLUMNS FROM usuario LIKE 'perfil_incompleto'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ La columna perfil_incompleto existe</p>";
        
        // Mostrar información de la columna
        $column_info = $result->fetch_assoc();
        echo "<p><strong>Tipo:</strong> " . $column_info['Type'] . "</p>";
        echo "<p><strong>Default:</strong> " . $column_info['Default'] . "</p>";
        echo "<p><strong>Null:</strong> " . $column_info['Null'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ La columna perfil_incompleto NO existe</p>";
    }
    
    // Verificar algunos registros
    echo "<h2>Verificación de Registros</h2>";
    $result = $conn->query("SELECT id_usuario, username, nombre, apellido, telefono, direccion, fecha_nac, perfil_incompleto FROM usuario LIMIT 5");
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Nombre</th><th>Apellido</th><th>Teléfono</th><th>Dirección</th><th>Fecha Nac</th><th>Perfil Incompleto</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id_usuario'] . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombre'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['apellido'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['telefono'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['direccion'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['fecha_nac'] ?? 'NULL') . "</td>";
            echo "<td>" . $row['perfil_incompleto'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?> 