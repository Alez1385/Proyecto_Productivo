<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_curso = $_POST['curso'];
    $id_profesor = $_POST['profesor'];
    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    
    $horarios = array_fill(0, 6, null);
    $conflicto = false;
    
    foreach ($dias as $index => $dia) {
        if (!empty($_POST["hora_inicio"][$dia]) && !empty($_POST["hora_fin"][$dia])) {
            $hora_inicio = $_POST["hora_inicio"][$dia];
            $hora_fin = $_POST["hora_fin"][$dia];
            
            // Verificar que la hora de inicio no sea igual a la hora de fin
            if ($hora_inicio === $hora_fin) {
                $conflicto = true;
                $error = "La hora de inicio y fin no pueden ser iguales para el día " . ucfirst($dia) . ".";
                break;
            }
            
            $horarios[$index] = $hora_inicio . " - " . $hora_fin;
            
            // Verificar disponibilidad antes de insertar
            $sql_check = "SELECT * FROM horarios 
                          WHERE id_profesor = ? 
                          AND $dia IS NOT NULL 
                          AND (
                              (SUBSTRING_INDEX($dia, ' - ', 1) < ? AND SUBSTRING_INDEX($dia, ' - ', -1) > ?)
                              OR (SUBSTRING_INDEX($dia, ' - ', 1) >= ? AND SUBSTRING_INDEX($dia, ' - ', 1) < ?)
                              OR (? >= SUBSTRING_INDEX($dia, ' - ', 1) AND ? < SUBSTRING_INDEX($dia, ' - ', -1))
                          )";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("issssss", $id_profesor, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $conflicto = true;
                $error = "El profesor ya tiene un horario asignado que se superpone en este período para el día " . ucfirst($dia) . ".";
                break;
            }
        }
    }
    
    if (!$conflicto) {
        $stmt = $conn->prepare("INSERT INTO horarios (id_curso, id_profesor, lunes, martes, miercoles, jueves, viernes, sabado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssss", $id_curso, $id_profesor, $horarios[0], $horarios[1], $horarios[2], $horarios[3], $horarios[4], $horarios[5]);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Horario guardado exitosamente.";
            header("Location: horarios_asignados.php");
            exit();
        } else {
            $error = "Error al guardar el horario: " . $conn->error;
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Horarios</title>
    <link rel="stylesheet" href="css/horario.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script type="module">
        import SidebarManager from '../../js/form_side.js';

        window.toggleSidebar = SidebarManager.toggle;
        window.openSidebar = SidebarManager.open;

        document.addEventListener('DOMContentLoaded', SidebarManager.init);
    </script>
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
        }
    </style>
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
                    <h1>Asignación de Horarios</h1>
                </div>
                <div class="header-right">
                    <button onclick="window.location.href='horarios_asignados.php'" class="btn-back">Volver a Horarios Asignados</button>
                </div>
            </header>

            <section class="content">
                <?php
                if (isset($error)) {
                    echo "<div class='mensaje error'>$error</div>";
                }
                ?>
                <form id="horarioForm" method="POST">
                    <div class="form-group">
                        <label for="curso">Curso:</label>
                        <select id="curso" name="curso" required>
                            <option value="">Seleccione un curso</option>
                            <?php
                            $sql_cursos = "SELECT id_curso, nombre_curso FROM cursos WHERE estado = 'activo'";
                            $result_cursos = $conn->query($sql_cursos);
                            while ($curso = $result_cursos->fetch_assoc()) {
                                echo "<option value='" . $curso['id_curso'] . "'>" . htmlspecialchars($curso['nombre_curso']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="profesor">Profesor:</label>
                        <select id="profesor" name="profesor" required>
                            <option value="">Seleccione un profesor</option>
                            <?php
                            $sql_profesores = "SELECT p.id_profesor, u.nombre, u.apellido FROM profesor p JOIN usuario u ON p.id_usuario = u.id_usuario";
                            $result_profesores = $conn->query($sql_profesores);
                            while ($profesor = $result_profesores->fetch_assoc()) {
                                echo "<option value='" . $profesor['id_profesor'] . "'>" . htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Horario:</label>
                        <?php
                        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                        foreach ($dias as $dia) {
                            echo "<div class='dia-horario'>";
                            echo "<label>" . ucfirst($dia) . ":</label>";
                            echo "<input type='time' name='hora_inicio[$dia]' id='hora_inicio_$dia' placeholder='Hora inicio'>";
                            echo "<input type='time' name='hora_fin[$dia]' id='hora_fin_$dia' placeholder='Hora fin'>";
                            echo "<div class='error-message' id='error_$dia'></div>";
                            echo "</div>";
                        }
                        ?>
                    </div>

                    <button type="submit" class="btn-submit" id="submitButton">Guardar Horario</button>
                </form>
            </section>
        </div>
    </div>

    <script>
        document.getElementById('overlay').addEventListener('click', toggleSidebar);
        
        document.addEventListener('DOMContentLoaded', function() {
            const profesorSelect = document.getElementById('profesor');
            const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
            const submitButton = document.getElementById('submitButton');
            let formValido = true;

            function verificarHorarios() {
                formValido = true;

                dias.forEach(dia => {
                    const horaInicio = document.getElementById(`hora_inicio_${dia}`).value;
                    const horaFin = document.getElementById(`hora_fin_${dia}`).value;
                    const errorElement = document.getElementById(`error_${dia}`);

                    if (horaInicio && horaFin) {
                        if (horaInicio === horaFin) {
                            errorElement.textContent = 'La hora de inicio y fin no pueden ser iguales.';
                            errorElement.style.display = 'block';
                            formValido = false;
                        } else {
                            errorElement.textContent = '';
                            errorElement.style.display = 'none';
                        }
                    }
                });

                return formValido;
            }

            function verificarDisponibilidad() {
                const idProfesor = profesorSelect.value;
                if (!idProfesor) return;

                if (!verificarHorarios()) {
                    submitButton.disabled = true;
                    return;
                }

                const promesas = dias.map(dia => {
                    const horaInicio = document.getElementById(`hora_inicio_${dia}`).value;
                    const horaFin = document.getElementById(`hora_fin_${dia}`).value;
                    if (horaInicio && horaFin) {
                        return fetch('verificar_disponibilidad.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_profesor=${idProfesor}&dia=${dia}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            const errorElement = document.getElementById(`error_${dia}`);
                            if (data.disponible) {
                                errorElement.textContent = '';
                                errorElement.style.display = 'none';
                            } else {
                                errorElement.textContent = 'El profesor ya tiene un horario asignado que se superpone en este período.';
                                errorElement.style.display = 'block';
                                formValido = false;
                            }
                        });
                    }
                    return Promise.resolve();
                });

                Promise.all(promesas).then(() => {
                    submitButton.disabled = !formValido;
                });
            }

            profesorSelect.addEventListener('change', verificarDisponibilidad);
            dias.forEach(dia => {
                document.getElementById(`hora_inicio_${dia}`).addEventListener('change', verificarDisponibilidad);
                document.getElementById(`hora_fin_${dia}`).addEventListener('change', verificarDisponibilidad);
            });

            // Verificar disponibilidad inicial
            verificarDisponibilidad();

            // Prevenir envío del formulario si no es válido
            document.getElementById('horarioForm').addEventListener('submit', function(event) {
                if (!verificarHorarios() || !formValido) {
                    event.preventDefault();
                    alert('Por favor, corrija los errores en los horarios antes de guardar.');
                }
            });
        });
    </script>
</body>

</html>