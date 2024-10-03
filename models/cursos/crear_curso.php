<div class="form-container">
    <form class="course-form" action="scripts/register_courses.php" method="post" enctype="multipart/form-data" id="newCourseForm">
        <h2>Nuevo Curso</h2>
        <div class="form-group">
            <input type="text" placeholder="Nombre del Curso" name="nombre_curso" required>
        </div>
        <div class="form-group">
            <textarea placeholder="Descripción del Curso" name="descripcion" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <select name="nivel_educativo" required>
                <option value="" disabled selected>Nivel Educativo</option>
                <option value="primaria">Primaria</option>
                <option value="secundaria">Secundaria</option>
                <option value="terciaria">Terciaria</option>
            </select>
        </div>
        <div class="form-group">
            <input type="number" placeholder="Duración en semanas" name="duracion" min="1" required>
        </div>
        <div class="form-group">
            <select name="estado" required>
                <option value="" disabled selected>Estado</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
        <div class="form-group">
            <select name="categoria" required>
                <option value="" disabled selected>Categoría</option>
                <?php
                include '../../scripts/conexion.php';
                $sql = "SELECT id_categoria, nombre_categoria FROM categoria_curso";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row["id_categoria"] . '">' . $row["nombre_categoria"] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <input type="file" name="upload_icon" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary" id="submitBtn">REGISTRAR CURSO</button>
    </form>
</div>