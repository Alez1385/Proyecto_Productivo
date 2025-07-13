<?php
// Include necessary files and perform initial checks
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a professor
if (!isset($_SESSION['username']) || !checkPermission('profesor')) {
    error_log("Intento de acceso no autorizado al dashboard de profesor. Usuario: " . ($_SESSION['username'] ?? 'No definido') . ", Tipo de usuario: " . ($_SESSION['user_type'] ?? 'No definido'));
    echo '<h2 class="no-tienes-permiso">No tienes permiso para acceder a esta página.</h2>';
    exit;
}

// Obtener información básica del usuario
$id_usuario = $_SESSION['id_usuario'];
$sql_user = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $id_usuario);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Mostrar notificación de perfil incompleto universal
include '../scripts/profile_notification.php';

// Verificar si el usuario necesita completar sus datos
$datos_incompletos = false;
$campos_faltantes = [];
if (isset($user['perfil_incompleto']) && $user['perfil_incompleto'] == 1) {
    $datos_incompletos = true;
    if (empty($user['nombre'])) $campos_faltantes[] = 'nombre';
    if (empty($user['apellido'])) $campos_faltantes[] = 'apellido';
    if (empty($user['telefono'])) $campos_faltantes[] = 'teléfono';
    if (empty($user['direccion'])) $campos_faltantes[] = 'dirección';
    if (empty($user['fecha_nac'])) $campos_faltantes[] = 'fecha de nacimiento';
    if (empty($campos_faltantes)) $campos_faltantes[] = 'información personal';
}
?>
<style>
.profile-notification {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    border-left: 5px solid #ff4757;
    position: relative;
    overflow: hidden;
}
.profile-notification h3 {margin:0 0 10px 0;font-size:18px;font-weight:600;display:flex;align-items:center;gap:10px;}
.profile-notification p {margin:0 0 15px 0;font-size:14px;opacity:0.9;}
.profile-notification .missing-fields {background:rgba(255,255,255,0.2);padding:10px;border-radius:8px;margin:10px 0;font-size:13px;}
.profile-notification .btn-complete-profile {background:rgba(255,255,255,0.2);color:white;border:2px solid rgba(255,255,255,0.3);padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;transition:all 0.3s ease;display:inline-flex;align-items:center;gap:8px;}
.profile-notification .btn-complete-profile:hover {background:rgba(255,255,255,0.3);transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,0.2);}
.profile-notification .close-notification {position:absolute;top:15px;right:15px;background:none;border:none;color:white;font-size:20px;cursor:pointer;opacity:0.7;transition:opacity 0.3s ease;}
.profile-notification .close-notification:hover {opacity:1;}
</style>
<?php if ($datos_incompletos): ?>
<div class="profile-notification" id="profile-notification">
    <button class="close-notification" onclick="closeNotification()">&times;</button>
    <h3><i class="fas fa-exclamation-triangle"></i> ¡Completa tu perfil!</h3>
    <p>Para una mejor experiencia, necesitamos que completes algunos datos de tu perfil:</p>
    <div class="missing-fields"><strong>Campos faltantes:</strong> <?php echo implode(', ', $campos_faltantes); ?></div>
    <a href="../models/perfil/perfil.php" class="btn-complete-profile"><i class="fas fa-user-edit"></i>Completar Perfil</a>
</div>
<script>
function closeNotification() {
    const notification = document.getElementById('profile-notification');
    if (notification) notification.style.display = 'none';
    localStorage.setItem('profileNotificationClosed', Date.now());
}
document.addEventListener('DOMContentLoaded', function() {
    const lastClosed = localStorage.getItem('profileNotificationClosed');
    if (lastClosed) {
        const timeDiff = Date.now() - parseInt(lastClosed);
        if (timeDiff < 24 * 60 * 60 * 1000) {
            const notification = document.getElementById('profile-notification');
            if (notification) notification.style.display = 'none';
        }
    }
});
</script>
<?php endif; ?>

<section class="professor-dashboard">
    <!-- Div para mostrar errores -->
    <div id="error-message" style="display:none;"></div>

    <div class="dashboard-summary">
        <div class="summary-card">
            <i class="fas fa-chalkboard-teacher"></i>
            <h3>Cursos Asignados</h3>
            <p id="cursos-count">0</p>
        </div>
        <div class="summary-card">
            <i class="fas fa-user-graduate"></i>
            <h3>Total Estudiantes</h3>
            <p id="estudiantes-count">0</p>
        </div>
        <div class="summary-card">
            <i class="fas fa-clock"></i>
            <h3>Horas de Clase</h3>
            <p id="horas-clase">0</p>
        </div>
    </div>

    <div class="dashboard-charts">
        <div class="chart-container">
            <h3>Distribución de Estudiantes por Curso</h3>
            <canvas id="estudiantesPorCursoChart"></canvas>
        </div>
    </div>

    <div class="cursos-asignados">
        <h2>Mis Cursos Asignados</h2>
        <div id="cursos-asignados-list" class="cursos-asignados-list">
            <!-- Cursos asignados will be dynamically inserted here -->
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/dashboard_profesor_updater.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        dashboardProfesorUpdater.init();
    });
</script>
