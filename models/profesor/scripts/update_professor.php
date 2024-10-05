<?php
// Configuración
require_once '../../../scripts/conexion.php';
require_once '../../../scripts/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_profesor = sanitizeInput($_POST['id_profesor']);
    $especialidad = sanitizeInput($_POST['especialidad']);
    $experiencia = sanitizeInput($_POST['experiencia']);
    $descripcion = sanitizeInput($_POST['descripcion']);

    $sql = "UPDATE profesor SET 
            especialidad = ?, 
            experiencia = ?, 
            descripcion = ?
            WHERE id_profesor = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('sssi', $especialidad, $experiencia, $descripcion, $id_profesor);
        if ($stmt->execute()) {
            header("Location: ../profesor.php?update_success=1");
            exit();
        } else {
            header("Location: ../profesor.php?update_success=0");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: ../profesor.php?update_success=0");
        exit();
    }
    $conn->close();
}



// Funciones auxiliares
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data));
}

function logError($error)
{
    $log_file = BASE_PATH . 'logs/error.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $error . PHP_EOL, FILE_APPEND);
}

function showError($message)
{
    echo "<script>
            alert('$message');
            window.location.href = '../profesor.php';
          </script>";
    exit();
}
?>