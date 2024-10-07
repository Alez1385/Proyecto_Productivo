<?php
// sidebar.php
require_once 'conexion.php';
require_once 'config.php';
require_once 'functions.php';
require_once 'auth.php';

// Get modules for user type
function getModules($conn, $userTypeId)
{
    $sql = "SELECT m.id_modulo, m.nom_modulo, m.icono, m.url, m.orden
            FROM modulos m 
            JOIN asig_modulo am ON m.id_modulo = am.id_modulo 
            WHERE am.id_tipo_usuario = ?
            ORDER BY m.orden ASC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error preparing modules query: ' . $conn->error);
    }
    $stmt->bind_param("i", $userTypeId);
    if (!$stmt->execute()) {
        throw new Exception('Error executing modules query: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $modules = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $modules;
}

// Get saved module order for user
function getSavedModuleOrder($conn, $userId)
{
    $sql = "SELECT module_order FROM user_module_order WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error preparing get order query: ' . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        throw new Exception('Error executing get order query: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($row && isset($row['module_order'])) {
        $decodedOrder = json_decode($row['module_order'], true);
        return is_array($decodedOrder) ? $decodedOrder : [];
    }
    return [];
}

try {
    if (!isset($_SESSION['id_usuario'])) {
        throw new Exception('ID de usuario no encontrado en la sesión.');
    }

    $user = getUserInfo($conn, $_SESSION['id_usuario']);
    $modules = getModules($conn, $user['id_tipo_usuario']);
    
    // Get saved order and reorder modules
    $savedOrder = getSavedModuleOrder($conn, $_SESSION['id_usuario']);
    if (!empty($savedOrder) && is_array($savedOrder)) {
        $orderedModules = [];
        $moduleIds = array_column($modules, 'id_modulo');
        
        foreach ($savedOrder as $moduleId) {
            $key = array_search($moduleId, $moduleIds);
            if ($key !== false && isset($modules[$key])) {
                $orderedModules[] = $modules[$key];
                unset($modules[$key]);
            }
        }
        
        $modules = array_merge($orderedModules, array_values($modules));
    }

} catch (Exception $e) {
    error_log('Error en la barra lateral: ' . $e->getMessage());
    echo '<script>console.error("Error en la barra lateral: ' . addslashes($e->getMessage()) . '");</script>';
    die('Error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>dashboard/css/style.css">
    <script src="<?php echo BASE_URL; ?>js/auth-debug.js"></script>
</head>

<body>
    <aside>
        <div class="sidebar">
            <div class="profile">
                <div class="info">
                    <p><b><?php echo htmlspecialchars($user['nombre']); ?></b></p>
                    <small class="text-muted"><?php echo htmlspecialchars($user['tipo_nombre']); ?></small>
                </div>
                <div class="profile-photo">
                    <img src="<?php echo BASE_URL . 'uploads/' . htmlspecialchars($user['foto']); ?>" alt="User Image">
                </div>
            </div>

            <div id="sortable-sidebar">
                <!-- Dashboard always first -->
                <div class="sidebar-item" data-module-id="dashboard">
                    <a href="/dashboard/dashboard.php">
                        <span class="material-icons-sharp">dashboard</span>
                        <h3>Dashboard</h3>
                    </a>
                </div>

                <?php if (!empty($modules)): ?>
                    <?php foreach ($modules as $module): ?>
                        <div class="sidebar-item" draggable="true" data-module-id="<?php echo htmlspecialchars($module['id_modulo'], ENT_QUOTES, 'UTF-8'); ?>">
                            <a href="<?php echo BASE_URL . htmlspecialchars($module['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="material-icons-sharp"><?php echo htmlspecialchars($module['icono'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <h3><?php echo htmlspecialchars($module['nom_modulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay módulos disponibles para este tipo de usuario.</p>
                <?php endif; ?>
            </div>

            <div class="sidebar-bottom">
                <a href="<?php echo BASE_URL . 'login/logout.php'; ?>" class="logout">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Salir</h3>
                </a>
            </div>
        </div>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sortable-sidebar');
            let draggedItem = null;

            function handleDragStart(e) {
                draggedItem = this;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.innerHTML);
                this.classList.add('dragging');
            }

            function handleDragOver(e) {
                if (e.preventDefault) {
                    e.preventDefault();
                }
                e.dataTransfer.dropEffect = 'move';
                return false;
            }

            function handleDragEnter(e) {
                this.classList.add('over');
            }

            function handleDragLeave(e) {
                this.classList.remove('over');
            }

            function handleDrop(e) {
                if (e.stopPropagation) {
                    e.stopPropagation();
                }

                if (draggedItem !== this && draggedItem.dataset.moduleId !== 'dashboard') {
                    if (this.dataset.moduleId === 'dashboard') {
                        sidebar.insertBefore(draggedItem, this.nextSibling);
                    } else {
                        sidebar.insertBefore(draggedItem, this);
                    }
                    updateOrder();
                }

                return false;
            }

            function handleDragEnd(e) {
                this.classList.remove('dragging');
                Array.from(sidebar.children).forEach(child => {
                    child.classList.remove('over');
                });
            }

            function addDragEvents(item) {
                if (item.dataset.moduleId !== 'dashboard') {
                    item.addEventListener('dragstart', handleDragStart);
                    item.addEventListener('dragover', handleDragOver);
                    item.addEventListener('dragenter', handleDragEnter);
                    item.addEventListener('dragleave', handleDragLeave);
                    item.addEventListener('drop', handleDrop);
                    item.addEventListener('dragend', handleDragEnd);
                }
            }

            function updateOrder() {
                const items = Array.from(sidebar.children);
                const order = items
                    .filter(item => item.dataset.moduleId !== 'dashboard')
                    .map(item => item.dataset.moduleId);

                // Send the new order to the server
                fetch('<?php echo BASE_URL; ?>scripts/update_module_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_usuario: <?php echo $_SESSION['id_usuario']; ?>,
                        module_order: order
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Order updated successfully');
                    } else {
                        console.error('Error al actualizar el orden:', data.error);
                    }
                })
                .catch(error => console.error('Error:', error.message));
            }

            Array.from(sidebar.children).forEach(addDragEvents);
        });
    </script>
</body>

</html>