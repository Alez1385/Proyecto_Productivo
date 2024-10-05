<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Módulos</title>
    <link rel="stylesheet" href="modulo.css">
</head>

<body>

    <div class="dashboard-container">

        <?php
        include "../../scripts/sidebar.php";
        include "../../scripts/conexion.php";

        // Consulta para obtener los módulos
        $sql = "SELECT id_modulo, nom_modulo, icono FROM modulos";
        $result = $conn->query($sql);
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Control de Módulos</h1>
                </div>
                <div class="header-right">
                <button onclick="window.location.href='new_modulo.php'">+ Agregar Nuevo Módulo</button>
                 <button onclick="window.location.href='../asig_modulo/datos_personales/asignacion.php'" class="assign-btn">Asignar Módulos</button>
                <button onclick="window.location.href='change_order.php'" class="assign-btn">Cambiar Orden</button>
                </div>
            </header>

            <section class="content">
                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar módulos..." onkeyup="filterModules()">
                </div>

                <!-- Lista de módulos -->
                <div class="module-list">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="module-item" data-module-id="' . $row["id_modulo"] . '">';
                            echo '<div class="module-details">';
                            echo '<span class="material-icons-sharp">' . $row["icono"] . '</span>';
                            echo '<h2>' . $row["nom_modulo"] . '</h2>';
                            echo '</div>';
                            echo '<div class="module-actions">';
                            echo '<button onclick="window.location.href=\'edit_module.php?id_modulo=' . $row["id_modulo"] . '\'" class="edit-btn">Editar</button>';
                            echo '<button onclick="deleteModule(' . $row["id_modulo"] . ')" class="delete-btn">Eliminar</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "No hay módulos.";
                    }
                    $conn->close();
                    ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        function deleteModule(moduleId) {
            if (confirm("¿Estás seguro de que quieres eliminar este módulo?")) {
                fetch('delete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_modulo=' + encodeURIComponent(moduleId)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const moduleElement = document.querySelector(`[data-module-id="${moduleId}"]`);
                            if (moduleElement) {
                                moduleElement.remove();
                            }
                            alert('Módulo eliminado exitosamente');
                        } else {
                            alert('Error al eliminar el módulo: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al eliminar el módulo');
                    });
            }
        }

        function filterModules() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const moduleList = document.querySelector('.module-list');
            const modules = moduleList.getElementsByClassName('module-item');

            for (let i = 0; i < modules.length; i++) {
                const moduleDetails = modules[i].getElementsByClassName('module-details')[0];
                const name = moduleDetails.getElementsByTagName('h2')[0].textContent.toLowerCase();

                if (name.indexOf(filter) > -1) {
                    modules[i].style.display = '';
                } else {
                    modules[i].style.display = 'none';
                }
            }
        }
    </script>

</body>

</html>