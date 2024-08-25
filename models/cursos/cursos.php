<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Cursos</title>
    <link rel="stylesheet" href="../../css/form.css"> <!-- Asegúrate de que esta ruta sea correcta -->
</head>
<body>
    <div class="form-container">
        <form class="course-form" action="scripts/register_course.php" method="post">
            <h2>Registro de Curso</h2>

            <div class="form-group">
                <input type="hidden" name="id_curso">
            </div>

            <div class="form-group">
                
                <input type="text" id="nombre_curso" name="nombre_curso" placeholder="Nombre del Curso" required>
            </div>

            <div class="form-group">
                
                <textarea id="descripcion" name="descripcion" placeholder="Descripción del Curso" rows="4" required></textarea>
            </div>

            <div class="form-group">
                
                <select id="nivel_educativo" name="nivel_educativo" required>
                    <option value="" disabled selected>Seleccione el Nivel Educativo</option>
                    <option value="primaria">Primaria</option>
                    <option value="secundaria">Secundaria</option>
                    <option value="terciaria">Terciaria</option>
                </select>
            </div>

            <div class="form-group">
                
                <input type="number" id="duracion" name="duracion" placeholder="Duración en semanas" min="1" required>
            </div>

            <div class="form-group">
                
                <select id="estado" name="estado" required>
                    <option value="" disabled selected>Seleccione el Estado</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Curso</button>
        </form>
    </div>
</body>
</html>
