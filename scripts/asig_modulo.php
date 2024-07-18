<?php

include ("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mod = $_POST['mod'];
    $rol = $_POST['rol'];

    $sql = "INSERT INTO asig_modulo(id_modulo, id_usuario) 
            VALUES ('$mod', '$rol');";

    if ($conn->query($sql) === TRUE) {
        echo "Nuevo registro creado exitosamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Cerrar la conexión
$conn->close();
?>
