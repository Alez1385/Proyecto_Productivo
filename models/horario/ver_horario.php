<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

// Debugging: Imprimir información de la sesión
error_log(print_r($_SESSION, true));

// Obtener el ID del usuario actual
$id_usuario = $_SESSION['id_usuario'] ?? null; // Cambiado de 'user_id' a 'id_usuario'

if (!$id_usuario) {
    error_log("ID de usuario no encontrado en la sesión");
    // Redirigir al usuario a la página de inicio de sesión o mostrar un error
    header('Location: ' . BASE_URL . 'login/login.php');
    exit;
}

// Determinar el rol del usuario
$es_admin = checkPermission('admin', false);
$es_profesor = checkPermission('profesor', false);
$es_estudiante = !$es_admin && !$es_profesor;

// Debugging: Imprimir roles
error_log("Es admin: " . ($es_admin ? 'Sí' : 'No'));
error_log("Es profesor: " . ($es_profesor ? 'Sí' : 'No'));
error_log("Es estudiante: " . ($es_estudiante ? 'Sí' : 'No'));

// Obtener el ID específico según el rol
$id_especifico = null;
if ($es_estudiante) {
    $stmt = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $id_especifico = $row['id_estudiante'];
    }
    $stmt->close();
} elseif ($es_profesor) {
    $stmt = $conn->prepare("SELECT id_profesor FROM profesor WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $id_especifico = $row['id_profesor'];
    }
    $stmt->close();
}

// Debugging: Imprimir ID específico
error_log("ID específico: " . $id_especifico);

// Construir la consulta SQL base
$sql = "SELECT h.*, c.nombre_curso, CONCAT(u.nombre, ' ', u.apellido) as nombre_profesor 
        FROM horarios h 
        JOIN cursos c ON h.id_curso = c.id_curso 
        JOIN profesor p ON h.id_profesor = p.id_profesor 
        JOIN usuario u ON p.id_usuario = u.id_usuario";

// Modificar la consulta según el rol
if ($es_estudiante) {
    $sql .= " JOIN inscripciones i ON h.id_curso = i.id_curso WHERE i.id_estudiante = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_especifico);
} elseif ($es_profesor) {
    $sql .= " WHERE h.id_profesor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_especifico);
} else {
    // Para administradores, mostrar todos los horarios
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
$horarios = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Debugging: Imprimir número de horarios obtenidos
error_log("Número de horarios obtenidos: " . count($horarios));

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario</title>
    <link rel="stylesheet" href="css/horario.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script type="module">
        import SidebarManager from '../../js/form_side.js';

        window.toggleSidebar = SidebarManager.toggle;
        window.openSidebar = SidebarManager.open;

        document.addEventListener('DOMContentLoaded', SidebarManager.init);
    </script>
    <style>
        .horario-grid {
            display: grid;
            grid-template-columns: auto repeat(6, 1fr);
            gap: 10px;
            margin-top: 20px;
        }
        .horario-grid > div {
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            text-align: center;
        }
        .horario-grid .header {
            font-weight: bold;
            background-color: #e0e0e0;
        }
        .curso-info {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 5px;
            margin: 2px 0;
            font-size: 0.9em;
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
                    <h1>Horario</h1>
                </div>
                <?php if ($es_admin): ?>
                <div class="header-right">
                    <button onclick="window.location.href='horarios_asignados.php'" class="btn-back">Volver a Horarios Asignados</button>
                </div>
                <?php endif; ?>
            </header>

            <section class="content">
                <div class="horario-grid">
                    <div class="header">Hora</div>
                    <div class="header">Lunes</div>
                    <div class="header">Martes</div>
                    <div class="header">Miércoles</div>
                    <div class="header">Jueves</div>
                    <div class="header">Viernes</div>
                    <div class="header">Sábado</div>
                    
                    <?php
                    $horas = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
                    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                    
                    foreach ($horas as $hora) {
                        echo "<div>{$hora}</div>";
                        foreach ($dias as $dia) {
                            echo "<div>";
                            foreach ($horarios as $horario) {
                                if (!empty($horario[$dia])) {
                                    list($inicio, $fin) = explode(' - ', $horario[$dia]);
                                    if ($inicio <= $hora && $hora < $fin) {
                                        echo "<div class='curso-info'>";
                                        echo "{$horario['nombre_curso']}<br>";
                                        echo "{$horario['nombre_profesor']}<br>";
                                        echo "{$horario[$dia]}";
                                        echo "</div>";
                                    }
                                }
                            }
                            echo "</div>";
                        }
                    }
                    ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.getElementById('overlay').addEventListener('click', toggleSidebar);
    </script>
</body>
</html>