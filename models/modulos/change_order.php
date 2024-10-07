<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Orden de Módulos</title>
    <link rel="stylesheet" href="modulo.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?php
        include "../../scripts/sidebar.php";
        include "../../scripts/conexion.php";

        // Consulta para obtener los módulos
        $sql = "SELECT id_modulo, nom_modulo, icono, orden FROM modulos ORDER BY orden ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
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
                <p>Arrastra los módulos para cambiar su orden y guarda los cambios.</p>

                <div class="module-list" id="sortableModules">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="module-item" data-module-id="' . htmlspecialchars($row["id_modulo"]) . '" data-order="' . htmlspecialchars($row["orden"]) . '" draggable="true">';
                            echo '<div class="module-details">';
                            echo '<span class="material-icons-sharp">' . htmlspecialchars($row["icono"]) . '</span>';
                            echo '<h2>' . htmlspecialchars($row["nom_modulo"]) . '</h2>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "No hay módulos.";
                    }
                    $stmt->close();
                    $conn->close();
                    ?>
                </div>

                <button onclick="saveModuleOrder()" class="assign-btn">Guardar Cambios</button>
            </section>
        </div>
    </div>

    <script>
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

        function saveModuleOrder() {
            const moduleList = document.getElementById('sortableModules');
            const modules = moduleList.getElementsByClassName('module-item');
            let orderData = [];

            for (let i = 0; i < modules.length; i++) {
                let moduleId = modules[i].getAttribute('data-module-id');
                let oldOrder = modules[i].getAttribute('data-order');
                orderData.push({ 
                    id_modulo: moduleId, 
                    old_orden: oldOrder,
                    new_orden: i + 1 
                });
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
                    window.location.href = 'modulos.php';
                } else {
                    alert('Error al actualizar el orden: ' + data.message);
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