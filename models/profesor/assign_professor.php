<?php
require_once "../../scripts/conexion.php";
?>

<div class="form-container">
    <form class="register-form" action="scripts/save_professor.php" method="post" enctype="multipart/form-data" id="newProfessorForm">
        <h2>Asignar Nuevo Profesor</h2>

        <div class="form-group">
            <select name="id_usuario" required>
                <option value="" disabled selected>Seleccionar Usuario</option>
                <?php
                $stmt = $conn->prepare("SELECT id_usuario, nombre, apellido FROM usuario WHERE id_tipo_usuario = 4 AND id_usuario NOT IN (SELECT id_usuario FROM profesor)");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id_usuario'] . "'>" . $row['nombre'] . " " . $row['apellido'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <input type="text" placeholder="Especialidad" name="especialidad" required>
        </div>

        <div class="form-group">
            <input type="number" placeholder="Años de Experiencia" name="experiencia" min="0" required>
        </div>

        <div class="form-group">
            <textarea placeholder="Descripción" name="descripcion"></textarea>
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn">ASIGNAR PROFESOR</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newProfessorForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('input', function() {
        submitBtn.disabled = !form.checkValidity();
    });
});
</script>