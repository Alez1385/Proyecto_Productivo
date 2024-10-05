<?php
require_once '../../scripts/conexion.php';
require_once '../../scripts/auth.php';
requireLogin();
checkPermission('admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <link rel="stylesheet" href="cursos.css">
    <script type="module">
        import SidebarManager from '../../js/form_side.js';

        // Exponer al ámbito global
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

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Control de Cursos</h1>
                </div>
                <div class="header-right">
                    <button class="add-course-btn" onclick="window.location.href='../categoria_curso/modificar_categorias.php'">Modificar Categorías</button>
                    <button class="add-course-btn" onclick="openSidebar('crear_curso.php')">+ Añadir nuevo curso</button>
                </div>
            </header>
            <section class="content">
                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar cursos..." onkeyup="filterCourses()">
                </div>

                <!-- Barra de filtros -->
                <div class="filter-bar">
                    <select id="categoryFilter" onchange="filterCourses()">
                        <option value="">Categoría</option>
                        <?php
                        include "../../scripts/conexion.php";
                        $sql_categories = "SELECT id_categoria, nombre_categoria FROM categoria_curso";
                        $categories = $conn->query($sql_categories);
                        while ($row = $categories->fetch_assoc()) {
                            echo "<option value='" . $row['id_categoria'] . "'>" . $row['nombre_categoria'] . "</option>";
                        }
                        ?>
                    </select>

                    <select id="statusFilter" onchange="filterCourses()">
                        <option value="">Estado</option>
                        <?php
                        $sql_states = "SELECT DISTINCT estado FROM cursos";
                        $states = $conn->query($sql_states);
                        while ($row = $states->fetch_assoc()) {
                            echo "<option value='" . $row['estado'] . "'>" . $row['estado'] . "</option>";
                        }
                        ?>
                    </select>

                    <select id="levelFilter" onchange="filterCourses()">
                        <option value="">Nivel Educativo</option>
                        <?php
                        $sql_levels = "SELECT DISTINCT nivel_educativo FROM cursos";
                        $levels = $conn->query($sql_levels);
                        while ($row = $levels->fetch_assoc()) {
                            echo "<option value='" . $row['nivel_educativo'] . "'>" . $row['nivel_educativo'] . "</option>";
                        }
                        ?>
                    </select>

                    <!-- Filtros aplicados -->
                    <div id="appliedFilters">
                        <!-- Los filtros aplicados se mostrarán aquí dinámicamente -->
                    </div>
                    <button id="resetFilters" onclick="resetFilters()">Resetear Filtros</button>
                </div>

                <!-- Lista de cursos -->
                <div class="course-list">
                    <?php
                    $sql = "SELECT c.id_curso, c.nombre_curso, c.descripcion, c.nivel_educativo, c.duracion, c.estado, c.id_categoria, c.icono,
                                   u.nombre AS nombre_profesor, u.apellido AS apellido_profesor,
                                   GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios,
                                   COUNT(DISTINCT i.id_estudiante) AS num_estudiantes
                            FROM cursos c
                            LEFT JOIN asignacion_curso ac ON c.id_curso = ac.id_curso
                            LEFT JOIN profesor p ON ac.id_profesor = p.id_profesor
                            LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
                            LEFT JOIN horarios h ON c.id_curso = h.id_curso
                            LEFT JOIN inscripciones i ON c.id_curso = i.id_curso
                            GROUP BY c.id_curso";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="course-item" data-course-id="' . $row["id_curso"] . '" data-category="' . $row["id_categoria"] . '" data-status="' . $row["estado"] . '" data-level="' . $row["nivel_educativo"] . '">';
                            echo '<img src="../../uploads/icons/' . (empty($row["icono"]) ? '/img/usuario.png' : $row["icono"]) . '" alt="Course Image">';
                            echo '<div class="course-content">';
                            echo '<div class="course-details">';
                            echo '<h2>' . $row["nombre_curso"] . '</h2>';
                            echo '<p>' . $row["descripcion"] . '</p>';
                            echo '<p><strong>Nivel: </strong>' . $row["nivel_educativo"] . '</p>';
                            echo '<p><strong>Duración: </strong>' . $row["duracion"] . ' semanas</p>';
                            echo '<p><strong>Profesor: </strong>' . $row["nombre_profesor"] . ' ' . $row["apellido_profesor"] . '</p>';
                            echo '<p><strong>Horarios: </strong>' . $row["horarios"] . '</p>';
                            echo '<p><strong>Estudiantes inscritos: </strong>' . $row["num_estudiantes"] . '</p>';
                            echo '</div>';
                            echo '<div class="course-actions">';
                            echo '<button onclick="openSidebar(\'editar_curso.php?id_curso=' . $row["id_curso"] . '\')" class="edit-btn">Editar</button>';
                            echo '<button onclick="deleteCourse(' . $row["id_curso"] . ')" class="delete-btn">Eliminar</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "No hay cursos.";
                    }
                    $conn->close();
                    ?>
                </div>
            </section>
        </div>
    </div>
    <script src="scripts/cursos.js"></script>
</body>

</html>