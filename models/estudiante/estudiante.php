<?php
include '../../scripts/conexion.php';

function getUserIds($conn) {
    $sql = "SELECT u.id_usuario, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, tu.nombre AS tipo_usuario
            FROM usuario u
            JOIN tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
            WHERE u.estado = 'activo' AND u.id_tipo_usuario like 3  -- Filtrar solo estudiantes y profesores
            ORDER BY u.id_usuario";
    $result = $conn->query($sql);

    $users = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    $conn->close(); // Cerrar la conexión a la base de datos
    return $users;
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
            $users = getUserIds($conn); // Obtener la lista de usuarios con tipo
            ?>

            <!-- Campo id_usuario (cuadro combinado) -->
            <div class="form-group">
                <select name="id_usuario" required>
                    <option value="" disabled selected>Select User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id_usuario']); ?>">
                            <?php echo htmlspecialchars($user['nombre_completo']) . ' (' . htmlspecialchars($user['tipo_usuario']) . ')'; ?>
                        </option>
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
                    <option value="" disabled selected>Select Educational Level</option>
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
