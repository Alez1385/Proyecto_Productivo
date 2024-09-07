<?php
require_once '../../../scripts/conexion.php';

// Función para registrar errores
function logError($message)
{
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, '../../logs/error.log');
    echo "<script>alert('$message');</script>";
}

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_curso = intval($_POST['id_curso']);
    $nombre_curso = trim($_POST['nombre_curso']);
    $descripcion = trim($_POST['descripcion']);
    $nivel_educativo = trim($_POST['nivel_educativo']);
    $duracion = intval($_POST['duracion']);
    $estado = trim($_POST['estado']);

    // Procesar la carga del icono si se subió uno nuevo
    $icono = '';
    if (isset($_FILES['upload_icon']) && $_FILES['upload_icon']['error'] === UPLOAD_ERR_OK) {
        $icono = basename($_FILES['upload_icon']['name']);
        $target_dir = "../../../uploads/icons/";
        $target_file = $target_dir . $icono;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validar que el archivo subido sea una imagen
        $check = getimagesize($_FILES['upload_icon']['tmp_name']);
        if ($check === false && $imageFileType != "svg") {
            logError("El archivo subido no es una imagen o un SVG válido.");
            
            exit();
        }

        // Validar el tamaño del archivo (máximo 5MB)
        if ($_FILES['upload_icon']['size'] > 5000000) {
            logError("El archivo es demasiado grande.");
            
            exit();
        }

        // Validar el tipo de archivo (solo imágenes JPG, JPEG, PNG, GIF, y SVG)
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif", "svg"])) {
            logError("Solo se permiten archivos JPG, JPEG, PNG, GIF y SVG.");
            
            exit();
        }

        // Mover el archivo subido al directorio de destino
        if (!move_uploaded_file($_FILES['upload_icon']['tmp_name'], $target_file)) {
            logError("Hubo un error al subir el archivo.");
            
            exit();
        }
    }

    // Actualizar el curso en la base de datos
    try {
        if (!empty($icono)) {
            $stmt = $conn->prepare("UPDATE cursos SET nombre_curso = ?, descripcion = ?, nivel_educativo = ?, duracion = ?, estado = ?, icono = ? WHERE id_curso = ?");
            $stmt->bind_param("ssssssi", $nombre_curso, $descripcion, $nivel_educativo, $duracion, $estado, $icono, $id_curso);
        } else {
            $stmt = $conn->prepare("UPDATE cursos SET nombre_curso = ?, descripcion = ?, nivel_educativo = ?, duracion = ?, estado = ? WHERE id_curso = ?");
            $stmt->bind_param("sssssi", $nombre_curso, $descripcion, $nivel_educativo, $duracion, $estado, $id_curso);
        }

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $stmt->close();
        $conn->close();

        // Redirigir a la página de detalles del curso actualizado
        header("Location: ../cursos.php");
        exit();
    } catch (Exception $e) {
        logError("Error al actualizar el curso: " . $e->getMessage());
        header("Location: ../cursos.php");
        exit();
    }
} else {
    // Redirigir si el acceso no es a través de un formulario POST
    header("Location: ../cursos.php");
    exit();
}
