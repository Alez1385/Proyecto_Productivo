<?php
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';

// Ensure user is logged in
requireLogin();

// Get user details
$id_usuario = $_SESSION['id_usuario'] ?? '';
$user = getUserInfo($conn, $id_usuario);

if (!$user) {
    die('Error: Usuario no encontrado. Por favor, contacte al administrador.');
}

$id_usuario = $user['id_usuario'];
$curso_id = filter_input(INPUT_GET, 'curso_id', FILTER_VALIDATE_INT);

if (!$curso_id) {
    die('Error: No se especificó un curso válido.');
}

// Function to get course details
function getCursoDetails($conn, $curso_id) {
    $stmt = $conn->prepare("SELECT * FROM cursos WHERE id_curso = ?");
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Process registration form
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "../uploads/comprobantes/";
    $file_name = basename($_FILES["comprobante"]["name"]);
    $target_file = $target_dir . time() . '_' . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file
    $uploadOk = validateFile($_FILES["comprobante"], $imageFileType);

    if ($uploadOk) {
        if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {
            if (processInscripcion($conn, $curso_id, $id_usuario, $target_file)) {
                $success_message = "Inscripción completada con éxito.";
            } else {
                $error_message = "Error al procesar la inscripción.";
            }
        } else {
            $error_message = "Lo siento, hubo un error al subir tu archivo.";
        }
    }
}

// Get course and user details
$curso = getCursoDetails($conn, $curso_id);

// Helper functions
function validateFile($file, $fileType) {
    $maxFileSize = 500000; // 500KB
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }

    if ($file['size'] > $maxFileSize) {
        return false;
    }

    $check = getimagesize($file["tmp_name"]);
    return $check !== false;
}

function processInscripcion($conn, $curso_id, $id_usuario, $comprobante) {
    // First, check if the estudiante exists
    $stmt = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // If the estudiante doesn't exist, create one
        $stmt = $conn->prepare("INSERT INTO estudiante (id_usuario) VALUES (?)");
        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            return false;
        }
        $id_estudiante = $stmt->insert_id;
    } else {
        $row = $result->fetch_assoc();
        $id_estudiante = $row['id_estudiante'];
    }
    
    // Check if there's a preinscripcion for this student and course
    $stmt = $conn->prepare("SELECT id_preinscripcion FROM preinscripciones WHERE id_usuario = ? AND id_curso = ?");
    $stmt->bind_param("ii", $id_usuario, $curso_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $id_preinscripcion = null;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt = $conn->prepare("delete from preinscripciones where id_preinscripcion = ?");
        $stmt->bind_param("i", $row['id_preinscripcion']);
        $stmt->execute();
    }

    // Now proceed with the inscription
    $stmt = $conn->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado, fecha_actualizacion, comprobante_pago) VALUES (?, ?, NOW(), 'pendiente', NOW(), ?)");
    $stmt->bind_param("iis", $curso_id, $id_estudiante, $comprobante);
    return $stmt->execute();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción al Curso</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="scripts/logAuthinfo.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
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

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?curso_id=' . $curso_id; ?>" method="post" enctype="multipart/form-data">
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
