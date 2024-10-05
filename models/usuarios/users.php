<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
        import SidebarManager from '../../js/form_side.js';

        // Exponer al ámbito global
        window.toggleSidebar = SidebarManager.toggle;
        window.openSidebar = SidebarManager.open;

        document.addEventListener('DOMContentLoaded', SidebarManager.init);
    </script>
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
                    <h1>Control de Usuarios</h1>
                </div>
                <div class="header-right">
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md flex items-center" onclick="openSidebar('new_user.php')">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0M3 20v1c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-1c0-.6-.4-1-1-1H3c-.6 0-1 .4-1 1v1c0 .6.4 1 1 1h16c.6 0 1-.4 1-1V7a4 4 0 00-8 0v13c0 .6-.4 1-1 1H3c-.6 0-1-.4-1-1V3c0-.6.4-1 1-1h16c.6 0 1 .4 1 1v1c0 .6-.4 1-1 1H3z"></path>
                        </svg>
                        Agregar Usuario
                    </button>
                </div>
            </header>

            <section class="content">
                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar usuarios..." onkeyup="filterUsers()">
                </div>

                <!-- Barra de filtros -->
                <div class="filter-bar">
                    <select id="typeFilter" onchange="filterUsers()">
                        <option value="">Tipo de Usuario</option>
                        <?php
                        include "../../scripts/conexion.php";
                        $sql_types = "SELECT DISTINCT id_tipo_usuario, nombre FROM tipo_usuario";
                        $types = $conn->query($sql_types);
                        while ($row = $types->fetch_assoc()) {
                            echo "<option value='" . $row['id_tipo_usuario'] . "'>" . $row['nombre'] . "</option>";
                        }
                        ?>
                    </select>

                    <!-- Puedes agregar más filtros aquí si es necesario -->

                    <!-- Filtros aplicados -->
                    <div id="appliedFilters">
                        <!-- Los filtros aplicados se mostrarán aquí dinámicamente -->
                    </div>
                    <button id="resetFilters" onclick="resetFilters()">Resetear Filtros</button>
                </div>

                <!-- Lista de usuarios -->
                <div class="user-list-container">
                <div class="user-list">
                    <?php
                    $sql = "SELECT t.nombre as tipo_usuario, t.id_tipo_usuario, u.id_usuario, u.nombre, u.apellido, u.mail, u.foto 
                            FROM usuario u
                            JOIN tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="user-item" data-user-id="' . $row["id_usuario"] . '" data-user-type="' . $row["id_tipo_usuario"] . '">';
                            echo '<img src="../../uploads/' . (empty($row["foto"]) ? '../../img/usuario.png' : $row["foto"]) . '" alt="User Image">';
                            echo '<div class="user-details">';
                            echo '<h2>' . $row["nombre"] . " " . $row['apellido']  . '</h2>';
                            echo '<p>' . $row["tipo_usuario"] . '</p>';
                            echo '<p>' . $row["mail"] . '</p>';
                            echo '</div>';
                            echo '<div class="user-actions">';
                            echo '<button onclick="openSidebar(\'edit_user.php?id_usuario=' . $row["id_usuario"] . '\')" class="edit-btn">Edit</button>';
                            echo '<button onclick="deleteUser(' . $row["id_usuario"] . ')" class="delete-btn">Delete</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "No hay usuarios.";
                    }
                    $conn->close();
                    ?>
                </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        function deleteUser(userId) {
            if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
                fetch('delete_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_usuario=' + userId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`[data-user-id="${userId}"]`).remove();
                            alert('Usuario eliminado con éxito');
                        } else {
                            alert('Error eliminando el usuario: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al eliminar el usuario.');
                    });
            }
        }

        function filterUsers() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;
            const users = document.querySelectorAll('.user-item');

            users.forEach(user => {
                const userName = user.querySelector('h2').textContent.toLowerCase();
                const userEmail = user.querySelector('p:nth-child(3)').textContent.toLowerCase();
                const userType = user.getAttribute('data-user-type');

                const matchesSearch = userName.includes(searchInput) || userEmail.includes(searchInput);
                const matchesType = typeFilter === '' || userType === typeFilter;

                if (matchesSearch && matchesType) {
                    user.style.display = '';
                } else {
                    user.style.display = 'none';
                }
            });

            updateAppliedFilters();
        }

        function updateAppliedFilters() {
            const appliedFilters = document.getElementById('appliedFilters');
            appliedFilters.innerHTML = '';

            const typeFilter = document.getElementById('typeFilter');
            if (typeFilter.value) {
                const filterTag = document.createElement('div');
                filterTag.className = 'filter-tag';
                filterTag.setAttribute('data-filter', 'type');
                filterTag.innerHTML = `
                    <span>${typeFilter.options[typeFilter.selectedIndex].text}</span>
                    <span class="filter-close" onclick="removeFilter('type')">&times;</span>
                `;
                appliedFilters.appendChild(filterTag);
            }

            // Puedes agregar más filtros aquí si es necesario
        }

        function removeFilter(filterType) {
            if (filterType === 'type') {
                document.getElementById('typeFilter').value = '';
            }
            // Agregar más casos aquí si se añaden más filtros

            filterUsers();
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('typeFilter').value = '';
            // Resetear más filtros aquí si se añaden

            filterUsers();
        }

        document.getElementById('overlay').addEventListener('click', toggleSidebar);
    </script>
</body>

</html>