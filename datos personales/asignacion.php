<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <form id="contactForm" onsubmit="return validateForm()" method="post" action="../scripts/asig_modulo.php">
            <h2>Asignación</h2>
            

            <label for="rol">Rol</label>
            <select id="rol" name="rol">
            <?php

            include "../scripts/conexion.php";

            $sql = "select tpu.* from tipo_usuario tpu";
            $resultado = $conn->query($sql);
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    echo '<option value="' . $row["id_tipo_usuario"] . '">' . $row["descripcion"] . '</option>';
                }
            } else {
                echo '<option value"">no hay opciones disponibles</value>';
            }
            ?>
            </select>

            <label for="mod">Modulos</label>
            <select id="mod" name="mod">
            <?php

            include "../scripts/conexion.php";

            $sql = "select m.* from modulos m";
            $resultado = $conn->query($sql);
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    echo '<option value="' . $row["id_modulo"] . '">' . $row["nom_modulo"] . '</option>';
                }
            } else {
                echo '<option value"">no hay opciones disponibles</value>';
            }
            ?>
            </select>


            <button type="submit">Enviar</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>

</html>

