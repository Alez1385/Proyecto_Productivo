<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="user.css">
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
                    <button class="add-user-btn" onclick="openSidebar('new_user.php')">+ Add New User</button>
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
                    include "../../scripts/conexion.php";

                    $sql = "SELECT t.nombre as tipo_usuario, u.id_usuario, u.nombre, u.apellido, u.mail, u.foto FROM usuario u
                    JOIN tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario ";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="user-item" data-user-id="' . $row["id_usuario"] . '">';
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
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const userList = document.querySelector('.user-list');
            const users = userList.getElementsByClassName('user-item');

            for (let i = 0; i < users.length; i++) {
                const userDetails = users[i].getElementsByClassName('user-details')[0];
                const name = userDetails.getElementsByTagName('h2')[0].textContent.toLowerCase();
                const email = userDetails.getElementsByTagName('p')[1].textContent.toLowerCase();

                if (name.indexOf(filter) > -1 || email.indexOf(filter) > -1) {
                    users[i].style.display = '';
                } else {
                    users[i].style.display = 'none';
                }
            }
        }

        document.getElementById('overlay').addEventListener('click', toggleSidebar);

        function setupFormValidation() {
            // Verificar si el campo de nombre de usuario existe antes de agregar el event listener
            const usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.addEventListener('input', function() {
                    const username = this.value;
                    const usernameError = document.getElementById('usernameError');
                    const submitBtn = document.getElementById('submitBtn');

                    if (username.length > 0) {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '../../login/scripts/check_username.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                        xhr.onreadystatechange = function() {
                            if (this.readyState === 4) {
                                console.log('Estado de la solicitud:', this.status);
                                console.log('Respuesta del servidor:', this.responseText);
                                if (this.status === 200) {
                                    if (this.responseText === 'taken') {
                                        usernameError.textContent = 'El nombre de usuario ya está en uso. Elige otro.';
                                        usernameError.style.color = '#c12646';
                                    } else {
                                        usernameError.textContent = 'El nombre de usuario está disponible.';
                                        usernameError.style.color = '#00bcff';
                                        submitBtn.disabled = false;
                                    }
                                } else {
                                    usernameError.textContent = 'Error al verificar el nombre de usuario. Inténtalo más tarde.';
                                    usernameError.style.color = '#c12646';
                                }
                            }
                        };

                        xhr.onerror = function() {
                            usernameError.textContent = 'Error al conectar con el servidor. Inténtalo más tarde.';
                            usernameError.style.color = '#c12646';
                        };

                        xhr.send('username=' + encodeURIComponent(username));
                    } else {
                        usernameError.textContent = '';
                    }
                });
            }

            // Verificar si el campo de email existe antes de agregar el event listener
            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.addEventListener('input', function() {
                    const email = this.value;
                    const emailError = document.getElementById('emailError');

                    // Expresión regular para validar el formato del correo electrónico
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (emailPattern.test(email)) {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '../../login/scripts/check_email.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                        xhr.onreadystatechange = function() {
                            if (this.readyState === 4) {
                                console.log('Estado de la solicitud:', this.status);
                                console.log('Respuesta del servidor:', this.responseText);
                                if (this.status === 200) {
                                    if (this.responseText === 'taken') {
                                        emailError.textContent = 'El correo electrónico ya está en uso. Elige otro.';
                                        emailError.style.color = '#c12646';
                                    } else {
                                        emailError.textContent = 'El correo electrónico está disponible.';
                                        emailError.style.color = '#00bcff';
                                        submitBtn.disabled = false;
                                    }
                                } else {
                                    emailError.textContent = 'Error al verificar el correo electrónico. Inténtalo más tarde.';
                                    emailError.style.color = '#c12646';
                                }
                            }
                        };

                        xhr.onerror = function() {
                            emailError.textContent = 'Error al conectar con el servidor. Inténtalo más tarde.';
                            emailError.style.color = '#c12646';
                        };

                        xhr.send('email=' + encodeURIComponent(email));
                    } else {
                        emailError.textContent = 'Introduce un formato de correo electrónico válido.';
                        emailError.style.color = '#c12646';
                    }
                });
            }
        }

        function togglePassword(passwordId, toggleId) {
            const passwordField = document.getElementById(passwordId);
            const lockIcon = document.getElementById(toggleId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                lockIcon.src = '../../login/img/eye-close.svg';
                lockIcon.classList.add('rotate');
            } else {
                passwordField.type = 'password';
                lockIcon.src = '../../login/img/eye-open.svg';
                lockIcon.classList.remove('rotate');
            }
        }

        function previewImage(input) {
            var preview = document.getElementById('previewImg');
            var file = input.files[0];
            var reader = new FileReader();

            reader.onloadend = function() {
                if (preview) {
                    preview.src = reader.result;
                } else {
                    var img = document.createElement('img');
                    img.src = reader.result;
                    img.width = 100;
                    img.id = 'previewImg';
                    document.getElementById('imagePreview').appendChild(img);
                }
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                if (preview) {
                    preview.src = "";
                }
            }
        }
    </script>
</body>

</html>