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
?>

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
