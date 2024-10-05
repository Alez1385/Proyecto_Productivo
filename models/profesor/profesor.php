<?php
include "../../scripts/conexion.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Profesores</title>
    <link rel="stylesheet" href="css/profesor.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script type="module">
        import SidebarManager from '../../js/form_side.js';

        window.toggleSidebar = SidebarManager.toggle;
        window.openSidebar = SidebarManager.open;

        document.addEventListener('DOMContentLoaded', SidebarManager.init);
    </script>

</head>

<body>
    <div id="overlay"></div>
    <div id="sidebar">
        <button class="sidebar-close" onclick="toggleSidebar()">&times;</button>
        <div id="sidebar-content">
            <!-- El contenido del formulario se cargará dinámicamente aquí -->
        </div>
        <div id="sidebar-resizer"></div>
    </div>

    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Control de Profesores</h1>
                </div>
                <div class="header-right">
                    <button class="add-professor-btn" onclick="openSidebar('assign_professor.php')">+ Asignar Nuevo Profesor</button>
                </div>
            </header>

            <section class="content">
                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar profesores..." onkeyup="filterProfessors()">
                </div>

                <!-- Lista de profesores -->
                <div class="professor-list">
                    <?php
                    $sql = "SELECT p.id_profesor, u.nombre, u.apellido, u.mail, u.foto, p.especialidad, p.experiencia, p.descripcion 
                            FROM profesor p
                            JOIN usuario u ON p.id_usuario = u.id_usuario";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="professor-item" data-professor-id="' . $row["id_profesor"] . '">';
                            echo '<img src="../../uploads/' . (empty($row["foto"]) ? '../../img/usuario.png' : $row["foto"]) . '" alt="Professor Image">';
                            echo '<div class="professor-details">';
                            echo '<h2>' . $row["nombre"] . " " . $row['apellido']  . '</h2>';
                            echo '<p>Especialidad: ' . $row["especialidad"] . '</p>';
                            echo '<p>Experiencia: ' . $row["experiencia"] . ' años</p>';
                            echo '<p>' . $row["mail"] . '</p>';
                            echo '<p>Descripcion: ' . $row["descripcion"] . ' </p>';
                            echo '</div>';
                            echo '<div class="professor-actions">';
                            echo '<button onclick="openSidebar(\'edit_professor.php?id_profesor=' . $row["id_profesor"] . '\')" class="edit-btn">Editar</button>';
                            echo '<button onclick="deleteProfessor(' . $row["id_profesor"] . ')" class="delete-btn">Eliminar</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "No hay profesores asignados.";
                    }
                    $conn->close();
                    ?>
                </div>
            </section>
        </div>
    </div>

    <!-- Contenedor para la notificación tipo toast -->
    <div id="toast" class="toast hidden"></div>

    <script>
        // Función para eliminar profesor
        function deleteProfessor(professorId) {
            if (confirm("¿Estás seguro de que deseas eliminar este profesor?")) {
                fetch('delete_professor.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_profesor=' + professorId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-professor-id="${professorId}"]`).remove();
                            alert('Profesor eliminado con éxito');
                        } else {
                            alert('Error eliminando el profesor: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al eliminar el profesor.');
                    });
            }
        }

        // Filtrar profesores en la lista
        function filterProfessors() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const professorList = document.querySelector('.professor-list');
            const professors = professorList.getElementsByClassName('professor-item');

            for (let i = 0; i < professors.length; i++) {
                const professorDetails = professors[i].getElementsByClassName('professor-details')[0];
                const name = professorDetails.getElementsByTagName('h2')[0].textContent.toLowerCase();
                const specialty = professorDetails.getElementsByTagName('p')[0].textContent.toLowerCase();
                const email = professorDetails.getElementsByTagName('p')[2].textContent.toLowerCase();

                if (name.indexOf(filter) > -1 || specialty.indexOf(filter) > -1 || email.indexOf(filter) > -1) {
                    professors[i].style.display = '';
                } else {
                    professors[i].style.display = 'none';
                }
            }
        }

        // Mostrar notificación tipo toast
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const toast = document.getElementById('toast');

            if (urlParams.has('update_success')) {
                const success = urlParams.get('update_success') === '1';
                if (success) {
                    showToast('Profesor actualizado con éxito');
                } else {
                    showToast('Error al actualizar el profesor', true);
                }
            }

            function showToast(message, isError = false) {
                toast.textContent = message;
                toast.style.backgroundColor = isError ? '#f44336' : '#4caf50'; // Rojo para error, verde para éxito
                toast.classList.remove('hidden');

                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000); // Ocultar después de 3 segundos
            }
        });

        document.getElementById('overlay').addEventListener('click', toggleSidebar);
    </script>
</body>

</html>
