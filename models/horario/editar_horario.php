<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('admin');

$id_horario = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_curso = $_POST['curso'];
    $id_profesor = $_POST['profesor'];
    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    
    $sql = "UPDATE horarios SET id_curso = ?, id_profesor = ?";
    $params = [$id_curso, $id_profesor];
    $types = "ii";
    
    foreach ($dias as $dia) {
        $sql .= ", $dia = ?";
        if (!empty($_POST["hora_inicio"][$dia]) && !empty($_POST["hora_fin"][$dia])) {
            $hora_inicio = $_POST["hora_inicio"][$dia];
            $hora_fin = $_POST["hora_fin"][$dia];
            
            // Validar que las horas estén dentro del rango permitido
            if (strtotime($hora_inicio) < strtotime('06:00') || strtotime($hora_fin) > strtotime('20:00')) {
                $error = "Las horas deben estar entre las 6:00 AM y las 8:00 PM para el día " . ucfirst($dia) . ".";
                break;
            }
            
            $params[] = $hora_inicio . " - " . $hora_fin;
            $types .= "s";
        } else {
            $params[] = null;
            $types .= "s";
        }
    }
    
    if (!isset($error)) {
        $sql .= " WHERE id_horario = ?";
        $params[] = $id_horario;
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Horario actualizado exitosamente.";
            header("Location: horarios_asignados.php");
            exit();
        } else {
            $error = "Error al actualizar el horario: " . $conn->error;
        }
        
        $stmt->close();
    }
}

// Cargar el horario existente
$sql = "SELECT * FROM horarios WHERE id_horario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_horario);
$stmt->execute();
$result = $stmt->get_result();
$horario = $result->fetch_assoc();
$stmt->close();

if (!$horario) {
    die("Horario no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horario</title>
    <link rel="stylesheet" href="css/horario.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <style>
        .error-message {
            color: red;
            display: none;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Editar Horario</h1>
                </div>
                <div class="header-right">
                    <button onclick="window.location.href='horarios_asignados.php'" class="btn-back">Volver a Horarios Asignados</button>
                </div>
            </header>

            <section class="content">
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
                <p><strong>Nota:</strong> Los horarios deben estar entre las 6:00 AM y las 8:00 PM.</p>
                <form id="editarHorarioForm" method="POST">
                    <div class="form-group">
                        <label for="curso">Curso:</label>
                        <select id="curso" name="curso" required>
                            <?php
                            $sql_cursos = "SELECT id_curso, nombre_curso FROM cursos WHERE estado = 'activo'";
                            $result_cursos = $conn->query($sql_cursos);
                            while ($curso = $result_cursos->fetch_assoc()) {
                                $selected = ($curso['id_curso'] == $horario['id_curso']) ? 'selected' : '';
                                echo "<option value='" . $curso['id_curso'] . "' $selected>" . htmlspecialchars($curso['nombre_curso']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="profesor">Profesor:</label>
                        <select id="profesor" name="profesor" required>
                            <?php
                            $sql_profesores = "SELECT p.id_profesor, u.nombre, u.apellido FROM profesor p JOIN usuario u ON p.id_usuario = u.id_usuario";
                            $result_profesores = $conn->query($sql_profesores);
                            while ($profesor = $result_profesores->fetch_assoc()) {
                                $selected = ($profesor['id_profesor'] == $horario['id_profesor']) ? 'selected' : '';
                                echo "<option value='" . $profesor['id_profesor'] . "' $selected>" . htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Horario:</label>
                        <?php
                        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                        foreach ($dias as $dia) {
                            $horario_dia = isset($horario[$dia]) ? explode(' - ', $horario[$dia]) : ['', ''];
                            echo "<div class='dia-horario'>";
                            echo "<label>" . ucfirst($dia) . ":</label>";
                            echo "<input type='time' name='hora_inicio[$dia]' value='" . htmlspecialchars($horario_dia[0]) . "' min='06:00' max='20:00'>";
                            echo "<input type='time' name='hora_fin[$dia]' value='" . htmlspecialchars($horario_dia[1]) . "' min='06:00' max='20:00'>";
                            echo "<div class='error-message' id='error_$dia'></div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                    <button type="submit" class="btn-submit" id="submitButton">Actualizar Horario</button>
                </form>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profesorSelect = document.getElementById('profesor');
            const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
            const idHorario = <?php echo $id_horario; ?>;
            const submitButton = document.getElementById('submitButton');
            let formValido = true;

            function verificarDisponibilidad() {
                const idProfesor = profesorSelect.value;
                const promesas = dias.map(dia => {
                    const horaInicio = document.querySelector(`input[name="hora_inicio[${dia}]"]`).value;
                    const horaFin = document.querySelector(`input[name="hora_fin[${dia}]"]`).value;
                    if (horaInicio && horaFin) {
                        // Validar rango de horas
                        if (horaInicio < "06:00" || horaFin > "20:00") {
                            document.getElementById(`error_${dia}`).textContent = 'Las horas deben estar entre las 6:00 AM y las 8:00 PM.';
                            document.getElementById(`error_${dia}`).style.display = 'block';
                            formValido = false;
                            return Promise.resolve();
                        }

                        return fetch('verificar_disponibilidad.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_profesor=${idProfesor}&dia=${dia}&hora_inicio=${horaInicio}&hora_fin=${horaFin}&id_horario=${idHorario}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            const errorElement = document.getElementById(`error_${dia}`);
                            if (data.disponible) {
                                errorElement.textContent = '';
                                errorElement.style.display = 'none';
                            } else {
                                errorElement.textContent = 'El profesor ya tiene un horario asignado en este período.';
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
                document.querySelector(`input[name="hora_inicio[${dia}]"]`).addEventListener('change', verificarDisponibilidad);
                document.querySelector(`input[name="hora_fin[${dia}]"]`).addEventListener('change', verificarDisponibilidad);
            });

            // Verificar disponibilidad inicial
            verificarDisponibilidad();

            // Prevenir envío del formulario si no es válido
            document.getElementById('editarHorarioForm').addEventListener('submit', function(event) {
                if (!formValido) {
                    event.preventDefault();
                    alert('Por favor, corrija los horarios en conflicto antes de actualizar.');
                }
            });
        });
    </script>
</body>
</html>