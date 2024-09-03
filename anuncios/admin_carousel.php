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
if (isset($_GET['delete'])) {
    $id_carrousel = $_GET['delete'];
    $query = "DELETE FROM carousel WHERE id_carrousel = $id_carrousel";
    $conn->query($query);
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
    <div class="container">
        <h1>Administra el Carousel</h1>
        
        <!-- Formulario para agregar un nuevo slid_carrousele -->
        <form action="" method="post" enctype="multipart/form-data" class="fade-in">
            <div>
                <label for="title">Título</label>
                <input type="text" id="title" name="title" maxlength="30" required>
                <small><span id="title-count">0</span>/30 caracteres</small>
            </div>
            <div>
                <label for="description">Descripción</label>
                <textarea id="description" name="description" rows="3" maxlength="90" required></textarea>
                <small><span id="description-count">0</span>/ 90 caracteres</small>
            </div>
            <div>
                <label for="fecha_curso_inicio">Fecha de Inicio</label>
                <input type="date" id="fecha_curso_inicio" name="fecha_curso_inicio" required>
            </div>
            <div>
                <label for="fecha_curso_fin">Fecha de Fin</label>
                <input type="date" id="fecha_curso_fin" name="fecha_curso_fin" required>
            </div>
            <div>
                <label for="image">Imagen</label>
                <input type="file" id="image" name="image" required>
            </div>
            <button type="submit" name="add_slid_carrousele">Agregar anuncio</button>
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
                    <td><img src="../uploads/<?= $row['image'] ?>" alt="<?= $row['title'] ?>"></td>
                    <td>Inicio: <?= $row['fecha_curso_inicio'] ?> <br> Fin: <?= $row['fecha_curso_fin'] ?></td>
                    <td>
                        <a href="admin_carousel.php?delete=<?= $row['id_carrousel'] ?>" class="btn delete-btn" onclick="return confirmDelete();">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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

        // Confirmación antes de eliminar
        function confirmDelete() {
            return confirm("¿Estás seguro de que deseas eliminar este anuncio?");
        }
    </script>
</body>
</html>
