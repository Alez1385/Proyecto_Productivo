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
    $id_curso = filter_var($_POST['id_curso'], FILTER_VALIDATE_INT);
    $nombre_curso = sanitize_input($_POST['nombre_curso']);
    $descripcion = sanitize_input($_POST['descripcion']);
    $nivel_educativo = sanitize_input($_POST['nivel_educativo']);
    $duracion = filter_var($_POST['duracion'], FILTER_VALIDATE_INT);
    $estado = sanitize_input($_POST['estado']);
    $categoria = filter_var($_POST['categoria'], FILTER_VALIDATE_INT);
    $upload_icon = $_FILES['upload_icon'];
    $id_profesor = filter_var($_POST['profesor'], FILTER_VALIDATE_INT);

    if (!$id_curso || !$nombre_curso || !$descripcion || !$nivel_educativo || !$duracion || !$estado || !$categoria || !$id_profesor) {
        throw new Exception('Por favor, complete todos los campos correctamente, incluyendo el profesor.');
    }

    // Manejo de la imagen del icono
    $icono_name = null;
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
        // Mantener el icono actual si no se sube uno nuevo
        $stmt = $pdo->prepare("SELECT icono FROM cursos WHERE id_curso = ?");
        $stmt->execute([$id_curso]);
        $current_icono = $stmt->fetchColumn();
        $icono_name = $current_icono;
    }

    // Preparar la consulta SQL para actualizar en la base de datos
    $sql = "UPDATE cursos SET
            nombre_curso = :nombre_curso,
            descripcion = :descripcion,
            nivel_educativo = :nivel_educativo,
            duracion = :duracion,
            estado = :estado,
            id_categoria = :categoria,
            icono = :icono
            WHERE id_curso = :id_curso";

    $stmt = $pdo->prepare($sql);

    // Ejecutar la consulta con los valores ligados
    $stmt->execute([
        ':nombre_curso' => $nombre_curso,
        ':descripcion' => $descripcion,
        ':nivel_educativo' => $nivel_educativo,
        ':duracion' => $duracion,
        ':estado' => $estado,
        ':categoria' => $categoria,
        ':icono' => $icono_name,
        ':id_curso' => $id_curso
    ]);

    // Actualizar o insertar la asignación del profesor
    $sql_check_assignment = "SELECT id_asignacion FROM asignacion_curso WHERE id_curso = ? AND estado = 'activo'";
    $stmt_check = $pdo->prepare($sql_check_assignment);
    $stmt_check->execute([$id_curso]);

    if ($stmt_check->rowCount() > 0) {
        // Actualizar la asignación existente
        $sql_update_teacher = "UPDATE asignacion_curso SET id_profesor = ?, fecha_asignacion = CURDATE() WHERE id_curso = ? AND estado = 'activo'";
        $stmt_update_teacher = $pdo->prepare($sql_update_teacher);
        $stmt_update_teacher->execute([$id_profesor, $id_curso]);
    } else {
        // Insertar una nueva asignación
        $sql_insert_teacher = "INSERT INTO asignacion_curso (id_curso, id_profesor, fecha_asignacion, estado) VALUES (?, ?, CURDATE(), 'activo')";
        $stmt_insert_teacher = $pdo->prepare($sql_insert_teacher);
        $stmt_insert_teacher->execute([$id_curso, $id_profesor]);
    }

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
