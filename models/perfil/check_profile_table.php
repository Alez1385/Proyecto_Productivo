<?php
include "../../scripts/conexion.php";

echo "<h1>Verificación de Estructura de Tabla Usuario</h1>";

try {
    // Verificar la estructura completa de la tabla usuario
    echo "<h2>Estructura de la Tabla Usuario</h2>";
    $result = $conn->query("DESCRIBE usuario");
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Verificar específicamente la columna perfil_incompleto
    echo "<h2>Verificación Específica de perfil_incompleto</h2>";
    $result = $conn->query("SHOW COLUMNS FROM usuario LIKE 'perfil_incompleto'");
    
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ La columna perfil_incompleto existe</p>";
        $column_info = $result->fetch_assoc();
        echo "<p><strong>Tipo:</strong> " . $column_info['Type'] . "</p>";
        echo "<p><strong>Default:</strong> " . $column_info['Default'] . "</p>";
        echo "<p><strong>Null:</strong> " . $column_info['Null'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ La columna perfil_incompleto NO existe</p>";
        echo "<p>Ejecutando script de reparación...</p>";
        
        // Agregar la columna si no existe
        $sql = "ALTER TABLE usuario ADD COLUMN perfil_incompleto TINYINT(1) DEFAULT 0";
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✓ Columna perfil_incompleto agregada exitosamente</p>";
            
            // Actualizar registros existentes
            $update_sql = "UPDATE usuario SET perfil_incompleto = 1 WHERE nombre IS NULL OR apellido IS NULL OR telefono IS NULL OR direccion IS NULL OR fecha_nac IS NULL";
            if ($conn->query($update_sql)) {
                echo "<p style='color: green;'>✓ Registros actualizados según campos faltantes</p>";
            } else {
                echo "<p style='color: red;'>✗ Error actualizando registros: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Error agregando columna: " . $conn->error . "</p>";
        }
    }
    
    // Verificar algunos registros de usuario
    echo "<h2>Muestra de Registros de Usuario</h2>";
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
    
    echo "<h2>Estado Final</h2>";
    echo "<p style='color: green;'>✓ Verificación completada. La tabla usuario está lista para actualizaciones de perfil.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?> 