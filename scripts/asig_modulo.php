<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "conexion.php";
    
    $count_success = 0;

    // Iterar a través de los módulos seleccionados
    foreach ($_POST['id_modulo'] as $key => $value) {
        // Obtener los datos del formulario
        $id_modulo = $_POST['id_modulo'][$key];
        $nombre_modulo = $_POST['nom_modulo'][$key];
        $descripcion_modulo = $_POST['desc_modulo'][$key];
        $id_usuario = $_POST['user'];

        // Verificar si la asignación ya existe
        $sql_check = "SELECT COUNT(*) FROM asig_modulo WHERE id_modulo = ? AND id_usuario = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $id_modulo, $id_usuario);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count == 0) {
            // Consulta de inserción
            $sql_insert = "INSERT INTO asig_modulo (id_modulo, id_usuario) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ii", $id_modulo, $id_usuario);

            // Ejecutar la consulta y verificar si se realizó correctamente
            if ($stmt_insert->execute()) {
                $count_success++;
            }
            $stmt_insert->close();
        
    }

    // Cerrar la conexión
    $conn->close();

    // Mostrar la alerta y redirigir
    echo "<script>
            alert('Se han asignado $count_success módulos correctamente.');
            window.location.href = '../datos personales/asignacion.php';
          </script>";
}
}
