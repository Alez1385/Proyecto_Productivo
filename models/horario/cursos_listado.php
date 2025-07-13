<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

// Determinar el rol del usuario
$es_admin = checkPermission('admin', false);
$es_profesor = checkPermission('profesor', false);
$es_estudiante = !$es_admin && !$es_profesor;

// Obtener el ID del usuario
$id_usuario = $_SESSION['id_usuario'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Cursos</title>
    <link rel="stylesheet" href="css/cursos_listado.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Listado de Cursos</h1>
                </div>
            </header>

            <section class="content">
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar cursos..." onkeyup="filterCursos()">
                </div>

                <div class="curso-list">
                    <?php
                    if ($es_estudiante) {
                        $sql = "SELECT DISTINCT c.id_curso, c.nombre_curso, c.descripcion, c.nivel_educativo, c.duracion, cc.nombre_categoria 
                                FROM cursos c 
                                LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
                                JOIN inscripciones i ON c.id_curso = i.id_curso
                                WHERE i.id_estudiante = (SELECT id_estudiante FROM estudiante WHERE id_usuario = ?)
                                GROUP BY c.id_curso
                                ORDER BY c.nombre_curso ASC";
                    } elseif ($es_profesor) {
                        $sql = "SELECT DISTINCT c.id_curso, c.nombre_curso, c.descripcion, c.nivel_educativo, c.duracion, cc.nombre_categoria 
                                FROM cursos c 
                                LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
                                LEFT JOIN horarios h ON c.id_curso = h.id_curso
                                LEFT JOIN profesor p ON h.id_profesor = p.id_profesor OR c.id_profesor = p.id_profesor
                                WHERE p.id_usuario = ?
                                GROUP BY c.id_curso
                                ORDER BY c.nombre_curso ASC";
                    }

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id_usuario);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="curso-item" data-curso-id="' . $row["id_curso"] . '">';
                            echo '<div class="curso-details">';
                            echo '<h2>' . htmlspecialchars($row["nombre_curso"]) . '</h2>';
                            echo '<p><strong>Descripción:</strong> ' . htmlspecialchars($row["descripcion"]) . '</p>';
                            echo '<p><strong>Nivel:</strong> ' . htmlspecialchars($row["nivel_educativo"]) . '</p>';
                            echo '<p><strong>Duración:</strong> ' . htmlspecialchars($row["duracion"]) . '</p>';
                            echo '<p><strong>Categoría:</strong> ' . htmlspecialchars($row["nombre_categoria"]) . '</p>';
                            echo '</div>';
                            echo '<div class="curso-actions">';
                            echo '<button onclick="verHorario(' . $row["id_curso"] . ')" class="ver-horario-btn">Ver Horario</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<div class='no-cursos-message'>";
                        echo "<div class='icon-text'>";
                        echo "<span class='material-icons-sharp'>info</span>";
                        echo "<p>No tienes cursos asignados actualmente.</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        function verHorario(cursoId) {
            window.location.href = 'ver_horario.php?id_curso=' + cursoId;
        }

        function filterCursos() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const cursoList = document.querySelector('.curso-list');
            const cursos = cursoList.getElementsByClassName('curso-item');

            for (let i = 0; i < cursos.length; i++) {
                const cursoDetails = cursos[i].getElementsByClassName('curso-details')[0];
                const textContent = cursoDetails.textContent || cursoDetails.innerText;
                if (textContent.toLowerCase().includes(filter)) {
                    cursos[i].style.display = '';
                } else {
                    cursos[i].style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
