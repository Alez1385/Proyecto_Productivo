<?php

// sanitize_input.php
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

try {
    // Configuración de la base de datos
    // Configuración de la base de datos
    $db_host = 'localhost'; // Cambia según tu configuración
    $db_name = 'db_gescursos'; // Cambia según tu configuración
    $db_user = 'root'; // Cambia según tu configuración
    $db_pass = ''; // Cambia según tu configuración



    // Crear una nueva conexión utilizando PDO para mayor seguridad
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

    // Validación adicional
    if (!$nombre_curso || !$descripcion || !$nivel_educativo || !$duracion || !$estado) {
        throw new Exception('Por favor, complete todos los campos correctamente.');
    }

    // Preparar la consulta SQL para insertar en la base de datos
    $sql = "INSERT INTO cursos (nombre_curso, descripcion, nivel_educativo, duracion, estado)
            VALUES (:nombre_curso, :descripcion, :nivel_educativo, :duracion, :estado)";

    $stmt = $pdo->prepare($sql);

    // Ejecutar la consulta con los valores ligados
    $stmt->execute([
        ':nombre_curso' => $nombre_curso,
        ':descripcion' => $descripcion,
        ':nivel_educativo' => $nivel_educativo,
        ':duracion' => $duracion,
        ':estado' => $estado
    ]);

    // Redirigir a una página de éxito o mostrar mensaje de éxito
    header("Location: ../cursos.php");
    exit();
} catch (PDOException $e) {
    // Manejo de errores de la base de datos
    error_log($e->getMessage());
    header("Location: ../../error.php?message=Error en la base de datos.");
    exit();
} catch (Exception $e) {
    // Manejo de errores generales
    error_log($e->getMessage());
    header("Location: ../../error.php?message=" . urlencode($e->getMessage()));
    exit();
}
