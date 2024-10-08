<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('admin');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios Asignados</title>
    <link rel="stylesheet" href="css/horarios_asignados.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Horarios Asignados</h1>
                </div>
                <div class="header-right">
                    <button onclick="window.location.href='horario.php'">Asignar Nuevo Horario</button>
                </div>
            </header>

            <section class="content">
                <?php
                if (isset($_SESSION['mensaje'])) {
                    echo "<div class='mensaje exito'>" . $_SESSION['mensaje'] . "</div>";
                    unset($_SESSION['mensaje']);
                }
                ?>
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar horarios..." onkeyup="filterHorarios()">
                </div>

                <div class="horario-list">
                    <?php
                    $sql = "SELECT h.id_horario, c.nombre_curso, CONCAT(u.nombre, ' ', u.apellido) as nombre_profesor, 
                           h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado, h.fecha_creacion
                            FROM horarios h 
                            JOIN cursos c ON h.id_curso = c.id_curso 
                            JOIN profesor p ON h.id_profesor = p.id_profesor 
                            JOIN usuario u ON p.id_usuario = u.id_usuario
                            ORDER BY h.fecha_creacion DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="horario-item" data-horario-id="' . $row["id_horario"] . '">';
                            echo '<div class="horario-details">';
                            echo '<h2>' . htmlspecialchars($row["nombre_curso"]) . '</h2>';
                            echo '<p>Profesor: ' . htmlspecialchars($row["nombre_profesor"]) . '</p>';
                            $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                            foreach ($dias as $index => $dia) {
                                $columna = strtolower($dia);
                                if (!empty($row[$columna])) {
                                    echo '<p>' . $dia . ': ' . htmlspecialchars($row[$columna]) . '</p>';
                                }
                            }
                            echo '</div>';
                            echo '<div class="horario-actions">';
                            echo '<button onclick="editHorario(' . $row["id_horario"] . ')" class="edit-btn">Editar</button>';
                            echo '<button onclick="deleteHorario(' . $row["id_horario"] . ')" class="delete-btn">Eliminar</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<div class='no-horarios-message'>";
                        echo "<div class='icon-text'>";
                        echo "<span class='material-icons-sharp'>info</span>";
                        echo "<p>No hay horarios asignados</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        function editHorario(horarioId) {
            window.location.href = 'editar_horario.php?id=' + horarioId;
        }

        function deleteHorario(horarioId) {
            if (confirm("¿Estás seguro de que quieres eliminar este horario?")) {
                fetch('eliminar_horario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id_horario=' + encodeURIComponent(horarioId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`[data-horario-id="${horarioId}"]`).remove();
                        alert('Horario eliminado exitosamente');
                    } else {
                        alert('Error al eliminar el horario: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al eliminar el horario');
                });
            }
        }

        function filterHorarios() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const horarioList = document.querySelector('.horario-list');
            const horarios = horarioList.getElementsByClassName('horario-item');

            for (let i = 0; i < horarios.length; i++) {
                const horarioDetails = horarios[i].getElementsByClassName('horario-details')[0];
                const textContent = horarioDetails.textContent || horarioDetails.innerText;
                if (textContent.toLowerCase().includes(filter)) {
                    horarios[i].style.display = '';
                } else {
                    horarios[i].style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>