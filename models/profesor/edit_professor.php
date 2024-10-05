<?php
require_once "../../scripts/conexion.php";

// Obtener el ID del profesor a editar
$id_profesor = $_GET['id_profesor'];

// Consultar los datos del profesor
$query = "SELECT p.*, u.nombre, u.apellido FROM profesor p
          JOIN usuario u ON p.id_usuario = u.id_usuario
          WHERE p.id_profesor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_profesor);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();
?>

<div class="form-container">
    <form class="edit-form" action="scripts/update_professor.php" method="post" enctype="multipart/form-data">
        <h2>Editar Profesor</h2>

        <input type="hidden" name="id_profesor" value="<?php echo $professor['id_profesor']; ?>">

        <div class="form-group">
            <input type="text" placeholder="Nombre" name="nombre" value="<?php echo $professor['nombre']; ?>" readonly>
        </div>

        <div class="form-group">
            <input type="text" placeholder="Apellido" name="apellido" value="<?php echo $professor['apellido']; ?>" readonly>
        </div>

        <div class="form-group">
            <input type="text" placeholder="Especialidad" name="especialidad" value="<?php echo $professor['especialidad']; ?>" required>
        </div>

        <div class="form-group">
            <input type="number" placeholder="Años de Experiencia" name="experiencia" value="<?php echo $professor['experiencia']; ?>" min="0" required>
        </div>

        <div class="form-group">
            <textarea placeholder="Descripción" name="descripcion"><?php echo $professor['descripcion']; ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn">ACTUALIZAR</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.edit-form');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('input', function() {
        submitBtn.disabled = !form.checkValidity();
    });
});
</script>