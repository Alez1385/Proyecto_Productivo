<?php
include '../../scripts/conexion.php'; 

function getModuloData($conn) {
    $sql = "SELECT id_modulo, nom_modulo FROM modulos ORDER BY id_modulo";
    $result = $conn->query($sql);

    $modulos = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $modulos[] = $row;
        }
    }
    return $modulos;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Módulos</title>
    <link rel="stylesheet" href="../../css/form.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <style>
        .icon-picker-container {
            display: flex;
            align-items: center;
        }

        .icon-picker-container .material-icons-sharp {
            font-size: 24px;
            margin-right: 10px;
            cursor: pointer;
        }

        .icon-picker-list {
            display: flex;
            flex-wrap: wrap;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #fff;
        }

        .icon-picker-list .icon-item {
            font-size: 24px;
            margin: 5px;
            cursor: pointer;
        }

        
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Registrar Módulo</h2>
        <form action="/scripts/procesar_modulo.php" method="POST" enctype="multipart/form-data">
            
            <!-- Campo nom_modulo -->
            <div class="form-group">
                <input type="text" id="nom_modulo" name="nom_modulo" placeholder="Nombre del Módulo" required>
            </div>

            <!-- Campo url -->
            <div class="form-group">
                <input type="text" id="url" name="url" placeholder="URL del Módulo" required>
            </div>

            <!-- Selector de icono -->
            <div class="form-group icon-picker-container">
                <span id="selected-icon" class="material-icons-sharp">person</span>
                <input type="hidden" id="icono" name="icono" value="person">
                <button class="btn btn-primary" type="button" onclick="toggleIconPicker()">Seleccionar Icono</button>
            </div>
            <div id="icon-picker" class="icon-picker-list" style="display: none;">
                <!-- Aquí se listan los íconos disponibles -->
                <span class="icon-item material-icons-sharp">person</span>
                <span class="icon-item material-icons-sharp">home</span>
                <span class="icon-item material-icons-sharp">school</span>
                <span class="icon-item material-icons-sharp">work</span>
                <span class="icon-item material-icons-sharp">card_travel</span>
                <span class="icon-item material-icons-sharp">assignment</span>
                <span class="icon-item material-icons-sharp">question_answer</span>
                <span class="icon-item material-icons-sharp">assignment_turned_in</span>
                <span class="icon-item material-icons-sharp">event</span>
                <span class="icon-item material-icons-sharp">local_library</span>
                <span class="icon-item material-icons-sharp">phone</span>
                <span class="icon-item material-icons-sharp">email</span>
                <span class="icon-item material-icons-sharp">notifications</span>
                <span class="icon-item material-icons-sharp">report_problem</span>
                <span class="icon-item material-icons-sharp">schedule</span>
                <span class="icon-item material-icons-sharp">more_horiz</span>
                <!-- Agrega más íconos según sea necesario -->
            </div>

            <div class="form-group">
                <a href="modulos.php" style="color: #00bcff;">Volver</a>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>

    <script>
        function toggleIconPicker() {
            const picker = document.getElementById('icon-picker');
            picker.style.display = picker.style.display === 'none' ? 'flex' : 'none';
        }

        document.querySelectorAll('.icon-item').forEach(item => {
            item.addEventListener('click', function() {
                const icon = this.textContent;
                document.getElementById('selected-icon').textContent = icon;
                document.getElementById('icono').value = icon;
                toggleIconPicker(); // Cierra el selector después de seleccionar un icono
            });
        });
    </script>
</body>
</html>
