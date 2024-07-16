<?php

include ("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $apellido = $_POST['apellido'];
    $documento = $_POST['documento'];
    $tipo_documento = $_POST['tipo_doc'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    $login = $_POST['login'];
    $clave = $_POST['clave'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $foto = $_FILES['foto']['name'];
        $foto_target_file = "../uploads/" . basename($foto);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $foto_target_file);
    } else {
        $foto = NULL;
        echo "la foto no se subio torombolo <br>";
    }

    $sql = "INSERT INTO usuario(tipo_doc, documento, apellido, nombre, telefono, mail, fecha_nac, foto, login, clave) 
            VALUES ('$tipo_documento', '$documento', '$apellido', '$name', '$phone', '$email', '$fecha_nacimiento', '$foto', '$login', '$clave')";

    if ($conn->query($sql) === TRUE) {
        echo "Nuevo registro creado exitosamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Cerrar la conexión
$conn->close();
?>
