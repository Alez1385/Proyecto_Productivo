<?php
function fetch_options($sql, $conn, $value_column, $text_column)
{
    $options = '';
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='" . htmlspecialchars($row[$value_column], ENT_QUOTES, 'UTF-8') . "'>" .
                htmlspecialchars($row[$text_column], ENT_QUOTES, 'UTF-8') . "</option>";
        }
    } else {
        $options .= "<option value=''>No hay datos disponibles</option>";
    }

    return $options;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inscripción</title>
    <link rel="stylesheet" href="../../css/form.css">
</head>

<body>
    <div class="form-container">
        <h2>Inscripción</h2>
        <form action="procesar_inscripcion.php" method="POST">
            <div class="form-group">
                <label for="id_curso">Curso:</label>
                <select id="id_curso" name="id_curso" required>
                    <option value="">Seleccione un curso</option>
                    <?php

                    include '../../scripts/conexion.php';
                    include 'functions.php';

                    $sql = "SELECT id_curso, nombre_curso FROM cursos WHERE estado = 'activo'";
                    echo fetch_options($sql, $conn, 'id_curso', 'nombre_curso');

                    $conn->close();


                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_estudiante">Estudiante:</label>
                <select id="id_estudiante" name="id_estudiante" required>
                    <option value="">Seleccione un estudiante</option>
                    <?php

                    include '../../scripts/conexion.php';
                    include 'functions.php';

                    $sql = "SELECT id_estudiante, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM estudiante WHERE estado = 'activo'";
                    echo fetch_options($sql, $conn, 'id_estudiante', 'nombre_completo');

                    $conn->close();
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_inscripcion">Fecha de Inscripción:</label>
                <input type="date" id="fecha_inscripcion" name="fecha_inscripcion" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Inscribir</button>
            </div>
        </form>
    </div>
</body>

</html>