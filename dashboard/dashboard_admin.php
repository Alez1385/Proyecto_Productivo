<?php
// dashboard_admin.php

// Verificar permisos de administrador
checkPermission('admin');

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
/* Estilos para el dashboard de admin */
.dashboard-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    
    color: white;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

.summary-card i {
    font-size: 2.5em;
    margin-bottom: 15px;
    opacity: 0.9;
}

.summary-card h3 {
    font-size: 1.2em;
    margin-bottom: 10px;
    font-weight: 600;
}

.summary-card p {
    font-size: 2em;
    font-weight: bold;
    margin: 0;
}

.dashboard-charts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
}

.chart-container {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.chart-container h3 {
    margin-bottom: 20px;
    color: #333;
    font-size: 1.3em;
    font-weight: 600;
}

.chart-full-width {
    grid-column: 1 / -1;
}

.recent-payments {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.recent-payments h3 {
    margin-bottom: 20px;
    color: #333;
    font-size: 1.3em;
    font-weight: 600;
}

.recent-payments table {
    width: 100%;
    border-collapse: collapse;
}

.recent-payments th,
.recent-payments td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.recent-payments th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.recent-payments tr:hover {
    background-color: #f8f9fa;
}

/* Estilos para la notificación de perfil */
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

/* Responsive */
@media (max-width: 768px) {
    .dashboard-summary {
        grid-template-columns: 1fr;
    }
    
    .dashboard-charts {
        grid-template-columns: 1fr;
    }
    
    .chart-container {
        padding: 15px;
    }
}
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

<?php
// Obtener estadísticas generales
$total_users = $conn->query("SELECT COUNT(*) as count FROM usuario")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) as count FROM cursos")->fetch_assoc()['count'];
$total_students = $conn->query("SELECT COUNT(*) as count FROM estudiante")->fetch_assoc()['count'];
$total_teachers = $conn->query("SELECT COUNT(*) as count FROM profesor")->fetch_assoc()['count'];

// Obtener cursos más populares
$popular_courses = getDatabaseData($conn, "
    SELECT c.nombre_curso, COUNT(i.id_inscripcion) as inscripciones
    FROM cursos c
    LEFT JOIN inscripciones i ON c.id_curso = i.id_curso
    GROUP BY c.id_curso
    ORDER BY inscripciones DESC
    LIMIT 5
");

// Obtener progreso promedio de estudiantes por curso
$student_progress = getDatabaseData($conn, "
    SELECT c.nombre_curso, AVG(a.presente) as promedio_asistencia
    FROM cursos c
    JOIN asistencia a ON c.id_curso = a.id_curso
    GROUP BY c.id_curso
    ORDER BY promedio_asistencia DESC
    LIMIT 5
");

// Obtener distribución de niveles educativos
$education_levels = getDatabaseData($conn, "
    SELECT nivel_educativo, COUNT(*) as count
    FROM cursos
    GROUP BY nivel_educativo
");

// Obtener últimos pagos
$recent_payments = getDatabaseData($conn, "
    SELECT p.monto, p.fecha_pago, u.nombre, u.apellido
    FROM pagos p
    JOIN estudiante e ON p.id_estudiante = e.id_estudiante
    JOIN usuario u ON e.id_usuario = u.id_usuario
    ORDER BY p.fecha_pago DESC
    LIMIT 5
");
?>

<section class="dashboard-summary">
    <div class="summary-card">
        <i class="fas fa-users"></i>
        <h3>Usuarios Totales</h3>
        <p><?php echo $total_users; ?></p>
    </div>
    <div class="summary-card">
        <i class="fas fa-book"></i>
        <h3>Cursos Totales</h3>
        <p><?php echo $total_courses; ?></p>
    </div>
    <div class="summary-card">
        <i class="fas fa-user-graduate"></i>
        <h3>Estudiantes</h3>
        <p><?php echo $total_students; ?></p>
    </div>
    <div class="summary-card">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>Profesores</h3>
        <p><?php echo $total_teachers; ?></p>
    </div>
</section>

<div class="dashboard-charts">
    <div class="chart-container">
        <h3>Cursos Más Populares</h3>
        <canvas id="popularCoursesChart"></canvas>
    </div>
    <div class="chart-container">
        <h3>Progreso de Estudiantes por Curso</h3>
        <canvas id="studentProgressChart"></canvas>
    </div>
</div>

<div class="dashboard-charts chart-full-width">
    <div class="chart-container">
        <h3>Distribución de Niveles Educativos</h3>
        <canvas id="educationLevelsChart"></canvas>
    </div>
</div>

<section class="recent-payments">
    <h3>Últimos Pagos</h3>
    <table>
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Monto</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_payments as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment['nombre'] . ' ' . $payment['apellido']); ?></td>
                    <td>$<?php echo htmlspecialchars($payment['monto']); ?></td>
                    <td><?php echo htmlspecialchars($payment['fecha_pago']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de cursos populares
    var ctxPopular = document.getElementById('popularCoursesChart').getContext('2d');
    var popularCoursesChart = new Chart(ctxPopular, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($popular_courses, 'nombre_curso')); ?>,
            datasets: [{
                label: 'Inscripciones',
                data: <?php echo json_encode(array_column($popular_courses, 'inscripciones')); ?>,
                backgroundColor: 'rgba(52, 152, 219, 0.6)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Número de Inscripciones'
                    }
                }
            }
        }
    });

    // Gráfico de progreso de estudiantes por curso
    var ctxProgress = document.getElementById('studentProgressChart').getContext('2d');
    var studentProgressChart = new Chart(ctxProgress, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($student_progress, 'nombre_curso')); ?>,
            datasets: [{
                label: 'Promedio de Asistencia',
                data: <?php echo json_encode(array_column($student_progress, 'promedio_asistencia')); ?>,
                fill: false,
                borderColor: 'rgb(231, 76, 60)',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1,
                    title: {
                        display: true,
                        text: 'Promedio de Asistencia'
                    }
                }
            }
        }
    });

    // Gráfico de distribución de niveles educativos
    var ctxEducation = document.getElementById('educationLevelsChart').getContext('2d');
    var educationLevelsChart = new Chart(ctxEducation, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($education_levels, 'nivel_educativo')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($education_levels, 'count')); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Distribución de Niveles Educativos'
                }
            }
        }
    });
</script>