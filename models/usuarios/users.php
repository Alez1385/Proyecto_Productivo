<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../../dashboard/css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
</head>
<body>
  
  
    
    <div class="dashboard-container">
    
<?php
include "../../scripts/sidebar.php";
include "../../scripts/conexion.php";

// Consulta para obtener los usuarios
$sql = "SELECT id_usuario, nombre, mail, foto FROM usuario";
$result = $conn->query($sql);
?>

<!-- Main Content -->
<div class="main-content">
    <header class="header">
        <div class="header-left">
            <h1>User Management</h1>
            <p>Manage and control all users</p>
        </div>
        <div class="header-right">
            <button class="add-user-btn" onclick="window.location.href='new_user.php'">+ Add New User</button>
        </div>
    </header>

    <section class="content">
        <div class="user-list">
            <?php
            if ($result->num_rows > 0) {
                // Salida de datos de cada fila
                while($row = $result->fetch_assoc()) {
                    echo '<div class="user-item">';
                    echo '<img src="../../uploads/' . $row["foto"] . '" alt="User Image">';
                    echo '<div class="user-details">';
                    echo '<h2>' . $row["nombre"] . '</h2>';
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
</script>
</body>
</html>


