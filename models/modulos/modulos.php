<?php
include '../../scripts/conexion.php'; 

function getModuloData($conn) {
    $sql = "SELECT id_modulo, nom_modulo FROM modulos ORDER BY id_modulo";
    $result = $conn->query($sql);

    $modulos = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $modulos[] = $row;
        }
    }
    return $modulos;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Módulos</title>
    <link rel="stylesheet" href="../../css/form.css">
</head>
<body>
    <div class="form-container">
        <h2>Registrar Módulo</h2>
        <form action="procesar_modulo.php" method="POST" enctype="multipart/form-data">
            
            <!-- Campo nom_modulo -->
            <div class="form-group">
                <input type="text" id="nom_modulo" name="nom_modulo" placeholder="Nombre del Módulo" required>
            </div>

            <!-- Campo url -->
            <div class="form-group">
                <input type="text" id="url" name="url" placeholder="URL del Módulo" required>
            </div>

            <!-- Campo icono -->
            <div class="form-group">
                <input type="text" id="icono" name="icono" placeholder="Icono (clase de icono)" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</body>
</html>
