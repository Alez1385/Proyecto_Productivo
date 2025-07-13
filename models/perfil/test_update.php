<?php
include "../../scripts/conexion.php";
include "../../scripts/auth.php";

// Habilitar logging de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Prueba de Actualización de Perfil</h1>";

// Simular datos de prueba
$test_data = [
    'nombre' => 'Test',
    'apellido' => 'User',
    'mail' => 'test@example.com',
    'telefono' => '123456789',
    'direccion' => 'Test Address',
    'fecha_nac' => '1990-01-01'
];

echo "<h2>Datos de Prueba</h2>";
echo "<pre>" . print_r($test_data, true) . "</pre>";

try {
    // Verificar si la columna perfil_incompleto existe
    $result = $conn->query("SHOW COLUMNS FROM usuario LIKE 'perfil_incompleto'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>✗ La columna perfil_incompleto NO existe</p>";
        echo "<p>Agregando columna...</p>";
        
        $sql = "ALTER TABLE usuario ADD COLUMN perfil_incompleto TINYINT(1) DEFAULT 0";
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✓ Columna agregada exitosamente</p>";
        } else {
            echo "<p style='color: red;'>✗ Error agregando columna: " . $conn->error . "</p>";
            exit;
        }
    } else {
        echo "<p style='color: green;'>✓ La columna perfil_incompleto existe</p>";
    }
    
    // Obtener un usuario de prueba
    $result = $conn->query("SELECT id_usuario FROM usuario LIMIT 1");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>✗ No hay usuarios en la base de datos</p>";
        exit;
    }
    
    $user = $result->fetch_assoc();
    $id_usuario = $user['id_usuario'];
    echo "<p><strong>Usuario de prueba ID:</strong> " . $id_usuario . "</p>";
    
    // Probar la consulta de actualización
    echo "<h2>Probando Consulta de Actualización</h2>";
    
    $query = "UPDATE usuario SET nombre = ?, apellido = ?, mail = ?, telefono = ?, direccion = ?, fecha_nac = ?, perfil_incompleto = ? WHERE id_usuario = ?";
    echo "<p><strong>Query:</strong> " . $query . "</p>";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<p style='color: red;'>✗ Error preparando statement: " . $conn->error . "</p>";
        exit;
    }
    
    $perfil_completo = !empty($test_data['nombre']) && !empty($test_data['apellido']) && 
                       !empty($test_data['telefono']) && !empty($test_data['direccion']) && 
                       !empty($test_data['fecha_nac']);
    $perfil_incompleto = $perfil_completo ? 0 : 1;
    
    echo "<p><strong>Perfil completo:</strong> " . ($perfil_completo ? 'Sí' : 'No') . "</p>";
    echo "<p><strong>Perfil incompleto:</strong> " . $perfil_incompleto . "</p>";
    
    $bind_result = $stmt->bind_param("ssssssii", 
        $test_data['nombre'], 
        $test_data['apellido'], 
        $test_data['mail'], 
        $test_data['telefono'], 
        $test_data['direccion'], 
        $test_data['fecha_nac'], 
        $perfil_incompleto, 
        $id_usuario
    );
    
    if (!$bind_result) {
        echo "<p style='color: red;'>✗ Error en bind_param: " . $stmt->error . "</p>";
        exit;
    }
    
    $execute_result = $stmt->execute();
    if ($execute_result) {
        echo "<p style='color: green;'>✓ Consulta ejecutada exitosamente</p>";
        echo "<p><strong>Filas afectadas:</strong> " . $stmt->affected_rows . "</p>";
        
        // Verificar el resultado
        $result = $conn->query("SELECT nombre, apellido, mail, telefono, direccion, fecha_nac, perfil_incompleto FROM usuario WHERE id_usuario = " . $id_usuario);
        $updated_user = $result->fetch_assoc();
        
        echo "<h2>Usuario Actualizado</h2>";
        echo "<pre>" . print_r($updated_user, true) . "</pre>";
        
    } else {
        echo "<p style='color: red;'>✗ Error ejecutando statement: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Excepción: " . $e->getMessage() . "</p>";
}

echo "<h2>Estado Final</h2>";
echo "<p style='color: green;'>✓ Prueba completada</p>";
?> 