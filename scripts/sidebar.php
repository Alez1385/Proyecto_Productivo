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
    $isUserTypeUser = ($user['tipo_nombre'] === 'user');
    $id_tipo_usuario = $_SESSION['id_tipo_usuario'] ?? null;

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
                    <p><b><?php echo htmlspecialchars($user['nombre'] ?: $user['username']); ?></b></p>
                </div>
                <div class="profile-photo">
                    <?php if (!empty($user['foto']) && file_exists(__DIR__ . '/../uploads/' . $user['foto'])): ?>
                        <img src="<?php echo BASE_URL . 'uploads/' . htmlspecialchars($user['foto']); ?>" alt="User Image">
                    <?php else: ?>
                        <i class="fas fa-user-circle user-icon" style="font-size: 60px; color: #ccc;"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sidebar-content">
                <div id="sortable-sidebar">
                    <!-- Dashboard always first -->
                    <div class="sidebar-item" data-module-id="dashboard">
                        <a href="/dashboard/dashboard.php">
                            <span class="material-icons-sharp">dashboard</span>
                            <h3>Dashboard</h3>
                        </a>
                    </div>

                    <!-- Módulos dinámicos para todos los usuarios -->
                    <?php foreach ($modules as $module): ?>
                        <div class="sidebar-item" draggable="true" data-module-id="<?php echo htmlspecialchars($module['id_modulo'], ENT_QUOTES, 'UTF-8'); ?>">
                            <a href="<?php echo BASE_URL . htmlspecialchars($module['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="material-icons-sharp"><?php echo htmlspecialchars($module['icono'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <h3><?php echo htmlspecialchars($module['nom_modulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sidebar-bottom">
                <a href="<?php echo BASE_URL . 'login/logout.php'; ?>" class="logout">
                    <span  class="material-icons-sharp">logout</span>
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

                if (draggedItem !== this) {
                    const allItems = [...this.parentNode.querySelectorAll('.sidebar-item')];
                    const draggedIndex = allItems.indexOf(draggedItem);
                    const droppedIndex = allItems.indexOf(this);

                    if (draggedIndex < droppedIndex) {
                        this.parentNode.insertBefore(draggedItem, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(draggedItem, this);
                    }
                }

                return false;
            }

            function handleDragEnd(e) {
                this.classList.remove('dragging');
                const items = [...this.parentNode.querySelectorAll('.sidebar-item')];
                const moduleOrder = items.map(item => item.getAttribute('data-module-id')).filter(id => id !== 'dashboard');
                
                // Save the new order
                fetch('<?php echo BASE_URL; ?>scripts/update_module_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        module_order: moduleOrder
                    })
                }).catch(error => console.error('Error saving module order:', error));
            }

            // Add event listeners to draggable items
            const draggableItems = sidebar.querySelectorAll('.sidebar-item[draggable="true"]');
            draggableItems.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragover', handleDragOver);
                item.addEventListener('dragenter', handleDragEnter);
                item.addEventListener('dragleave', handleDragLeave);
                item.addEventListener('drop', handleDrop);
                item.addEventListener('dragend', handleDragEnd);
            });
        });
    </script>
</body>
</html>
