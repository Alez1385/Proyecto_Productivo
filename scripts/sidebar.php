<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../../../login/login.php");
    exit();
}

$username = $_SESSION['username'];

// Obtener la información del usuario autenticado, incluyendo el tipo de usuario
$sql = "SELECT u.*, t.nombre AS tipo_nombre FROM usuario u 
        JOIN tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario 
        WHERE u.username = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Error al preparar la consulta de usuario: ' . $conn->error);
}
$stmt->bind_param("s", $username);
if (!$stmt->execute()) {
    die('Error al ejecutar la consulta de usuario: ' . $stmt->error);
}
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die('No se encontró el usuario.');
}
$user = $result->fetch_assoc();
$stmt->close();

// Obtener los módulos dinámicamente desde la base de datos según el tipo de usuario
$sql_modulos = "SELECT m.nom_modulo, m.icono, m.url 
                FROM modulos m 
                JOIN asig_modulo am ON m.id_modulo = am.id_modulo 
                WHERE am.id_tipo_usuario = ?";
$stmt_modulos = $conn->prepare($sql_modulos);
if (!$stmt_modulos) {
    die('Error al preparar la consulta de módulos: ' . $conn->error);
}
$stmt_modulos->bind_param("i", $user['id_tipo_usuario']);
if (!$stmt_modulos->execute()) {
    die('Error al ejecutar la consulta de módulos: ' . $stmt_modulos->error);
}
$modulosResult = $stmt_modulos->get_result();
$stmt_modulos->close();

// Procesar los resultados para utilizarlos en tu dashboard
$modulos = [];
while ($row = $modulosResult->fetch_assoc()) {
    $modulos[] = $row;
}

// Ahora, $modulos contiene los módulos asignados al tipo de usuario autenticado

?>



<!-- sidebar.php -->

<aside>
    <div class="sidebar">
        <div class="profile">
            <div class="info">
                <p>Hey, <b><?php echo htmlspecialchars($user['nombre']); ?></b></p>
                <small class="text-muted">
                    <?php
                    $tipo_usuario_id = $user['id_tipo_usuario'];
                    $sql_tipo_usuario = "SELECT nombre FROM tipo_usuario WHERE id_tipo_usuario = ?";
                    $stmt_tipo_usuario = $conn->prepare($sql_tipo_usuario);
                    $stmt_tipo_usuario->bind_param("i", $tipo_usuario_id);
                    $stmt_tipo_usuario->execute();
                    $result_tipo_usuario = $stmt_tipo_usuario->get_result();
                    $tipo_usuario = $result_tipo_usuario->fetch_assoc();
                    echo htmlspecialchars($tipo_usuario['nombre']);
                    $stmt_tipo_usuario->close();
                    ?>
                </small>
            </div>
            <div class="profile-photo">
                <img src="../../uploads/<?php echo htmlspecialchars(basename($user['foto'])); ?>" alt="User Image">
            </div>
        </div>

        <?php if (!empty($modulos)): ?>
            <?php foreach ($modulos as $modulo): ?>
                <a href="<?= htmlspecialchars($modulo['url'], ENT_QUOTES, 'UTF-8'); ?>"> <!-- url del modulo -->
                    <span class="material-icons-sharp"><?= htmlspecialchars($modulo['icono'], ENT_QUOTES, 'UTF-8'); ?></span> <!-- Icono del modulo -->
                    <h3><?= htmlspecialchars($modulo['nom_modulo'], ENT_QUOTES, 'UTF-8'); ?></h3> <!-- Nombre del modulo -->
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay módulos disponibles para este tipo de usuario.</p>
        <?php endif; ?>

        <div class="toggle">
            <div class="logo">
                <img src="https://corsaje.gnosoft.com.co/general-ws/imgGeneral?imagen=2" alt="Logo">
                <h2>Corsa<span style="color: #3498db;">Cor</span></h2>
            </div>
            <div class="close" id="close-btn">
                <span class="material-icons-sharp">close</span>
            </div>
        </div>
    </div>
</aside>

<!-- Manejo de errores en JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Puedes realizar comprobaciones en el lado del cliente si es necesario.
        try {
            // Ejemplo de operación que podría fallar
            var elements = document.querySelectorAll('.sidebar a');
            if (elements.length === 0) {
                throw new Error('No se encontraron elementos en el sidebar.');
            }

            elements.forEach(function(element) {
                // Verifica si hay algún problema con los enlaces
                if (!element.href) {
                    console.warn('Un enlace en el sidebar no tiene una URL.');
                }
            });
        } catch (error) {
            console.error('Error en el script del sidebar:', error.message);
            alert('Hubo un problema al cargar el contenido del sidebar. Por favor, intenta de nuevo más tarde.');
        }
    });
</script>