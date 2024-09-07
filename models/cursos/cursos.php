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
            require_once '../../scripts/filters.php';
        } catch (Exception $e) {
            logError("Error al incluir archivos necesarios: " . $e->getMessage());
            $_SESSION['error'] = "Error al cargar componentes necesarios. Por favor, contacte al administrador.";
            header("Location: error.php");
            exit();
        }

        // Obtener filtros desde la URL
        $course_filters = [
            'nombre_curso' => isset($_GET['nombre_curso']) ? $_GET['nombre_curso'] : '',
            'nivel_educativo' => isset($_GET['nivel_educativo']) ? $_GET['nivel_educativo'] : '',
            'categoria' => isset($_GET['categoria']) ? $_GET['categoria'] : ''
        ];

        // Crear instancia de Filter para cursos
        $courseFilter = new Filter($conn, 'cursos', $course_filters);

        // Aplicar filtros y obtener resultados
        try {
            $result = $courseFilter->applyFilters();
            if (!$result) {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            logError("Error en la consulta SQL: " . $e->getMessage());
            $_SESSION['error'] = "Error al obtener la lista de cursos. Por favor, inténtelo de nuevo más tarde.";
            $result = false;
        }
        ?>
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
                if (isset($_SESSION['error'])) {
                    echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>

                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar cursos..." onkeyup="filterCourses()">

                    <div class="filter-dropdown">
                        <button class="filter-button" onclick="toggleFilters()">Filtros</button>
                        <div class="filter-content" id="filterContent">
                            <div class="filter-category">
                                <div class="filter-category-title">Categorías</div>
                                <label class="filter-option"><input type="checkbox" value="artwork"> Artwork</label>
                                <label class="filter-option"><input type="checkbox" value="game-engine"> Game Engine</label>
                                <label class="filter-option"><input type="checkbox" value="support"> Support</label>
                                <label class="filter-option"><input type="checkbox" value="jobs"> Jobs</label>
                                <label class="filter-option"><input type="checkbox" value="coding"> Coding</label>
                                <label class="filter-option"><input type="checkbox" value="general-forums"> General Forums</label>
                                <label class="filter-option"><input type="checkbox" value="contests"> Contests</label>
                                <label class="filter-option"><input type="checkbox" value="promotions"> Promotions</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-tags" id="filterTags"></div>

                <div class="course-list">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="course-item" data-course-id="' . htmlspecialchars($row["id_curso"]) . '" data-category="' . htmlspecialchars($row["categoria"]) . '">';
                            echo '<div class="course-icon-container">';
                            if (!empty($row["icono"])) {
                                echo '<img src="../../uploads/icons/' . htmlspecialchars($row["icono"]) . '" alt=" " class="course-icon">';
                            } else {
                                echo '<span class="material-icons-sharp">school</span>';
                            }
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

    <script src="./scripts/cursos.js"></script>
</body>

</html>