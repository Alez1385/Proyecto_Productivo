<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <link rel="stylesheet" href="cursos.css">
</head>

<body>
    <div class="dashboard-container">
        <?php
        include "../../scripts/sidebar.php";
        include "../../scripts/conexion.php";

        // Definir los filtros iniciales
        $selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
        $selectedStatus = isset($_GET['status']) ? $_GET['status'] : '';
        $selectedLevel = isset($_GET['level']) ? $_GET['level'] : '';

        // Consulta para obtener los cursos con información adicional y aplicar filtros
        $sql = "SELECT c.id_curso, c.nombre_curso, c.descripcion, c.nivel_educativo, c.duracion, c.estado, c.categoria, c.icono,
                       u.nombre AS nombre_profesor, u.apellido AS apellido_profesor,
                       GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios,
                       COUNT(DISTINCT i.id_estudiante) AS num_estudiantes
                FROM cursos c
                LEFT JOIN asignacion_curso ac ON c.id_curso = ac.id_curso
                LEFT JOIN profesor p ON ac.id_profesor = p.id_profesor
                LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
                LEFT JOIN horarios h ON c.id_curso = h.id_curso
                LEFT JOIN inscripciones i ON c.id_curso = i.id_curso
                WHERE (c.categoria LIKE '%$selectedCategory%' OR '$selectedCategory' = '')
                  AND (c.estado LIKE '%$selectedStatus%' OR '$selectedStatus' = '')
                  AND (c.nivel_educativo LIKE '%$selectedLevel%' OR '$selectedLevel' = '')
                GROUP BY c.id_curso";
        $result = $conn->query($sql);

        // Cargar opciones de filtro
        $sql_categories = "SELECT DISTINCT categoria FROM cursos";
        $categories = $conn->query($sql_categories);

        $sql_states = "SELECT DISTINCT estado FROM cursos";
        $states = $conn->query($sql_states);

        $sql_levels = "SELECT DISTINCT nivel_educativo FROM cursos";
        $levels = $conn->query($sql_levels);
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Control de Cursos</h1>
                </div>
                <div class="header-right">
                    <button class="add-course-btn" onclick="window.location.href='../categoria_curso/modificar_categorias.php'">Modificar Categorías</button>
                    <button class="add-course-btn" onclick="window.location.href='crear_curso.php'">+ Añadir nuevo curso</button>
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
                        <?php while ($row = $categories->fetch_assoc()) { ?>
                            <option value="<?php echo $row["categoria"]; ?>" <?php echo $selectedCategory === $row["categoria"] ? 'selected' : ''; ?>>
                                <?php echo $row["categoria"]; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <select id="statusFilter" onchange="filterCourses()">
                        <option value="">Estado</option>
                        <?php while ($row = $states->fetch_assoc()) { ?>
                            <option value="<?php echo $row["estado"]; ?>" <?php echo $selectedStatus === $row["estado"] ? 'selected' : ''; ?>>
                                <?php echo $row["estado"]; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <select id="levelFilter" onchange="filterCourses()">
                        <option value="">Nivel Educativo</option>
                        <?php while ($row = $levels->fetch_assoc()) { ?>
                            <option value="<?php echo $row["nivel_educativo"]; ?>" <?php echo $selectedLevel === $row["nivel_educativo"] ? 'selected' : ''; ?>>
                                <?php echo $row["nivel_educativo"]; ?>
                            </option>
                        <?php } ?>
                    </select>

                    

                    <!-- Filtros aplicados -->
                    <div id="appliedFilters">
                        <?php if ($selectedCategory) { ?>
                            <div class="filter-tag" data-filter="category">
                                <span><?php echo $selectedCategory; ?></span>
                                <span class="filter-close" onclick="removeFilter('category')">&times;</span>
                            </div>
                        <?php } ?>
                        <?php if ($selectedStatus) { ?>
                            <div class="filter-tag" data-filter="status">
                                <span><?php echo $selectedStatus; ?></span>
                                <span class="filter-close" onclick="removeFilter('status')">&times;</span>
                            </div>
                        <?php } ?>
                        <?php if ($selectedLevel) { ?>
                            <div class="filter-tag" data-filter="level">
                                <span><?php echo $selectedLevel; ?></span>
                                <span class="filter-close" onclick="removeFilter('level')">&times;</span>
                            </div>
                        <?php } ?>
                        <button id="resetFilters" onclick="resetFilters()">Resetear Filtros</button>
                    </div>
                </div>

                <!-- Lista de cursos -->
                <div class="course-list">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="course-item" data-course-id="' . $row["id_curso"] . '">';
                            echo '<img src="../../uploads/icons/' . $row["icono"] . '" alt="Course Image">';
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
                            echo '<button onclick="window.location.href=\'editar_curso.php?id_curso=' . $row["id_curso"] . '\'" class="edit-btn">Editar</button>';
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
