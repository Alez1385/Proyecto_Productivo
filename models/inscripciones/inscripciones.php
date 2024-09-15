<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Inscripciones</title>
    <link rel="stylesheet" href="css/ins.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php";
        require_once "../../scripts/conexion.php";
        require_once "../../scripts/config.php";

        // Function to get inscriptions
        function getInscripciones(mysqli $conn, array $filtros = []): array
        {
            $sql = "SELECT i.id_inscripcion, i.fecha_inscripcion, i.estado,
            c.nombre_curso, e.id_estudiante, u.nombre, u.apellido
            FROM inscripciones i
            JOIN cursos c ON i.id_curso = c.id_curso
            JOIN estudiante e ON i.id_estudiante = e.id_estudiante
            JOIN usuario u ON e.id_usuario = u.id_usuario";

            $whereConditions = [];
            $params = [];
            $types = '';

            foreach ($filtros as $key => $value) {
                $whereConditions[] = "$key = ?";
                $params[] = $value;
                $types .= getParamType($value);
            }

            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }

            $sql .= " ORDER BY i.fecha_inscripcion DESC";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparing query: " . $conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error executing query: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $inscripciones = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();

            return $inscripciones;
        }

        function getParamType($value): string
        {
            if (is_int($value)) {
                return 'i';
            } elseif (is_float($value)) {
                return 'd';
            } elseif (is_string($value)) {
                return 's';
            } else {
                return 'b';
            }
        }

        // Process state changes
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_inscripcion = $_POST['id_inscripcion'];
            $nuevo_estado = $_POST['nuevo_estado'];
            $username = $_SESSION['username'];
            $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id_usuario_cambio = $row['id_usuario'];
            } else {
                // Manejar el error si el usuario no se encuentra
                die("Usuario no encontrado");
            } // Asumiendo que tienes una sesión con el ID del usuario actual

            $conn->begin_transaction();

            try {
                // Get current state
                $stmt = $conn->prepare("SELECT estado FROM inscripciones WHERE id_inscripcion = ?");
                $stmt->bind_param("i", $id_inscripcion);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $estado_anterior = $row['estado'];

                // Update state
                $stmt = $conn->prepare("UPDATE inscripciones SET estado = ? WHERE id_inscripcion = ?");
                $stmt->bind_param("si", $nuevo_estado, $id_inscripcion);
                $stmt->execute();

                // Insert into historial_inscripciones
                $stmt = $conn->prepare("INSERT INTO historial_inscripciones (id_inscripcion, estado_anterior, estado_nuevo, id_usuario_cambio) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("issi", $id_inscripcion, $estado_anterior, $nuevo_estado, $id_usuario_cambio);
                $stmt->execute();

                // Commit transaction
                $conn->commit();
            } catch (Exception $e) {
                // Rollback transaction if any error occurs
                $conn->rollback();
                die("Error processing state change: " . $e->getMessage());
            }
        }

        // Nueva función para obtener estudiantes
        function getEstudiantes($conn)
        {
            $sql = "SELECT e.id_estudiante, u.nombre, u.apellido 
            FROM estudiante e 
            JOIN usuario u ON e.id_usuario = u.id_usuario";
            $result = $conn->query($sql);
            if (!$result) {
                throw new Exception("Error fetching students: " . $conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        // Procesar nueva inscripción
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_inscripcion'])) {
            $id_estudiante = $_POST['id_estudiante'];
            $id_curso = $_POST['id_curso'];
            $fecha_inscripcion = date('Y-m-d');
            $estado = 'pendiente';

            $stmt = $conn->prepare("INSERT INTO inscripciones (id_estudiante, id_curso, fecha_inscripcion, estado) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $id_estudiante, $id_curso, $fecha_inscripcion, $estado);

            if ($stmt->execute()) {
                $mensaje = "<div class='mensaje exito'>Nueva inscripción creada con éxito.</div>";
            } else {
                $mensaje = "<div class='mensaje error'>Error al crear la inscripción: " . $conn->error . "</div>";
            }
        }

        // Get filters
        $filtros = [];
        if (isset($_GET['estado']) && $_GET['estado'] !== '') {
            $filtros['i.estado'] = $_GET['estado'];
        }
        if (isset($_GET['curso']) && $_GET['curso'] !== '') {
            $filtros['c.id_curso'] = $_GET['curso'];
        }

        try {
            $inscripciones = getInscripciones($conn, $filtros);
        } catch (Exception $e) {
            die("Error fetching inscriptions: " . $e->getMessage());
        }

        // Function to get courses
        function getCursos($conn)
        {
            $sql = "SELECT id_curso, nombre_curso FROM cursos";
            $result = $conn->query($sql);
            if (!$result) {
                throw new Exception("Error fetching courses: " . $conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        try {
            $cursos = getCursos($conn);
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
        ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Administración de Inscripciones</h1>
                </div>
                <div class="header-right">
                    <button id="nuevaInscripcionBtn" class="btn-primary">Nueva Inscripción</button>
                </div>
            </header>

            <section class="content">
                <?php if (isset($mensaje)): ?>
                    <div class="mensaje"><?php echo $mensaje; ?></div>
                <?php endif; ?>

                <div id="nuevaInscripcionModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Nueva Inscripción</h2>
                            <span class="close">&times;</span>
                        </div>
                        <form action="" method="POST" class="modal-form">
                            <select name="id_estudiante" required>
                                <option value="">Seleccione un estudiante</option>
                                <?php foreach ($estudiantes as $estudiante): ?>
                                    <option value="<?php echo $estudiante['id_estudiante']; ?>">
                                        <?php echo htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="id_curso" required>
                                <option value="">Seleccione un curso</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?php echo $curso['id_curso']; ?>">
                                        <?php echo htmlspecialchars($curso['nombre_curso']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="nueva_inscripcion" value="1">
                            <button type="submit" class="btn-primary">Crear Inscripción</button>
                        </form>
                    </div>
                </div>
                <!-- Filters -->
                <div class="filter-bar">
                    <form id="filterForm" action="" method="GET" class="filter-form">
                        <select name="estado" class="filter-select">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?php echo isset($_GET['estado']) && $_GET['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="aprobada" <?php echo isset($_GET['estado']) && $_GET['estado'] == 'aprobada' ? 'selected' : ''; ?>>Aprobada</option>
                            <option value="rechazada" <?php echo isset($_GET['estado']) && $_GET['estado'] == 'rechazada' ? 'selected' : ''; ?>>Rechazada</option>
                            <option value="cancelada" <?php echo isset($_GET['estado']) && $_GET['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                        </select>
                        <select name="curso" class="filter-select">
                            <option value="">Todos los cursos</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo htmlspecialchars($curso['id_curso']); ?>" <?php echo isset($_GET['curso']) && $_GET['curso'] == $curso['id_curso'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($curso['nombre_curso']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <div id="detallesModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Detalles de la inscripción</h2>
                            <span class="close">&times;</span>
                        </div>
                        <div class="modal-body">
                            <!-- Los detalles de la inscripción se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                </div>

                <div class="inscriptions-list">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Curso</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inscripciones as $inscripcion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inscripcion['id_inscripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($inscripcion['nombre'] . ' ' . $inscripcion['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($inscripcion['nombre_curso']); ?></td>
                                    <td><?php echo htmlspecialchars($inscripcion['fecha_inscripcion']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo htmlspecialchars($inscripcion['estado']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($inscripcion['estado'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form action="" method="POST" class="status-form">
                                            <input type="hidden" name="id_inscripcion" value="<?php echo htmlspecialchars($inscripcion['id_inscripcion']); ?>">
                                            <select name="nuevo_estado" onchange="this.form.submit()" class="status-select">
                                                <option value="pendiente" <?php echo $inscripcion['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                <option value="aprobada" <?php echo $inscripcion['estado'] == 'aprobada' ? 'selected' : ''; ?>>Aprobar</option>
                                                <option value="rechazada" <?php echo $inscripcion['estado'] == 'rechazada' ? 'selected' : ''; ?>>Rechazar</option>
                                                <option value="cancelada" <?php echo $inscripcion['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelar</option>
                                            </select>
                                        </form>
                                        <button class="btn-view" onclick="verDetalles(<?php echo $inscripcion['id_inscripcion']; ?>)">Ver Detalles</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Modal para nueva inscripción 
        var modal = document.getElementById("nuevaInscripcionModal");
        var btn = document.getElementById("nuevaInscripcionBtn");
        var span = modal.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Función para ver detalles de la inscripción
        function verDetalles(id_inscripcion) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'ajax/detalles_inscripcion.php?id_inscripcion=' + id_inscripcion, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var respuesta = JSON.parse(xhr.responseText);
                    var detallesModal = document.getElementById("detallesModal");
                    detallesModal.querySelector(".modal-body").innerHTML = `
                    <p>ID: ${respuesta.id_inscripcion}</p>
                    <p>Fecha de inscripción: ${respuesta.fecha_inscripcion}</p>
                    <p>Estado: ${respuesta.estado}</p>
                    <p>Curso: ${respuesta.nombre_curso}</p>
                    <p>Estudiante: ${respuesta.nombre} ${respuesta.apellido}</p>`;
                    detallesModal.style.display = "block";
                }
            };
            xhr.send();
        }

        // Cierre del modal de detalles
        var detallesModal = document.getElementById("detallesModal");
        var detallesCloseBtn = detallesModal.querySelector(".close");

        detallesCloseBtn.onclick = function() {
            detallesModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == detallesModal) {
                detallesModal.style.display = "none";
            }
        }
    // Obtener los elementos select de los filtros
    const filterSelects = document.querySelectorAll('.filter-select');

    // Agregar un evento 'change' para enviar el formulario cuando cambie la selección
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    </script>
</body>

</html>