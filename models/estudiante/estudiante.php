<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes</title>
    <link rel="stylesheet" href="css/student.css">
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
                    <h1>Control de Estudiantes</h1>
                </div>
                <div class="header-right">
                    <button class="add-student-btn" onclick="openSidebar('new_student.php')">+ Agregar Nuevo Estudiante</button>
                </div>
            </header>

            <section class="content">
                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar estudiantes..." onkeyup="filterStudents()">
                </div>

                <!-- Lista de estudiantes -->
                <div class="student-list">
                    <?php
                    include "../../scripts/conexion.php";

                    $sql = "SELECT e.id_estudiante, u.nombre, u.apellido, e.genero, e.fecha_registro, e.estado, e.nivel_educativo, e.observaciones 
                            FROM estudiante e
                            JOIN usuario u ON e.id_usuario = u.id_usuario";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="student-item" data-student-id="' . $row["id_estudiante"] . '">';
                            echo '<img src="../../uploads/' . (empty($row["foto"]) ? '../../img/usuario.png' : $row["foto"]) . '" alt="Student Image">';
                            echo '<div class="student-details">';
                            echo '<h2>' . $row["nombre"] . " " . $row['apellido'] . '</h2>';
                            echo '<p>Género: ' . $row["genero"] . '</p>';
                            echo '<p>Nivel Educativo: ' . $row["nivel_educativo"] . '</p>';
                            echo '<p>Estado: ' . $row["estado"] . '</p>';
                            echo '<p>Fecha de Registro: ' . $row["fecha_registro"] . '</p>';
                            echo '</div>';
                            echo '<div class="student-actions">';
                            echo '<button onclick="openSidebar(\'edit_student.php?id_estudiante=' . $row["id_estudiante"] . '\')" class="edit-btn">Editar</button>';
                            echo '<button onclick="deleteStudent(' . $row["id_estudiante"] . ')" class="delete-btn">Eliminar</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "No hay estudiantes registrados.";
                    }
                    $conn->close();
                    ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        function deleteStudent(studentId) {
            if (confirm("¿Estás seguro de que deseas eliminar este estudiante?")) {
                fetch('delete_student.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_estudiante=' + studentId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-student-id="${studentId}"]`).remove();
                            alert('Estudiante eliminado con éxito');
                        } else {
                            alert('Error eliminando el estudiante: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al eliminar el estudiante.');
                    });
            }
        }

        function filterStudents() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const studentList = document.querySelector('.student-list');
            const students = studentList.getElementsByClassName('student-item');

            for (let i = 0; i < students.length; i++) {
                const studentDetails = students[i].getElementsByClassName('student-details')[0];
                const name = studentDetails.getElementsByTagName('h2')[0].textContent.toLowerCase();
                const details = studentDetails.getElementsByTagName('p');
                let textContent = name;

                for (let j = 0; j < details.length; j++) {
                    textContent += ' ' + details[j].textContent.toLowerCase();
                }

                if (textContent.indexOf(filter) > -1) {
                    students[i].style.display = '';
                } else {
                    students[i].style.display = 'none';
                }
            }
        }

        document.getElementById('overlay').addEventListener('click', toggleSidebar);
    </script>
</body>

</html>