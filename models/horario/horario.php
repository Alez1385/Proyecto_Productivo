<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_curso = $_POST['curso'];
    $id_profesor = $_POST['profesor'];
    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    
    // Verificar si el curso ya tiene un horario asignado
    $sql_check = "SELECT id_horario FROM horarios WHERE id_curso = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_curso);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        $error = "Este curso ya tiene un horario asignado.";
    } else {
        $horarios = array_fill(0, 6, null);
        $conflicto = false;
        $error = "";
        
        foreach ($dias as $index => $dia) {
            if (!empty($_POST["hora_inicio"][$dia]) && !empty($_POST["hora_fin"][$dia])) {
                $hora_inicio = $_POST["hora_inicio"][$dia];
                $hora_fin = $_POST["hora_fin"][$dia];
                
                // Validar que las horas estén dentro del rango permitido
                if (strtotime($hora_inicio) < strtotime('06:00') || strtotime($hora_fin) > strtotime('20:00')) {
                    $conflicto = true;
                    $error = "Las horas deben estar entre las 6:00 AM y las 8:00 PM para el día " . ucfirst($dia) . ".";
                    break;
                }
                
                // Verificar que la hora de inicio sea menor que la hora de fin
                if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
                    $conflicto = true;
                    $error = "La hora de inicio debe ser menor que la hora de fin para el día " . ucfirst($dia) . ".";
                    break;
                }
                
                $horarios[$index] = $hora_inicio . " - " . $hora_fin;
                
                // Verificar conflicto de horarios
                $sql = "SELECT * FROM horarios WHERE id_profesor = ? AND $dia IS NOT NULL";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_profesor);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    list($h_inicio, $h_fin) = explode(" - ", $row[$dia]);
                    if ((strtotime($hora_inicio) >= strtotime($h_inicio) && strtotime($hora_inicio) < strtotime($h_fin)) ||
                        (strtotime($hora_fin) > strtotime($h_inicio) && strtotime($hora_fin) <= strtotime($h_fin)) ||
                        (strtotime($hora_inicio) <= strtotime($h_inicio) && strtotime($hora_fin) >= strtotime($h_fin))) {
                        $conflicto = true;
                        $error = "Conflicto de horario para el profesor en el día " . ucfirst($dia) . ".";
                        break 2;
                    }
                }
            }
        }
        
        if (!$conflicto) {
            $sql = "INSERT INTO horarios (id_curso, id_profesor, lunes, martes, miercoles, jueves, viernes, sabado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iissssss", $id_curso, $id_profesor, $horarios[0], $horarios[1], $horarios[2], $horarios[3], $horarios[4], $horarios[5]);
            
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Horario creado exitosamente.";
                header("Location: horarios_asignados.php");
                exit();
            } else {
                $error = "Error al crear el horario: " . $conn->error;
            }
        }
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
</head>
<body>
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
                <h2>Asignación de Horarios</h2>
                <p><strong>Nota:</strong> Los horarios deben estar entre las 6:00 AM y las 8:00 PM.</p>
                
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form id="horarioForm" method="POST">
                    <div class="form-group">
                        <label for="curso">Curso:</label>
                        <select id="curso" name="curso" required>
                            <option value="">Seleccione un curso</option>
                            <?php
                            $sql_cursos = "SELECT c.id_curso, c.nombre_curso 
                                           FROM cursos c
                                           LEFT JOIN horarios h ON c.id_curso = h.id_curso
                                           WHERE c.estado = 'activo' AND h.id_horario IS NULL";
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
                            echo "<input type='time' name='hora_inicio[$dia]' min='06:00' max='20:00'>";
                            echo "<input type='time' name='hora_fin[$dia]' min='06:00' max='20:00'>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                    <button type="submit" class="btn-submit">Crear Horario</button>
                </form>
            </section>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('horarioForm');
        form.addEventListener('submit', function(event) {
            const inputs = form.querySelectorAll('input[type="time"]');
            let isValid = true;

            inputs.forEach(function(input) {
                if (input.value) {
                    const time = input.value;
                    if (time < "06:00" || time > "20:00") {
                        isValid = false;
                        input.setCustomValidity('Las horas deben estar entre las 6:00 AM y las 8:00 PM.');
                    } else {
                        input.setCustomValidity('');
                    }
                }
            });

            if (!isValid) {
                event.preventDefault();
            }
            const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        dias.forEach(function(dia) {
            const inicioInput = document.querySelector(`input[name="hora_inicio[${dia}]"]`);
            const finInput = document.querySelector(`input[name="hora_fin[${dia}]"]`);
        });

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

        // Agregar validación de rango de horas
        document.getElementById('horarioForm').addEventListener('submit', function(event) {
            var inputs = this.getElementsByTagName('input');
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type === 'time') {
                    var time = inputs[i].value;
                    if (time < "06:00" || time > "20:00") {
                        alert('Las horas deben estar entre las 6:00 AM y las 8:00 PM.');
                        event.preventDefault();
                        return;
                    }
                }
            }
        });

        // Agregar validación adicional para verificar si el curso ya tiene horario
        document.getElementById('horarioForm').addEventListener('submit', function(event) {
            const cursoSelect = document.getElementById('curso');
            if (cursoSelect.options.length === 0) {
                alert('No hay cursos disponibles para asignar horario.');
                event.preventDefault();
                return;
            }
        });

        finInput.addEventListener('change', function() {
                if (inicioInput.value && finInput.value) {
                    if (inicioInput.value >= finInput.value) {
                        finInput.setCustomValidity('La hora de fin debe ser mayor que la hora de inicio.');
                    } else {
                        finInput.setCustomValidity('');
                    }
                }
            });
        });
    });
    </script>
</body>
</html>