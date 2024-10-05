<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Orden de Módulos</title>
    <link rel="stylesheet" href="modulo.css">
</head>

<body>

    <div class="dashboard-container">

        <?php
        include "../../scripts/sidebar.php";
        include "../../scripts/conexion.php";

        // Consulta para obtener los módulos
        $sql = "SELECT id_modulo, nom_modulo, icono FROM modulos ORDER BY id_modulo ASC";
        $result = $conn->query($sql);
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Cambiar Orden de Módulos</h1>
                </div>
                <div class="header-right">
                    <button onclick="window.location.href='modulos.php'" class="assign-btn">Volver</button>
                </div>
            </header>

            <section class="content">
                <!-- Instrucción para el usuario -->
                <p>Arrastra los módulos para cambiar su orden y guarda los cambios.</p>

                <!-- Lista de módulos -->
                <div class="module-list" id="sortableModules">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="module-item" data-module-id="' . $row["id_modulo"] . '" draggable="true">';
                            echo '<div class="module-details">';
                            echo '<span class="material-icons-sharp">' . $row["icono"] . '</span>';
                            echo '<h2>' . $row["nom_modulo"] . '</h2>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "No hay módulos.";
                    }
                    $conn->close();
                    ?>
                </div>

                <!-- Botón para guardar cambios -->
                <button onclick="saveModuleOrder()" class="assign-btn">Guardar Cambios</button>
            </section>
        </div>
    </div>

    <script>
        // Variables para arrastrar y soltar
        let draggedItem = null;

        document.addEventListener('DOMContentLoaded', () => {
            const moduleList = document.getElementById('sortableModules');
            const moduleItems = moduleList.getElementsByClassName('module-item');

            for (let item of moduleItems) {
                item.addEventListener('dragstart', () => {
                    draggedItem = item;
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 0);
                });

                item.addEventListener('dragend', () => {
                    setTimeout(() => {
                        draggedItem.style.display = 'block';
                        draggedItem = null;
                    }, 0);
                });

                item.addEventListener('dragover', (e) => {
                    e.preventDefault();
                });

                item.addEventListener('drop', (e) => {
                    e.preventDefault();
                    if (draggedItem) {
                        let target = e.target.closest('.module-item');
                        if (target && target !== draggedItem) {
                            moduleList.insertBefore(draggedItem, target);
                        }
                    }
                });
            }
        });

        // Función para guardar el nuevo orden de los módulos
        function saveModuleOrder() {
            const moduleList = document.getElementById('sortableModules');
            const modules = moduleList.getElementsByClassName('module-item');
            let orderData = [];

            for (let i = 0; i < modules.length; i++) {
                let moduleId = modules[i].getAttribute('data-module-id');
                orderData.push({ id_modulo: moduleId, orden: i + 1 });
            }

            fetch('update_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(orderData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Orden actualizado correctamente');
                    window.location.href = 'modulos.php'; // Redirige a la lista de módulos
                } else {
                    alert('Error al actualizar el orden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al actualizar el orden');
            });
        }
    </script>

</body>

</html>
