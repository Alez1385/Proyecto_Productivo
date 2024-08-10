<?php
include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listar Usuarios</title>
    <link rel="stylesheet" href="../css/listar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function confirmDelete(id_usuario) {
            if (confirm('¿Estás seguro de que deseas eliminar esta copia?')) {
                window.location.href = 'eliminar-usuario.php?id_usuario=' + id_usuario;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Listado de Usuarios</h1>
        <?php
        $sql = "SELECT * FROM usuario";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<thead><tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Documento</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                  </tr></thead>";
            echo "<tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row["id_usuario"]."</td>";
                echo "<td>".$row["nombre"]."</td>";
                echo "<td>".$row["apellido"]."</td>";
                echo "<td>".$row["tipo_doc"]." ".$row["documento"]."</td>";
                echo "<td>".$row["mail"]."</td>";
                echo "<td>".$row["telefono"]."</td>";
                echo "<td class='actions'>";
                echo "<a href='editar-usuario.php?id_usuario=".$row["id_usuario"]."' class='edit-btn' title='Editar'><i class='fas fa-edit'></i></a>";
                echo "<a onclick='confirmDelete(" . $row['id_usuario'] . ")' class='delete-btn' title='Borrar'><i class='fas fa-trash'></i></a>";
               
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p class='no-users'>No hay usuarios registrados.</p>";
        }
        $conn->close();
        ?>
        <a href="../usuario.html" class="back-btn"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</body>
</html>
