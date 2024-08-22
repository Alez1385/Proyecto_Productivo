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
        <form id="contactForm" method="post" action="../scripts/asig_modulo.php">
            <h2>Asignación</h2>

            <div>
                <label for="user">User</label>
                <select id="user" name="user" onchange="fetchAssignedModules(this.value)">
                    <?php
                    include "../scripts/conexion.php";

                    $sql = "SELECT u.id_usuario, u.nombre, u.apellido, tpu.descripcion 
                            FROM usuario u 
                            JOIN tipo_usuario tpu ON tpu.id_tipo_usuario = u.id_tipo_usuario";
                    $resultado = $conn->query($sql);
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo '<option value="' . $row["id_usuario"] . '">' . $row["nombre"] . ' ' . $row["apellido"] . ' (' . $row["descripcion"] . ')</option>';
                        }
                    } else {
                        echo '<option value="">No hay opciones disponibles</option>';
                    }
                    $conn->close();
                    ?>
                </select>
            </div>

            <div>
                <label for="mod">Modulos</label>
                <div class="checkbox-group">
                    <?php
                    include "../scripts/conexion.php";

                    $sql = "SELECT * FROM modulos";
                    $resultado = $conn->query($sql);

                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo '<div class="checkbox-container">';
                            echo '<label for="modulo_' . $row["id_modulo"] . '">' . $row["nom_modulo"] . '</label>';
                            echo '<input type="checkbox" name="id_modulo[' . $row["id_modulo"] . ']" value="' . $row["id_modulo"] . '">';
                            echo '<input type="hidden" name="nom_modulo[' . $row["id_modulo"] . ']" value="' . $row["nom_modulo"] . '">';
                            echo '<input type="hidden" name="desc_modulo[' . $row["id_modulo"] . ']" value="' . $row["desc_modulo"] . '">';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No hay módulos disponibles</p>';
                    }
                    $conn->close();
                    ?>
                </div>
            </div>

            <button type="submit">Enviar</button>
        </form>
    </div>

    <div class="mod_asign_container">
        <h2>Módulos Asignados</h2>
        <div id="assignedModules">
            <p><span id="userName"></span></p>
            <ul id="assignedModulesList"></ul>
        </div>
    </div>

    <script src="../scripts/modulos.js"></script>
</body>

</html>