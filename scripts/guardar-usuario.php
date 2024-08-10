<?php
include 'conexion.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$tipo_doc = $_POST['tipo_doc'];
$documento = $_POST['documento'];
$fecha_nac = $_POST['fecha_nac'];
$direccion = $_POST['direccion'];
$mail = $_POST['mail'];
$telefono = $_POST['telefono'];
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
// Insertar nuevo usuario
$sql = "INSERT INTO usuario (nombre, apellido, tipo_doc, documento, fecha_nac, direccion, foto, mail, telefono, login, clave) VALUES ('$nombre', '$apellido', '$tipo_doc', '$documento', '$fecha_nac', '$direccion', '$foto', '$mail', '$telefono', '$login', '$clave')";

if ($conn->query($sql) === TRUE) {
    echo "Nuevo registro creado exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
}
$conn->close();