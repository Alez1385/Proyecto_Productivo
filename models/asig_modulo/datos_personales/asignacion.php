<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Asignación de Modulos</title>
    <link rel="stylesheet" href="../styles/asignacion.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .notification {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            color: white;
            display: none;
        }
        .success {
            background-color: #4CAF50;
        }
        .error {
            background-color: #f44336;
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="notification" class="notification"></div>
        
        <form id="assignmentForm" method="post" action="../scripts/asig_modulo.php">
            <h2>Asignación de Módulos</h2>

            <div>
                <label for="userType">Tipo de Usuario</label>
                <select id="userType" name="userType" onchange="fetchAssignedModules(this.value)">
                    <?php
                    require_once "../../../scripts/conexion.php";

                    $sql = "SELECT id_tipo_usuario, nombre FROM tipo_usuario";
                    $resultado = $conn->query($sql);
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row["id_tipo_usuario"]) . '">' . htmlspecialchars($row["nombre"]) . '</option>';
                        }
                    } else {
                        echo '<option value="">No hay tipos de usuario disponibles</option>';
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
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No hay módulos disponibles</p>';
                    }
                    $conn->close();
                    ?>
                </div>
            </div>

            <div class="button-container">
        <button type="submit" name="action" value="assign">Asignar Módulos</button>
        <button type="submit" name="action" value="remove">Quitar Módulos</button>
    </div>
        </form>
    </div>

    <div class="mod_asign_container">
        <h2>Módulos Asignados</h2>
        <div id="assignedModules">
            <p><span id="userTypeName"></span></p>
            <ul id="assignedModulesList"></ul>
        </div>
    </div>

    <div class="back-button-container">
        <a href="../../modulos/modulos.php" class="back-button">Volver a Módulos</a>
    </div>

    <script src="../scripts/modulos.js"></script>
    <script>
        // Función para mostrar notificaciones
        function showNotification(message, isSuccess = true) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = 'notification ' + (isSuccess ? 'success' : 'error');
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000); // La notificación desaparecerá después de 5 segundos
        }

        // Verificar si hay un mensaje en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        if (message) {
            showNotification(message);
        }

        document.getElementById('assignmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Añade el valor de 'action' basado en el botón presionado
            const submitButton = e.submitter;
            formData.append('action', submitButton.value);

            fetch('../scripts/asig_modulo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Respuesta no válida:', text);
                    throw new Error('La respuesta del servidor no es JSON válido');
                }
            })
            .then(data => {
                showNotification(data.message, data.success);
                if (data.success) {
                    fetchAssignedModules(document.getElementById('userType').value);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al procesar la solicitud: ' + error.message, false);
            });
        });
    </script>
</body>

</html>
