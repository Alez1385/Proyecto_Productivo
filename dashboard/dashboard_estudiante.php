<?php
// Include necessary files and perform initial checks
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a student
if (!isset($_SESSION['username']) || !checkPermission('estudiante')) {
    echo '<h2 class="no-tienes-permiso">No tienes permiso para acceder a esta página.</h2>';
    exit;
}
?>
<section class="student-dashboard">
    <!-- Div para mostrar errores -->
    <div id="error-message" style="display:none;"></div>
    <h2>Mis Inscripciones</h2>
    <div id="inscripciones-list" class="inscripciones-list">
        <!-- Inscripciones will be dynamically inserted here -->
    </div>

    <h2>Mis Preinscripciones</h2>
    <div id="preinscripciones-list" class="preinscripciones-list">
        <!-- Preinscripciones will be dynamically inserted here -->
    </div>

    <h2>Cursos Disponibles</h2>
    <div id="course-list" class="course-list">
        <!-- Available courses will be dynamically inserted here -->
    </div>
</section>

<script src="../js/inscripcion-handler.js"></script>
<script src="../js/dashboard_updater.js"></script>

