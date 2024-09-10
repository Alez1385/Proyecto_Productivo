<?php

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

try {
    // Configuración de la base de datos
    $db_host = 'localhost'; 
    $db_name = 'db_gescursos'; 
    $db_user = 'root'; 
    $db_pass = ''; 

    // Crear una nueva conexión utilizando PDO
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

    // Sanitizar y validar los datos del formulario
    $nombre_curso = sanitize_input($_POST['nombre_curso']);
    $descripcion = sanitize_input($_POST['descripcion']);
    $nivel_educativo = sanitize_input($_POST['nivel_educativo']);
    $duracion = filter_var($_POST['duracion'], FILTER_VALIDATE_INT);
    $estado = sanitize_input($_POST['estado']);
    $categoria = filter_var($_POST['categoria'], FILTER_VALIDATE_INT);
    $upload_icon = $_FILES['upload_icon'];

    if (!$nombre_curso || !$descripcion || !$nivel_educativo || !$duracion || !$estado || !$categoria) {
        throw new Exception('Por favor, complete todos los campos correctamente.');
    }

    // Manejo de la imagen del icono
    if ($upload_icon['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'svg'];
        if (!in_array($upload_icon['type'], $allowed_types)) {
            throw new Exception('Tipo de archivo no permitido. Solo se permiten imágenes JPEG, PNG y GIF.');
        }

        $upload_dir = '../../../uploads/icons/';
        $icono_name = uniqid('icon_', true) . '.' . pathinfo($upload_icon['name'], PATHINFO_EXTENSION);
        $icono_path = $upload_dir . $icono_name;

        if (!move_uploaded_file($upload_icon['tmp_name'], $icono_path)) {
            throw new Exception('Error al subir el icono.');
        }
    } else {
        throw new Exception('Por favor, suba un archivo de icono.');
    }

    // Preparar la consulta SQL para insertar en la base de datos
    $sql = "INSERT INTO cursos (nombre_curso, descripcion, nivel_educativo, duracion, estado, id_categoria, icono)
            VALUES (:nombre_curso, :descripcion, :nivel_educativo, :duracion, :estado, :categoria, :icono)";

    $stmt = $pdo->prepare($sql);

    // Ejecutar la consulta con los valores ligados
    $stmt->execute([
        ':nombre_curso' => $nombre_curso,
        ':descripcion' => $descripcion,
        ':nivel_educativo' => $nivel_educativo,
        ':duracion' => $duracion,
        ':estado' => $estado,
        ':categoria' => $categoria,
        ':icono' => $icono_name
    ]);

    // Redirigir a una página de éxito o mostrar mensaje de éxito
    header("Location: ../cursos.php");
    exit();

} catch (PDOException $e) {
    error_log($e->getMessage());
    header("Location: ../../error.php?message=Error en la base de datos.");
    exit();
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../../error.php?message=" . urlencode($e->getMessage()));
    exit();
}

?>
