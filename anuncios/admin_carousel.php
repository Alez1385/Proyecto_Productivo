<?php
include('../scripts/conexion.php'); // Conectar a la base de datos

// Agregar nuevo slid_carrousele
if (isset($_POST['add_slid_carrousele'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $fecha_curso_inicio = $_POST['fecha_curso_inicio'];
    $fecha_curso_fin = $_POST['fecha_curso_fin'];
    $image = $_FILES['image']['name'];
   
    // Mover la imagen subida al directorio correcto
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$image");
    $query = "INSERT INTO carousel (title, description, image, fecha_curso_inicio, fecha_curso_fin) VALUES ('$title', '$description', '$image', '$fecha_curso_inicio', '$fecha_curso_fin')";
    $conn->query($query);
}

// Eliminar slid_carrousele
if (isset($_POST['delete_confirmed'])) {
    $id_carrousel = $_POST['id_carrousel'];
    $query = "DELETE FROM carousel WHERE id_carrousel = $id_carrousel";
    if ($conn->query($query) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=true");
        exit();
    }
}

$result = $conn->query("SELECT * FROM carousel ORDER BY order_index ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Carousel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<class="container">
    
        <h1>Administra el Carousel</h1>
    </div>
   
    <!-- Formulario para agregar un nuevo slid_carrousele -->
    <form action="" method="post" enctype="multipart/form-data" class="fade-in">
    <div class="header">
    <a href="../models/admin_index/admin_index.php" id="back-button" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Volver
</a><br></div>
        <div class="form-group">
            <label for="title">Título</label>
            <input type="text" id="title" name="title" maxlength="30" required>
            <small><span id="title-count">0</span>/30 caracteres</small>
        </div>
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea id="description" name="description" rows="3" maxlength="90" required></textarea>
            <small><span id="description-count">0</span>/ 90 caracteres</small>
        </div>
        <div class="form-group">
            <label for="fecha_curso_inicio">Fecha de Inicio</label>
            <input type="date" id="fecha_curso_inicio" name="fecha_curso_inicio" required>
        </div>
        <div class="form-group">
            <label for="fecha_curso_fin">Fecha de Fin</label>
            <input type="date" id="fecha_curso_fin" name="fecha_curso_fin" required>
        </div>
        <div class="form-group">
            <label for="image">Imagen</label>
            <input type="file" id="image" name="image" required>
        </div>
        <button type="submit" name="add_slid_carrousele" class="btn btn-primary">Agregar anuncio</button>
    </form>

        
        <!-- Lista de slid_carrouseles existentes -->
        <table class="fade-in">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Imagen</th>
                    <th>Fechas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_carrousel'] ?></td>
                    <td><?= $row['title'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><img src="../uploads/<?= $row['image'] ?>" alt="<?= $row['title'] ?>" width="100"></td>
                    <td>Inicio: <?= $row['fecha_curso_inicio'] ?> <br> Fin: <?= $row['fecha_curso_fin'] ?></td>
                    <td>
                        <button onclick="openDeleteModal(<?= $row['id_carrousel'] ?>)" class="btn delete-btn">Eliminar</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de confirmación -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <p>¿Estás seguro de que deseas eliminar este anuncio?</p>
            <div class="modal-buttons">
                <button onclick="confirmDelete()">Sí, eliminar</button>
                <button onclick="closeModal()">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Notificación de éxito -->
    <div id="notification" class="notification">
        Anuncio eliminado exitosamente.
    </div>

    <script>
        // Contador de caracteres para el título
        const titleInput = document.getElementById('title');
        const titleCount = document.getElementById('title-count');
        
        titleInput.addEventListener('input', () => {
            titleCount.textContent = titleInput.value.length;
        });

        // Contador de caracteres para la descripción
        const descriptionInput = document.getElementById('description');
        const descriptionCount = document.getElementById('description-count');
        
        descriptionInput.addEventListener('input', () => {
            descriptionCount.textContent = descriptionInput.value.length;
        });

        // Funcionalidad del modal
        const modal = document.getElementById('deleteModal');
        const modalContent = modal.querySelector('.modal-content');
        let currentIdToDelete;

        function openDeleteModal(id) {
            currentIdToDelete = id;
            modal.style.display = "block";
            setTimeout(() => {
                modalContent.classList.add('show');
            }, 10);
        }

        function closeModal() {
            modalContent.classList.remove('show');
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);
        }

        function confirmDelete() {
            const form = document.createElement('form');
            form.method = 'post';
            form.innerHTML = `
                <input type="hidden" name="delete_confirmed" value="1">
                <input type="hidden" name="id_carrousel" value="${currentIdToDelete}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Mostrar notificación si se ha eliminado un anuncio
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
        const notification = document.getElementById('notification');
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>
