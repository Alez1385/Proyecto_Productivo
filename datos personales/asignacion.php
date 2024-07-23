<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="../styles/asignacion.css">
</head>

<body>
    <div class="container">
        <form id="contactForm" onsubmit="return validateForm()" method="post" action="../scripts/asig_modulo.php">
            <h2>Asignación</h2>
            
            <div>
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
            </div>

            <div>
            <label for="mod">Modulos</label>
            <div class="checkbox-group">
            <?php

            include "../scripts/conexion.php";

            $sql = "select m.* from modulos m";
            $resultado = $conn->query($sql);
            
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc() ) {
                    echo '<div class="checkbox-container">' .  $row["nom_modulo"] .' </label>' .  '<input type="checkbox" name="' . $row["id_modulo"] . '"> </div>' ;
                    
                    
                    
                }
            } else {
                echo '<p>No hay modulos disponibles</p>';
            }
            ?>
            </div>

            </div>

            <button type="submit">Enviar</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>

</html>

