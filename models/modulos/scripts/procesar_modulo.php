<?php
include '../../scripts/conexion.php';

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $nom_modulo = $_POST['nom_modulo'];
    $url = $_POST['url'];
    $icono = $_POST['icono'];

    // Validar los datos recibidos
    if (empty($nom_modulo) || empty($url) || empty($icono)) {
        die('Todos los campos son obligatorios.');
    }

    // Escapar los datos para prevenir inyecciones SQL
    $nom_modulo = $conn->real_escape_string($nom_modulo);
    $url = $conn->real_escape_string($url);
    $icono = $conn->real_escape_string($icono);

    // Preparar la consulta para insertar el nuevo módulo
    $sql = "INSERT INTO modulos (nom_modulo, url, icono) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error al preparar la consulta: ' . $conn->error);
    }
    $stmt->bind_param("sss", $nom_modulo, $url, $icono);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir o mostrar un mensaje de éxito
        echo "<script>
                alert('Módulo registrado exitosamente.');
                window.location.href = 'modulos.php'; // Redirigir al formulario o a la lista de módulos
              </script>";
    } else {
        die('Error al guardar el módulo: ' . $stmt->error);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
}
?>
