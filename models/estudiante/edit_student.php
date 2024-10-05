<?php
require_once "../../scripts/conexion.php";
/**
 * This function retrieves student data from the database and prepares it for editing.
 *
 * @param int $id_estudiante The unique identifier of the student to be edited.
 * @return array An associative array containing the student's data.
 * @throws mysqli_sql_exception If there is an error executing the SQL query.
 */
function getStudentDataForEdit($id_estudiante)
{
    global $conn; // Assuming $conn is a global variable representing the database connection

    // Prepare the SQL query
    $query = "SELECT e.*, u.* FROM estudiante e
              JOIN usuario u ON e.id_usuario = u.id_usuario
              WHERE e.id_estudiante = ?";
    $stmt = $conn->prepare($query);

    // Bind the parameters
    $stmt->bind_param("i", $id_estudiante);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the student data as an associative array
    $student = $result->fetch_assoc();

    // Return the student data
    return $student;
}

// Obtener el ID del estudiante a editar
$id_estudiante = $_GET['id_estudiante'];

// Consultar los datos del estudiante
$query = "SELECT e.*, u.* FROM estudiante e
          JOIN usuario u ON e.id_usuario = u.id_usuario
          WHERE e.id_estudiante = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_estudiante);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>
<div class="form-container">
<form class="edit-form" action="scripts/edit_student.php" method="post" enctype="multipart/form-data">
    <h2>Editar Estudiante</h2>

    <input type="hidden" name="id_estudiante" value="<?php echo $student['id_estudiante']; ?>">
    <input type="hidden" name="id_usuario" value="<?php echo $student['id_usuario']; ?>">

    <div class="form-group">
        <input type="text" placeholder="Nombre" name="nombre" value="<?php echo $student['nombre']; ?>" required>
    </div>

    <div class="form-group">
        <input type="text" placeholder="Apellido" name="apellido" value="<?php echo $student['apellido']; ?>" required>
    </div> 

    <div class="form-group">
        <select name="tipo_doc" required>
            <option value="" disabled>Tipo de Documento</option>
            <option value="ID" <?php if ($student['tipo_doc'] == 'ID') echo 'selected'; ?>>ID</option>
            <option value="Passport" <?php if ($student['tipo_doc'] == 'Passport') echo 'selected'; ?>>Pasaporte</option>
        </select>
    </div>

    <div class="form-group">
        <input type="text" placeholder="Número de Documento" name="documento" value="<?php echo $student['documento']; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
    </div>

    <div class="form-group">
        <input type="date" placeholder="Fecha de Nacimiento" name="fecha_nac" value="<?php echo $student['fecha_nac']; ?>" required>
    </div>

    <div class="form-group">
        <input type="file" name="foto" accept="image/*" id="fotoInput" onchange="previewImage(this);">
        <div id="imagePreview">
            <?php if ($student['foto']) : ?>
                <img src="../../uploads/<?php echo $student['foto']; ?>" alt="Student Image" width="100" id="previewImg">
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <input type="email" placeholder="Email" name="mail" id="email" value="<?php echo $student['mail']; ?>" required>
        <span id="emailError" class="error-message"></span>
    </div>

    <div class="form-group">
        <input type="tel" placeholder="Teléfono" name="telefono" value="<?php echo $student['telefono']; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
    </div>

    <div class="form-group">
        <input type="text" placeholder="Dirección" name="direccion" value="<?php echo $student['direccion']; ?>" required>
    </div>

    <div class="form-group">
        <select name="nivel_educativo" require>
            <option value="" disabled>Nivel Educativo</option>
            <option value="primaria" <?php if ($student['nivel_educativo'] == 'primaria') echo 'selected'; ?>>Primaria</option>
            <option value="secundaria" <?php if ($student['nivel_educativo'] == 'secundaria') echo 'selected'; ?>>Secundaria</option>
            <option value="terciaria" <?php if ($student['nivel_educativo'] == 'terciaria') echo 'selected'; ?>>Terciaria</option>
        </select>
    </div>

    <div class="form-group">
        <select name="genero" required>
            <option value="" disabled>Género</option>
            <option value="M" <?php if ($student['genero'] == 'M') echo 'selected'; ?>>Masculino</option>
            <option value="F" <?php if ($student['genero'] == 'F') echo 'selected'; ?>>Femenino</option>
            <option value="O" <?php if ($student['genero'] == 'O') echo 'selected'; ?>>Otro</option>
        </select>
    </div>

    <div class="form-group">
        <textarea placeholder="Observaciones" name="observaciones"><?php echo $student['observaciones']; ?></textarea>
    </div>

    <div class="form-group">
        <input type="text" placeholder="Nombre de Usuario" name="username" id="username" value="<?php echo $student['username']; ?>" required>
        <span id="usernameError" class="error-message"></span>
    </div>

    <div class="form-group">
        <input type="password" placeholder="Contraseña" name="clave" id="password">
        <img src="../../login/img/eye-open.svg" alt="Toggle Lock" class="lock-icon" id="toggleLock" onclick="togglePassword('password', 'toggleLock')">
    </div>

    <button type="submit" class="btn btn-primary" id="submitBtn">ACTUALIZAR</button>
</form>
</div>

<script>
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

    document.addEventListener('DOMContentLoaded', function() {
        setupFormValidation();
    });
</script>