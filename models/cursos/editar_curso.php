<?php
// Database connection
require_once '../../scripts/conexion.php';

// Check if an ID was provided in the URL
if (!isset($_GET['id_curso']) || !is_numeric($_GET['id_curso'])) {
    die("ID de curso no válido");
}

$id_curso = intval($_GET['id_curso']);

// Prepare and execute the query
$stmt = $conn->prepare("SELECT * FROM cursos WHERE id_curso = ?");
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();
$curso = $result->fetch_assoc();

// If no course was found, display an error
if (!$curso) {
    die("Curso no encontrado");
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso</title>
    <link rel="stylesheet" href="../../css/form.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
</head>
<body>
    <div class="form-container">
        <form class="course-form" action="scripts/update.php" method="post" enctype="multipart/form-data">
            <h2>Editar Curso</h2>

            <div class="form-group">
                <input type="hidden" name="id_curso" value="<?php echo htmlspecialchars($curso['id_curso']); ?>">
            </div>

            <div class="form-group">
                <input type="text" id="nombre_curso" name="nombre_curso" placeholder="Nombre del Curso" value="<?php echo htmlspecialchars($curso['nombre_curso']); ?>" required>
            </div>

            <div class="form-group">
                <textarea id="descripcion" name="descripcion" placeholder="Descripción del Curso" rows="4" required><?php echo htmlspecialchars($curso['descripcion']); ?></textarea>
            </div>

            <div class="form-group">
                <select id="nivel_educativo" name="nivel_educativo" required>
                    <option value="" disabled>Seleccione el Nivel Educativo</option>
                    <option value="primaria" <?php echo ($curso['nivel_educativo'] == 'primaria') ? 'selected' : ''; ?>>Primaria</option>
                    <option value="secundaria" <?php echo ($curso['nivel_educativo'] == 'secundaria') ? 'selected' : ''; ?>>Secundaria</option>
                    <option value="terciaria" <?php echo ($curso['nivel_educativo'] == 'terciaria') ? 'selected' : ''; ?>>Terciaria</option>
                </select>
            </div>

            <div class="form-group">
                <input type="number" id="duracion" name="duracion" placeholder="Duración en semanas" value="<?php echo htmlspecialchars($curso['duracion']); ?>" min="1" required>
            </div>

            <div class="form-group">
                <select id="estado" name="estado" required>
                    <option value="" disabled>Seleccione el Estado</option>
                    <option value="activo" <?php echo ($curso['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                    <option value="inactivo" <?php echo ($curso['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>

            <div class="form-group">
                <select id="categoria" name="categoria" required>
                    <option value="" disabled>Seleccione la Categoría</option>
                    <?php
                    // Consulta para obtener las categorías desde la base de datos
                    include '../../scripts/conexion.php';
                    $sql = "SELECT id_categoria, nombre_categoria FROM categoria_curso";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $selected = ($curso['id_categoria'] == $row['id_categoria']) ? 'selected' : '';
                        echo '<option value="' . $row["id_categoria"] . '" ' . $selected . '>' . $row["nombre_categoria"] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <div class="icon-upload">
                    <label for="upload_icon">Suba la imagen del curso:</label>
                    <input type="file" id="upload_icon" name="upload_icon" accept="image/*">
                </div>
                <?php if (!empty($curso['icono'])): ?>
                    <p>Imagen actual:</p>
                    <img src="../../uploads/icons/<?php echo htmlspecialchars($curso['icono']); ?>" alt="Icono del curso" style="width: 50px; height: 50px;">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <a href="cursos.php" style="color: #00bcff;">Volver</a>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
