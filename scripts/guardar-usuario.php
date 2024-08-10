<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $tipo_doc = $_POST['tipo_doc'];
    $documento = $_POST['documento'];
    $fecha_nac = $_POST['fecha_nac'];
    $foto = ""; // Ruta de la foto
    $mail = $_POST['mail'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $id_tipo_usuario = $_POST['id_tipo_usuario'];
    $login = $_POST['login'];
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT); // Hash de la contraseña

    // Manejo de archivo de imagen
    if ($_FILES['foto']['error'] == 0) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_nombre = $_FILES['foto']['name'];
        $foto_ruta = 'uploads/' . basename($foto_nombre);
        
        // Mover el archivo a la carpeta de uploads
        if (move_uploaded_file($foto_tmp, $foto_ruta)) {
            $foto = $foto_ruta;
        }
    }

    // Preparar y ejecutar la consulta
    $sql = "INSERT INTO usuarios (nombre, apellido, tipo_doc, documento, fecha_nac, foto, mail, telefono, direccion, id_tipo_usuario, login, clave)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssssis', $nombre, $apellido, $tipo_doc, $documento, $fecha_nac, $foto, $mail, $telefono, $direccion, $id_tipo_usuario, $login, $clave);

    if ($stmt->execute()) {
        echo "Registro exitoso";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
} else {
    // Redirigir al formulario si no se envió el formulario
    header("Location: register.html");
    exit();
}
?>
