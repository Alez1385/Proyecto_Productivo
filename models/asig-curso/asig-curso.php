<?php
require_once '../../scripts/conexion.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Curso</title>
    <link rel="stylesheet" href="../../css/form.css"> <!-- Asegúrate de que esta ruta sea correcta -->
</head>
<body>
    <div class="form-container">
        <form class="assign-course-form" action="register_assignment.php" method="post">
            <h2>Asignación de Curso</h2>

            <div class="form-group">
                <select id="id_curso" name="id_curso" required>
                    <option value="" disabled selected>Seleccione un Curso</option>
                    <?php
                    $query = "SELECT id_curso, nombre_curso FROM cursos WHERE estado = 'activo'";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_curso'] . "'>" . $row['nombre_curso'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay cursos disponibles</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <select id="id_profesor" name="id_profesor" required>
                    <option value="" disabled selected>Seleccione un Profesor</option>
                    <?php
                    $query = "SELECT id_usuario, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM usuario WHERE rol = 'profesor' AND estado = 'activo'";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_usuario'] . "'>" . $row['nombre_completo'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay profesores disponibles</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <select id="id_estudiante" name="id_estudiante" required>
                    <option value="" disabled selected>Seleccione un Estudiante</option>
                    <?php
                    $query = "SELECT id_usuario, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM usuario WHERE rol = 'estudiante' AND estado = 'activo'";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_usuario'] . "'>" . $row['nombre_completo'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay estudiantes disponibles</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <input type="date" id="fecha_asignacion" name="fecha_asignacion" required placeholder="Fecha de Asignación">
            </div>

            <div class="form-group">
                <textarea id="comentarios" name="comentarios" placeholder="Comentarios opcionales" rows="4"></textarea>
            </div>

            <div class="form-group">
                <select id="estado" name="estado" required>
                    <option value="" disabled selected>Seleccione el Estado</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Asignar Curso</button>
        </form>
    </div>
</body>
</html>
