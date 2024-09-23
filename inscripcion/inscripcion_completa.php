<?php
session_start();
require_once '../scripts/conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php?error=notauthenticated&redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die('Error: Usuario no encontrado. Por favor, contacte al administrador.');
}

$id_usuario = $user['id_usuario'];
$curso_id = $_GET['curso_id'] ?? null;

if (!$curso_id) {
    die('Error: No se especificó un curso.');
}

// Función para obtener detalles del curso
function getCursoDetails($conn, $curso_id)
{
    $stmt = $conn->prepare("SELECT * FROM cursos WHERE id_curso = ?");
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para obtener detalles del usuario
function getUserDetails($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT nombre, apellido, mail, telefono, direccion FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Procesar el formulario de inscripción
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y validar los datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    // Validaciones
    if (empty($nombre) || empty($email) || empty($telefono)) {
        $error_message = 'Error: Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Error: El email proporcionado no es válido.';
    } else {
        // Procesar el archivo subido
        $target_dir = "../uploads/comprobantes/";
        $file_name = basename($_FILES["comprobante"]["name"]);
        $target_file = $target_dir . time() . '_' . $file_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Comprobar si el archivo es una imagen real o un archivo falso
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["comprobante"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $error_message = "El archivo no es una imagen.";
                $uploadOk = 0;
            }
        }

        // Comprobar el tamaño del archivo
        if ($_FILES["comprobante"]["size"] > 500000) {
            $error_message = "Lo siento, tu archivo es demasiado grande.";
            $uploadOk = 0;
        }

        // Permitir ciertos formatos de archivo
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            $error_message = "Lo siento, solo se permiten archivos JPG, JPEG, PNG & GIF.";
            $uploadOk = 0;
        }

        // Comprobar si $uploadOk está establecido en 0 por un error
        if ($uploadOk == 0) {
            $error_message = "Lo siento, tu archivo no fue subido.";
            // Si todo está bien, intenta subir el archivo
        } else {
            if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {
                // Archivo subido correctamente, proceder con la inscripción
                $stmt = $conn->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado, fecha_actualizacion, comprobante_pago) VALUES (?, ?, NOW(), 'pendiente', NOW(), ?)");
                $stmt->bind_param("iis", $curso_id, $id_usuario, $target_file);

                if ($stmt->execute()) {
                    $inscripcion_id = $stmt->insert_id;
                    $success_message = "Inscripción completada con éxito. ID de inscripción: " . $inscripcion_id;
                    // Aquí puedes agregar lógica para enviar un email de confirmación
                } else {
                    $error_message = "Error al procesar la inscripción: " . $stmt->error;
                }
            } else {
                $error_message = "Lo siento, hubo un error al subir tu archivo.";
            }
        }
    }
}

// Obtener detalles del curso y del usuario
$curso = getCursoDetails($conn, $curso_id);
$usuario = getUserDetails($conn, $id_usuario);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción al Curso</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <h1>Inscripción al Curso</h1>

        <div class="course-details">
            <div class="course-image">
                <img src="../uploads/icons/<?php echo htmlspecialchars($curso['icono']); ?>" alt="<?php echo htmlspecialchars($curso['nombre_curso']); ?>">
            </div>
            <div class="course-info">
                <h2><?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
                <p class="description"><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                <div class="course-meta">
                    <span class="duration"><i class="icon-clock"></i> <?php echo htmlspecialchars($curso['duracion']); ?> semanas</span>
                    <span class="level"><i class="icon-graduation-cap"></i> <?php echo htmlspecialchars($curso['nivel_educativo']); ?></span>
                </div>
            </div>
        </div>

        <form action="scripts/save_ins.php?curso_id=<?php echo $curso_id; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['mail']); ?>" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
            </div>

            <div class="form-group">
                <label for="comprobante">Comprobante de pago:</label>
                <div class="file-upload">
                    <input type="file" id="comprobante" name="comprobante" accept="image/*" required>
                    <span class="file-upload-label">Seleccionar archivo</span>
                </div>
                <p class="file-info">Formatos aceptados: JPG, JPEG, PNG, GIF. Tamaño máximo: 500KB.</p>
                <div id="imagePreview" class="image-preview">
                    <img id="previewImage" src="#" alt="Vista previa del comprobante">
                </div>
            </div>

            <button type="submit">Completar Inscripción</button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('comprobante');
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>

</html>