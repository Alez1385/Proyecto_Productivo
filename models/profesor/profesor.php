<?php
include '../../scripts/conexion.php'; 

function getUserIds($conn) {
    $sql = "SELECT id_usuario, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM usuario ORDER BY id_usuario";
    $result = $conn->query($sql);

    $ids = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ids[] = $row; // Guardamos el array completo para usar tanto el ID como el nombre completo
        }
    }
    return $ids;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Profesor</title>
    <link rel="stylesheet" href="../../css/form.css">
</head>
<body>
    <div class="form-container">
        <h2>Registrar Profesor</h2>
        <form action="procesar_profesor.php" method="POST" enctype="multipart/form-data">
            <?php
            $userIds = getUserIds($conn); 
            ?>

            <!-- Campo id_usuario (cuadro combinado) -->
            <div class="form-group">
                <select name="id_usuario" required>
                    <option value="" disabled selected>Selecciona el Usuario</option>
                    <?php foreach ($userIds as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id_usuario']); ?>">
                            <?php echo htmlspecialchars($user['nombre_completo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Campo especialidad -->
            <div class="form-group">
                <input type="text" id="especialidad" name="especialidad" placeholder="Especialidad" required>
            </div>

            <!-- Campo experiencia -->
            <div class="form-group">
                <input type="text" id="experiencia" name="experiencia" placeholder="Experiencia" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</body>
</html>