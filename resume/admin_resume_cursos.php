<?php
// admin_resume_cursos.php
include('../scripts/conexion.php');


$response = [
    'status' => 'error',
    'message' => 'Ocurrió un error',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener la acción
    $action = $_GET['action'] ?? '';

    // Agregar o editar curso
    $dia = $_POST['dia'];
    $nombre = $_POST['nombre'];
    $lugar = $_POST['lugar'];
    $descripcion = $_POST['descripcion'];

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO resume_cursos (dia, nombre, lugar, descripcion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $dia, $nombre, $lugar, $descripcion);
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Curso agregado exitosamente';
        }
    } elseif ($action == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("UPDATE resume_cursos SET dia = ?, nombre = ?, lugar = ?, descripcion = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $dia, $nombre, $lugar, $descripcion, $id);
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Curso actualizado exitosamente';
        }
    }
    echo json_encode($response);
    exit;
}

// Eliminar curso
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM resume_cursos WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Curso eliminado exitosamente';
    }
    echo json_encode($response);
    exit;
}

// Consulta para obtener todos los cursos
$cursos = $conn->query("SELECT * FROM resume_cursos");

?>

<?php
// Incluir el archivo de conexión
include('../scripts/conexion.php');

// Obtener la acción (agregar, editar o eliminar) y manejarla adecuadamente
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';
$curso = [];

// Consulta para obtener un curso si se está editando
if ($action == 'edit' && $id) {
    $sql = "SELECT * FROM resume_cursos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $curso = $result->fetch_assoc();
}

// Consulta para obtener todos los cursos
$cursos = $conn->query("SELECT * FROM resume_cursos");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Cursos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Administrar Cursos</h1>
<div class="container">
    <div class="form-wrap"> 
        <form id="cursoForm" action="admin_resume_cursos.php<?= $action == 'edit' ? "?action=edit&id=$id" : '?action=add' ?>" method="post">
        <div class="header">
        <a href="../models/admin_index/admin_index.php" id="back-button" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver</a><br></div>
       
   
            <div class="form-group">
                <label for="dia">Día:</label>
                <input type="text" id="dia" name="dia" value="<?= $curso['dia'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?= $curso['nombre'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label for="lugar">Lugar:</label>
                <input type="text" id="lugar" name="lugar" value="<?= $curso['lugar'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required><?= $curso['descripcion'] ?? '' ?></textarea>
            </div>
            <button type="submit"><?= $action == 'edit' ? 'Actualizar' : 'Agregar' ?> Curso</button>
        </form>
    </div>

    <!-- Lista de cursos -->
    <h2>Cursos Existentes</h2>
    <table>
        <thead>
            <tr>
                <th>Día</th>
                <th>Nombre</th>
                <th>Lugar</th>
                <th>descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $cursos->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['dia']) ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['lugar']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td>
                    <a href="?action=edit&id=<?= $row['id'] ?>" class="btn_editar">Editar</a> 
                    <a href="#" class="btn" onclick="eliminarCurso(<?= $row['id'] ?>)">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <span class="btn-close" onclick="closeModal()">&times;</span>
        <div class="modal-header">Mensaje</div>
        <div class="modal-body" id="modalMessage"> Se ha editado con éxito, felicidades </div>
        <div class="modal-footer">
            <button onclick="closeModal()">Aceptar</button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    // Función para eliminar curso
    function eliminarCurso(id) {
        if (confirm('¿Estás seguro de que quieres eliminar este curso?')) {
            fetch(`admin_resume_cursos.php?action=delete&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    mostrarModal(data.message);
                });
        }
    }

     // Mostrar modal y redirigir a la página principal
    function mostrarModal(mensaje) {
        document.getElementById('modalMessage').textContent = mensaje;
        const modal = document.getElementById('successModal');
        modal.style.display = "block";
    }

      // Cerrar el modal y redirigir al inicio
    function closeModal() {
        const modal = document.getElementById('successModal');
        modal.style.display = "none";
        window.location.href = "admin_resume_cursos.php"; // Redirigir al inicio
    }

    // Manejo de la respuesta del formulario
    const cursoForm = document.getElementById('cursoForm');
    cursoForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(cursoForm);
        const action = cursoForm.getAttribute('action');

        fetch(action, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            mostrarModal(data.message);
        });
    });

    // Mostrar modal con animación
function mostrarModal(mensaje) {
    document.getElementById('modalMessage').textContent = mensaje;
    const modal = document.getElementById('successModal');
    modal.classList.add('show');  // Añadir clase 'show' para activar la animación
}

// Cerrar el modal con animación
function closeModal() {
    const modal = document.getElementById('successModal');
    modal.classList.remove('show'); // Eliminar clase 'show'
    
    // Esperar a que la animación de opacidad termine antes de redirigir
    setTimeout(() => {
        window.location.href = "admin_resume_cursos.php";
    }, 300); // El tiempo debe coincidir con la duración de la transición en CSS
}

    // Cerrar el modal al hacer clic fuera de él
    window.onclick = function(event) {
        const modal = document.getElementById('successModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

</body>
</html>

