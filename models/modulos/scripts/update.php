<?php
// Include the database connection
include '../../../scripts/conexion.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id_modulo = $_POST['id_modulo'];
    $nom_modulo = $_POST['nom_modulo'];
    $url = $_POST['url'];
    $icono = $_POST['icono'];

    // Verificar que los campos requeridos no estén vacíos
    if (empty($id_modulo) || empty($nom_modulo) || empty($url) || empty($icono)) {
        echo "Error: Todos los campos son obligatorios.";
        exit;
    }

    // Prepare the SQL statement
    $sql = "UPDATE modulos SET nom_modulo = ?, url = ?, icono = ? WHERE id_modulo = ?";
    
    // Prepare and bind the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $nom_modulo, $url, $icono, $id_modulo);

        // Execute the statement
        if ($stmt->execute()) {
            // Successful update
            echo "<script>
                    alert('Módulo actualizado exitosamente.');
                    window.location.href = '../modulos.php'; // Redirigir al formulario o a la lista de módulos
                  </script>";
        } else {
            // Error in update
            echo "Error al actualizar el módulo: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error in preparing statement
        echo "Error al preparar la consulta: " . $conn->error;
    }

} else {
    // If the script is accessed directly without form submission
    echo "Acceso no autorizado.";
}

// Close the database connection
$conn->close();
?>
