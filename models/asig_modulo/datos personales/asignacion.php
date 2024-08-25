<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Asignación</title>
    <link rel="stylesheet" href="../styles/asignacion.css">
</head>

<body>
    <div class="container">
        <form id="assignmentForm" method="post" action="../scripts/asig_modulo.php">
            <h2>Asignación</h2>

            <div>
                <label for="user">Usuario</label>
                <select id="user" name="user" onchange="fetchAssignedModules(this.value)">
                    <?php
                    require_once "../../../scripts/conexion.php";

                    $sql = "SELECT u.id_usuario, u.nombre, u.apellido, tpu.nombre as nom_tp
                            FROM usuario u 
                            JOIN tipo_usuario tpu ON tpu.id_tipo_usuario = u.id_tipo_usuario";
                    $resultado = $conn->query($sql);
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row["id_usuario"]) . '">' . htmlspecialchars($row["nombre"] . ' ' . $row["apellido"] . ' (' . $row["nom_tp"] . ')') . '</option>';
                        }
                    } else {
                        echo '<option value="">No hay usuarios disponibles</option>';
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="mod">Módulos</label>
                <div class="checkbox-group">
                    <?php
                    $sql = "SELECT * FROM modulos";
                    $resultado = $conn->query($sql);

                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo '<div class="checkbox-container">';
                            echo '<label for="modulo_' . htmlspecialchars($row["id_modulo"]) . '">' . htmlspecialchars($row["nom_modulo"]) . '</label>';
                            echo '<input type="checkbox" id="modulo_' . htmlspecialchars($row["id_modulo"]) . '" name="id_modulo[' . htmlspecialchars($row["id_modulo"]) . ']" value="' . htmlspecialchars($row["id_modulo"]) . '">';
                            echo '<input type="hidden" name="nom_modulo[' . htmlspecialchars($row["id_modulo"]) . ']" value="' . htmlspecialchars($row["nom_modulo"]) . '">';
                            echo '<input type="hidden" name="desc_modulo[' . htmlspecialchars($row["id_modulo"]) . ']" value="' . htmlspecialchars($row["desc_modulo"]) . '">';
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
