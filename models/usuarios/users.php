<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="user.css">
</head>

<body>

    <div class="dashboard-container">

        <?php
        include "../../scripts/sidebar.php";
        include "../../scripts/conexion.php";

        // Consulta para obtener los usuarios
        $sql = "SELECT t.nombre as tipo_usuario, u.id_usuario, u.nombre, u.apellido, u.mail, u.foto FROM usuario u
        JOIN tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario ";
        $result = $conn->query($sql);
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Control de Usuarios</h1>
                </div>
                <div class="header-right">
                    <button class="add-user-btn" onclick="window.location.href='new_user.php'">+ Add New User</button>
                </div>
            </header>

            <section class="content">
                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <span class="material-icons-sharp search-icon">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar usuarios..." onkeyup="filterUsers()">
                </div>


                <!-- Lista de usuarios -->
                <div class="user-list">
                    <?php
                    if ($result->num_rows > 0) {
                        // Salida de datos de cada fila
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="user-item" data-user-id="' . $row["id_usuario"] . '">';
                            echo '<img src="../../uploads/' . $row["foto"] . '" alt="User Image">';
                            echo '<div class="user-details">';
                            echo '<h2>' . $row["nombre"] . " " . $row['apellido']  . '</h2>';
                            echo '<p>' . $row["tipo_usuario"] . '</p>';
                            echo '<p>' . $row["mail"] . '</p>';
                            echo '</div>';
                            echo '<div class="user-actions">';
                            echo '<button onclick=window.location.href="edit_user.php?id_usuario=' . $row["id_usuario"] . '" class="edit-btn">Edit</button>';
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
            </section>
        </div>
    </div>

    <script>
        function deleteUser(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
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
                            // Remove the user element from the DOM
                            const userElement = document.querySelector(`[data-user-id="${userId}"]`);
                            if (userElement) {
                                userElement.remove();
                            }
                            alert('User deleted successfully');
                        } else {
                            alert('Error deleting user: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the user');
                    });
            }
        }

        function filterUsers() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const userList = document.querySelector('.user-list');
            const users = userList.getElementsByClassName('user-item');

            for (let i = 0; i < users.length; i++) {
                const userDetails = users[i].getElementsByClassName('user-details')[0];
                const name = userDetails.getElementsByTagName('h2')[0].textContent.toLowerCase();
                const email = userDetails.getElementsByTagName('p')[0].textContent.toLowerCase();

                if (name.indexOf(filter) > -1 || email.indexOf(filter) > -1) {
                    users[i].style.display = '';
                } else {
                    users[i].style.display = 'none';
                }
            }
        }
    </script>

</body>

</html>