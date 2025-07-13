<?php
// Include necessary files and perform initial checks
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a user type
if (!isset($_SESSION['username']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'user') {
    echo '<h2 class="no-tienes-permiso">No tienes permiso para acceder a esta página.</h2>';
    exit;
}

// Obtener información básica del usuario
$id_usuario = $_SESSION['id_usuario'];

// Obtener información del usuario de forma simple
$sql_user = "SELECT u.*, tu.nombre as tipo_nombre
              FROM usuario u
              INNER JOIN tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
              WHERE u.id_usuario = ?";
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
if (
    empty($user['nombre']) ||
    empty($user['apellido']) ||
    empty($user['telefono']) ||
    empty($user['direccion']) ||
    empty($user['fecha_nac']) ||
    (isset($user['perfil_incompleto']) && $user['perfil_incompleto'] == 1)
) {
    $datos_incompletos = true;
    if (empty($user['nombre'])) $campos_faltantes[] = 'nombre';
    if (empty($user['apellido'])) $campos_faltantes[] = 'apellido';
    if (empty($user['telefono'])) $campos_faltantes[] = 'teléfono';
    if (empty($user['direccion'])) $campos_faltantes[] = 'dirección';
    if (empty($user['fecha_nac'])) $campos_faltantes[] = 'fecha de nacimiento';
    if (empty($campos_faltantes)) $campos_faltantes[] = 'información personal';
}

// Obtener cursos disponibles (sin LIMIT)
$sql_cursos = "SELECT c.*, cc.nombre_categoria
               FROM cursos c
               LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
               WHERE c.estado = 'activo'
               ORDER BY c.nombre_curso";
$result_cursos = $conn->query($sql_cursos);
$cursos_disponibles = [];
while ($row = $result_cursos->fetch_assoc()) {
    $cursos_disponibles[] = $row;
}

// Resumen (puedes personalizar los valores si quieres)
$dias_registrado = 0;
if (!empty($user['fecha_registro'])) {
    $fecha_registro = new DateTime($user['fecha_registro']);
    $hoy = new DateTime();
    $diferencia = $hoy->diff($fecha_registro);
    $dias_registrado = $diferencia->days;
}

// Restaurar sección de notificaciones de usuario de forma segura
$notificaciones = [];
$id_usuario = $_SESSION['id_usuario'] ?? null;
if ($id_usuario && isset($conn)) {
    // Verificar si la tabla existe antes de consultar
    $check = $conn->query("SHOW TABLES LIKE 'notificaciones_user'");
    if ($check && $check->num_rows > 0) {
        $sql_notif = "SELECT * FROM notificaciones_user WHERE id_usuario = ? ORDER BY fecha DESC LIMIT 20";
        $stmt_notif = $conn->prepare($sql_notif);
        if ($stmt_notif) {
            $stmt_notif->bind_param("i", $id_usuario);
            $stmt_notif->execute();
            $res_notif = $stmt_notif->get_result();
            while ($row = $res_notif->fetch_assoc()) {
                $notificaciones[] = $row;
            }
        }
    }
}
?>
<link rel="stylesheet" href="../dashboard/css/estudiante.css">
<style>
.dashboard-summary .summary-card {
    background: #fff !important;
    color: #333 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    border: 1px solid #eaeaea;
}
.dashboard-summary .summary-card i {
    color: #3498db !important;
}
.course-item {
    position: relative;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.course-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.course-item .inscribir-btn {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(76, 175, 80, 0.9);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.2em;
    font-weight: bold;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}
.course-item:hover .inscribir-btn {
    opacity: 1;
}
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}
.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    border-radius: 8px;
    text-align: center;
}
.modal-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
.modal-btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}
.modal-btn.rapida {
    background-color: #2196F3;
    color: white;
}
.modal-btn.completa {
    background-color: #4CAF50;
    color: white;
}
.modal-btn:hover {
    opacity: 0.8;
}
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}
.close:hover {
    color: #000;
}

/* Estilos para la notificación de datos incompletos */
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

.profile-notification::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.profile-notification h3 {
    margin: 0 0 10px 0;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-notification p {
    margin: 0 0 15px 0;
    font-size: 14px;
    opacity: 0.9;
}

.profile-notification .missing-fields {
    background: rgba(255,255,255,0.2);
    padding: 10px;
    border-radius: 8px;
    margin: 10px 0;
    font-size: 13px;
}

.profile-notification .btn-complete-profile {
    background: rgba(255,255,255,0.2);
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.profile-notification .btn-complete-profile:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.profile-notification .close-notification {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.profile-notification .close-notification:hover {
    opacity: 1;
}
</style>
<section class="student-dashboard">
    <div id="error-message" style="display:none;"></div>
    
    <?php if ($datos_incompletos): ?>
    <div class="profile-notification" id="profile-notification">
        <button class="close-notification" onclick="closeNotification()">&times;</button>
        <h3>
            <i class="fas fa-exclamation-triangle"></i>
            ¡Completa tu perfil!
        </h3>
        <p>Para una mejor experiencia, necesitamos que completes algunos datos de tu perfil:</p>
        <div class="missing-fields">
            <strong>Campos faltantes:</strong> <?php echo implode(', ', $campos_faltantes); ?>
        </div>
        <a href="../models/perfil/perfil.php" class="btn-complete-profile">
            <i class="fas fa-user-edit"></i>
            Completar Perfil
        </a>
    </div>
    <?php endif; ?>
    
    <h2 style="text-align:center;margin-bottom:24px;">Bienvenido<?php echo $user['nombre'] ? ', ' . htmlspecialchars($user['nombre']) : ''; ?> 👋</h2>
    <div class="dashboard-summary">
        <div class="summary-card">
            <i class="fas fa-user"></i>
            <h3>Perfil</h3>
            <p><?php echo htmlspecialchars($user['username']); ?></p>
        </div>
        <div class="summary-card">
            <i class="fas fa-calendar-alt"></i>
            <h3>Días Registrado</h3>
            <p><?php echo $dias_registrado; ?></p>
        </div>
    </div>

    <?php if (count($notificaciones) > 0): ?>
<div class="notificaciones-user" style="margin-bottom:30px;">
    <h2 style="font-size:1.3em;color:#2176ae;margin-bottom:10px;">Notificaciones</h2>
    <table style="width:100%;background:#f8fbff;border-radius:8px;box-shadow:0 2px 8px #eaf6fb;font-size:15px;">
        <thead>
            <tr style="background:#eaf6fb;color:#2176ae;">
                <th style="padding:8px 6px;">Título</th>
                <th style="padding:8px 6px;">Mensaje</th>
                <th style="padding:8px 6px;">Fecha</th>
                <th style="padding:8px 6px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notificaciones as $notif): ?>
            <tr style="background:<?= $notif['leido'] ? '#f8fbff' : '#d4edfa' ?>;">
                <td style="padding:8px 6px;"><strong><?= htmlspecialchars($notif['titulo']) ?></strong></td>
                <td style="padding:8px 6px;"><?= nl2br(htmlspecialchars($notif['mensaje'])) ?></td>
                <td style="padding:8px 6px;"><?= date('d/m/Y H:i', strtotime($notif['fecha'])) ?></td>
                <td style="padding:8px 6px; color:<?= $notif['leido'] ? '#888' : '#2176ae' ?>;">
                    <?= $notif['leido'] ? 'Leído' : 'No leído' ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

    <h2 id="inscripciones-section" style="margin-top:32px;">Inscripciones Disponibles</h2>
    <div class="course-list">
        <?php if (!empty($cursos_disponibles)): ?>
            <?php foreach ($cursos_disponibles as $curso): ?>
                <div class="course-item" onclick="showInscripcionModal(<?php echo $curso['id_curso']; ?>, '<?php echo htmlspecialchars($curso['nombre_curso']); ?>')">
                    <div class="course-content">
                        <div class="course-details">
                            <h2><?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
                            <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                            <p><strong>Categoria:</strong> <?php echo htmlspecialchars($curso['nombre_categoria'] ?? 'Sin categoría'); ?></p>
                            <p><strong>Nivel:</strong> <?php echo ucfirst($curso['nivel_educativo']); ?></p>
                            <p><strong>Duración:</strong> <?php echo $curso['duracion']; ?> semanas</p>
                        </div>
                    </div>
                    <div class="inscribir-btn">Inscribirse</div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column:1/-1;text-align:center;padding:40px 0;color:#888;font-size:1.2em;">No hay cursos disponibles en este momento. ¡Vuelve pronto!</div>
        <?php endif; ?>
    </div>
    <div id="success-message" style="display:none;text-align:center;margin:32px auto 0 auto;padding:18px 24px;background:#e6ffed;color:#207245;border-radius:8px;font-size:1.1em;max-width:400px;"></div>
</section>

<!-- Modal de opciones de inscripción -->
<div id="inscripcionModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Opciones de Inscripción</h3>
        <p>Elige cómo quieres inscribirte al curso: <strong id="cursoNombre"></strong></p>
        <div class="modal-buttons">
            <button class="modal-btn rapida" onclick="inscripcionRapida()">Preinscripción Rápida</button>
            <button class="modal-btn completa" onclick="inscripcionCompleta()">Inscripción Completa</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/inscripcion-handler.js"></script>
<script>
let currentCursoId = null;

function showInscripcionModal(cursoId, cursoNombre) {
    currentCursoId = cursoId;
    document.getElementById('cursoNombre').textContent = cursoNombre;
    document.getElementById('inscripcionModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('inscripcionModal').style.display = 'none';
}

function inscripcionRapida() {
    if (!currentCursoId) return;
    
    // Redirigir a la preinscripción
    window.location.href = `/scripts/preinscribir.php?curso_id=${currentCursoId}`;
}

function inscripcionCompleta() {
    if (!currentCursoId) return;
    
    // Redirigir al proceso de inscripción completa
    window.location.href = `/inscripcion/inscripcion_completa.php?curso_id=${currentCursoId}`;
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('inscripcionModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Función para cerrar la notificación de perfil
function closeNotification() {
    const notification = document.getElementById('profile-notification');
    if (notification) {
        notification.style.display = 'none';
        // Guardar en localStorage para no mostrar la notificación por un tiempo
        localStorage.setItem('profileNotificationClosed', Date.now());
    }
}

// Verificar si la notificación fue cerrada recientemente
function checkNotificationStatus() {
    const lastClosed = localStorage.getItem('profileNotificationClosed');
    if (lastClosed) {
        const timeDiff = Date.now() - parseInt(lastClosed);
        // Si se cerró hace menos de 24 horas, no mostrar
        if (timeDiff < 24 * 60 * 60 * 1000) {
            const notification = document.getElementById('profile-notification');
            if (notification) {
                notification.style.display = 'none';
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard de usuario cargado correctamente');
    checkNotificationStatus();
});
</script> 