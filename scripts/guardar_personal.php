<?php

include ("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $name = $_POST['name'];
    $apellido = $_POST['apellido'];
    $documento = $_POST['documento'];
    $tipo_documento = $_POST['tipo_doc'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $gender = $_POST['gender'];
    $active = $_POST['active'];
    $perfil = $_POST['role'];

    $message = $_POST['message'];
    $login = $_POST['login'];
    $clave = $_POST['clave'];

    if (isset($_FILES['curriculum']) && $_FILES['curriculum']['error'] == UPLOAD_ERR_OK) {
        $curriculum = $_FILES['curriculum']['name'];
        $foto_target_file = "../uploads/" . basename($curriculum);
        move_uploaded_file($_FILES["curriculum"]["tmp_name"], $foto_target_file);
    } else {
        $curriculum = NULL;
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $foto = $_FILES['foto']['name'];
        $foto_target_file = "../uploads/" . basename($foto);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $foto_target_file);
    } else {
        $foto = NULL;
    }

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO datos_personales (tipo_documento, documento, apellido, name, phone, email, fecha_nacimiento, gender, active, perfil, curriculum, foto, message, login, clave) 
            VALUES ('$tipo_documento', '$documento', '$apellido', '$name', '$phone', '$email', '$fecha_nacimiento', '$gender', '$active', '$perfil', '$curriculum', '$foto', '$message', '$login', '$clave')";

    if ($conn->query($sql) === TRUE) {
        echo "Nuevo registro creado exitosamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Cerrar la conexión
$conn->close();
?>