<?php
include '../../scripts/conexion.php';
function getUserIds($conn) {
    $sql = "SELECT id_usuario, CONCAT(nombre,' ', apellido) as nombre_completo FROM usuario ORDER BY id_usuario";
    $result = $conn->query($sql);

    $ids = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ids[] = $row['nombre_completo'];
        }
    }
    return $ids;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="../../css/form.css">
</head>
<body>
    <div class="form-container">
        <form class="course-form" action="scripts/register_student.php" method="post">
            <h2>Student Registration</h2>
            <?php
            $userIds = getUserIds($conn); // Pasar la conexión a la función
            ?>

            <!-- Campo id_usuario (cuadro combinado) -->
            <div class="form-group">
                <select name="id_usuario" required>
                    <option value="" disabled selected>Select User</option>
                    <?php foreach ($userIds as $id): ?>
                        <option value="<?php echo htmlspecialchars($id); ?>"><?php echo htmlspecialchars($id); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Campo genero -->
            <div class="form-group">
                <select name="genero" required>
                    <option value="" disabled selected>Gender</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                    <option value="O">Other</option>
                </select>
            </div>

            <!-- Campo fecha_registro -->
            <div class="form-group">
                <input type="date" placeholder="Registration Date" name="fecha_registro" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <!-- Campo estado -->
            <div class="form-group">
                <select name="estado" required>
                    <option value="activo">Active</option>
                    <option value="inactivo">Inactive</option>
                </select>
            </div>
                                
            <!-- Campo nivel_educativo -->
            <div class="form-group">
                <select id="nivel_educativo" name="nivel_educativo" required>
                    <option value="" disabled selected>Seleccione el Nivel Educativo</option>
                    <option value="primaria">Primaria</option>
                    <option value="secundaria">Secundaria</option>
                    <option value="terciaria">Terciaria</option>
                </select>
            </div>

            <!-- Campo observaciones -->
            <div class="form-group">
                <textarea placeholder="Observations" name="observaciones" rows="4"></textarea>
            </div>


            <button type="submit" class="btn btn-primary">Register Student</button>
        </form>
    </div>
</body>
</html>
