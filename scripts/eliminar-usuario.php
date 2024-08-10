<?php
include "conexion.php";

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Eliminar el usuario de la base de datos
    $sql = "DELETE FROM usuario WHERE id_usuario = '$id_usuario'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Usuario eliminado correctamente');</script>";
        echo "<script> window.location.href='..listar-usuario.php'; </script>";
    } else {
        echo "Error al eliminar el usuario: " . $conn->error;
    }

    $conn->close();
} else {
    echo "No se ha proporcionado un código de usuario.";
}
header("Location: listar-usuario.php");
exit();
?>