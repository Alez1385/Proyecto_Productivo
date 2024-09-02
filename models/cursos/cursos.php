<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <link rel="stylesheet" href="cursos.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
</head>

<body>
    <div class="dashboard-container">
        <?php

        // Función para registrar errores
        function logError($message)
        {
            error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, '../../logs/error.log');
        }

        try {
            include "../../scripts/sidebar.php";
            include "../../scripts/conexion.php";
        } catch (Exception $e) {
            logError("Error al incluir archivos necesarios: " . $e->getMessage());
            $_SESSION['error'] = "Error al cargar componentes necesarios. Por favor, contacte al administrador.";
            // Redirigir a una página de error general
            header("Location: error.php");
            exit();
        }

        // Consulta para obtener los cursos
        $sql = "SELECT id_curso, nombre_curso, descripcion, nivel_educativo, duracion, estado FROM cursos";
        try {
            $result = $conn->query($sql);
            if (!$result) {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            logError("Error en la consulta SQL: " . $e->getMessage());
            $_SESSION['error'] = "Error al obtener la lista de cursos. Por favor, inténtelo de nuevo más tarde.";
            $result = false;
        }
        ?>
        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Control de Cursos</h1>
                </div>
                <div class="header-right">
                    <button class="add-course-btn" onclick="window.location.href='crear_curso.php'">+ Agregar Nuevo Curso</button>
                </div>
            </header>

            <section class="content">
                <?php
                // Mostrar mensajes de error si existen
                if (isset($_SESSION['error'])) {
                    echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>

                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar cursos..." onkeyup="filterCourses()">
                </div>

                <!-- Lista de cursos -->
                <div class="course-list">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="course-item" data-course-id="' . htmlspecialchars($row["id_curso"]) . '">';
                            echo '<div class="course-icon-container">';
                            echo '<span class="material-icons-sharp course-icon">school</span>';
                            echo '</div>';
                            echo '<div class="course-details">';
                            echo '<h2>' . htmlspecialchars($row["nombre_curso"]) . '</h2>';
                            echo '<p>' . htmlspecialchars($row["descripcion"]) . '</p>';
                            echo '<p>Nivel: ' . htmlspecialchars(ucfirst($row["nivel_educativo"])) . ' | Duración: ' . htmlspecialchars($row["duracion"]) . ' semanas | Estado: ' . htmlspecialchars(ucfirst($row["estado"])) . '</p>';
                            echo '</div>';
                            echo '<div class="course-actions">';
                            echo '<button onclick="window.location.href=\'editar_curso.php?id_curso=' . htmlspecialchars($row["id_curso"]) . '\'" class="edit-btn">Editar</button>';
                            echo '<button onclick="deleteCourse(' . htmlspecialchars($row["id_curso"]) . ')" class="delete-btn">Eliminar</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="no-courses">No hay cursos disponibles o ocurrió un error al cargarlos.</p>';
                    }
                    $conn->close();
                    ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        function deleteCourse(courseId) {
            if (confirm("¿Estás seguro de que deseas eliminar este curso?")) {
                fetch('scripts/delete_courses.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_curso=' + encodeURIComponent(courseId)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const courseElement = document.querySelector(`[data-course-id="${courseId}"]`);
                            if (courseElement) {
                                courseElement.remove();
                            }
                            alert('Curso eliminado exitosamente');
                        } else {
                            throw new Error(data.message || 'Error desconocido al eliminar el curso');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al eliminar el curso: ' + error.message);
                    });
            }
        }

        function filterCourses() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const courseList = document.querySelector('.course-list');
            const courses = courseList.getElementsByClassName('course-item');
            let found = false;

            for (let i = 0; i < courses.length; i++) {
                const courseDetails = courses[i].getElementsByClassName('course-details')[0];
                const name = courseDetails.getElementsByTagName('h2')[0].textContent.toLowerCase();
                const description = courseDetails.getElementsByTagName('p')[0].textContent.toLowerCase();

                if (name.indexOf(filter) > -1 || description.indexOf(filter) > -1) {
                    courses[i].style.display = '';
                    found = true;
                } else {
                    courses[i].style.display = 'none';
                }
            }

            // Mostrar mensaje si no se encontraron cursos
            let noResultsMessage = document.getElementById('noResultsMessage');
            if (!found) {
                if (!noResultsMessage) {
                    noResultsMessage = document.createElement('p');
                    noResultsMessage.id = 'noResultsMessage';
                    noResultsMessage.textContent = 'No se encontraron cursos que coincidan con la búsqueda.';
                    courseList.appendChild(noResultsMessage);
                }
            } else if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    </script>
</body>

</html>
