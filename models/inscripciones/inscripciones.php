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
        <?php
        include "../../scripts/sidebar.php";
        require_once "../../scripts/conexion.php";
        require_once "../../scripts/config.php";

        // Process state changes
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_inscripcion']) && isset($_POST['nuevo_estado'])) {
            $id_inscripcion = $_POST['id_inscripcion'];
            $nuevo_estado = $_POST['nuevo_estado'];
            $username = $_SESSION['username'] ?? '';

            $conn->begin_transaction();

            try {
                $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $id_usuario_cambio = $row['id_usuario'] ?? null;

                if (!$id_usuario_cambio) {
                    throw new Exception("Usuario no encontrado");
                }

                // Get current state
                $stmt = $conn->prepare("SELECT estado FROM inscripciones WHERE id_inscripcion = ?");
                $stmt->bind_param("i", $id_inscripcion);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $estado_anterior = $row['estado'];

                // Update state
                $stmt = $conn->prepare("UPDATE inscripciones SET estado = ?, fecha_actualizacion = NOW() WHERE id_inscripcion = ?");
                $stmt->bind_param("si", $nuevo_estado, $id_inscripcion);
                $stmt->execute();

                // Insert into historial_inscripciones
                $stmt = $conn->prepare("INSERT INTO historial_inscripciones (id_inscripcion, estado_anterior, estado_nuevo, id_usuario_cambio, fecha_cambio) VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("issi", $id_inscripcion, $estado_anterior, $nuevo_estado, $id_usuario_cambio);
                $stmt->execute();

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                die("Error processing state change: " . $e->getMessage());
            }
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

        // Function to get students
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

        try {
            $cursos = getCursos($conn);
            $estudiantes = getEstudiantes($conn);
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
                <div id="mensajeExito" class="mensaje exito" style="display: none;">Nueva inscripción creada con éxito.</div>

                <div id="nuevaInscripcionModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Nueva Inscripción</h2>
                            <span class="close">&times;</span>
                        </div>
                        <form id="nuevaInscripcionForm" class="modal-form" method="post" enctype="multipart/form-data">
                            <select name="id_estudiante" required>
                                <option value="">Seleccione un estudiante</option>
                                <?php foreach ($estudiantes as $estudiante): ?>
                                    <option value="<?= htmlspecialchars($estudiante['id_estudiante']) ?>">
                                        <?= htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="id_curso" required>
                                <option value="">Seleccione un curso</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= htmlspecialchars($curso['id_curso']) ?>">
                                        <?= htmlspecialchars($curso['nombre_curso']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="file-input">
                                <input type="file" name="comprobante_pago" id="comprobante_pago" accept="image/*" required>
                                <label for="comprobante_pago">
                                    <span class="file-name" id="file-name-display">Inserte Comprobante De Pago</span>
                                    <span class="file-name-before"></span>
                                </label>
                            </div>
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
                            <option value="pendiente" <?= isset($_GET['estado']) && $_GET['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="aprobada" <?= isset($_GET['estado']) && $_GET['estado'] == 'aprobada' ? 'selected' : '' ?>>Aprobada</option>
                            <option value="rechazada" <?= isset($_GET['estado']) && $_GET['estado'] == 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
                            <option value="cancelada" <?= isset($_GET['estado']) && $_GET['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                        <select name="curso" class="filter-select">
                            <option value="">Todos los cursos</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= htmlspecialchars($curso['id_curso']) ?>" <?= isset($_GET['curso']) && $_GET['curso'] == $curso['id_curso'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($curso['nombre_curso']) ?>
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
                        <tbody id="inscripcionesTableBody">
                            <!-- Las inscripciones se cargarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Función para cargar inscripciones
        function cargarInscripciones() {
            var filtros = $("#filterForm").serialize();
            $.ajax({
                url: "ajax/get_inscripciones.php",
                method: "GET",
                data: filtros,
                dataType: 'json',
                success: function(inscripciones) {
                    var tbody = $("#inscripcionesTableBody");
                    tbody.empty();
                    inscripciones.forEach(function(inscripcion) {
                        var row = `
                    <tr>
                        <td>${inscripcion.id_inscripcion}</td>
                        <td>${inscripcion.nombre} ${inscripcion.apellido}</td>
                        <td>${inscripcion.nombre_curso}</td>
                        <td>${inscripcion.fecha_inscripcion}</td>
                        <td>
                            <span class="status-badge ${inscripcion.estado}">
                                ${inscripcion.estado.charAt(0).toUpperCase() + inscripcion.estado.slice(1)}
                            </span>
                        </td>
                        <td>
                            <form action="" method="POST" class="status-form">
                                <input type="hidden" name="id_inscripcion" value="${inscripcion.id_inscripcion}">
                                <select name="nuevo_estado" onchange="cambiarEstado(this)" class="status-select">
                                    <option value="pendiente" ${inscripcion.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                    <option value="aprobada" ${inscripcion.estado === 'aprobada' ? 'selected' : ''}>Aprobar</option>
                                    <option value="rechazada" ${inscripcion.estado === 'rechazada' ? 'selected' : ''}>Rechazar</option>
                                    <option value="cancelada" ${inscripcion.estado === 'cancelada' ? 'selected' : ''}>Cancelar</option>
                                </select>
                            </form>
                            <button class="btn-view" onclick="verDetalles(${inscripcion.id_inscripcion})">Ver Detalles</button>
                        </td>
                    </tr>
                `;
                        tbody.append(row);
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error cargando inscripciones:", textStatus, errorThrown);
                }
            });
        }

        // Función para cambiar el estado de una inscripción
        function cambiarEstado(selectElement) {
            var form = $(selectElement).closest('form');
            $.ajax({
                url: "",
                method: "POST",
                data: form.serialize(),
                success: function(response) {
                    cargarInscripciones();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error cambiando estado:", textStatus, errorThrown);
                }
            });
        }

        // Cargar inscripciones al cargar la página
        $(document).ready(function() {
            cargarInscripciones();

            // Manejar cambios en los filtros
            $("#filterForm select").on("change", function() {
                cargarInscripciones();
            });
        });
        // Modal para nueva inscripción
        var modal = document.getElementById("nuevaInscripcionModal");
        var btn = document.getElementById("nuevaInscripcionBtn");
        var span = modal.querySelector(".close");

        // Abrir el modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Cerrar el modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Cerrar modal si se hace clic fuera del modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Enviar nueva inscripción usando Ajax
        $("#nuevaInscripcionForm").on("submit", function(event) {
    event.preventDefault();

    // Crear un objeto FormData para manejar tanto datos como archivos
    var formData = new FormData(this);

    $.ajax({
        url: "ajax/insertar_inscripcion.php", // Ruta al archivo PHP que procesa el formulario
        method: "POST",
        data: formData, // Usar FormData para manejar archivos
        contentType: false, // Evita que jQuery establezca el tipo de contenido incorrecto
        processData: false, // Evita que jQuery procese los datos, ya que estamos usando FormData
        dataType: 'json', // Esperar respuesta en formato JSON
        success: function(response) {
            if (response.success) {
                $("#mensajeExito").text(response.message).fadeIn().delay(3000).fadeOut();
                modal.style.display = "none";
                cargarInscripciones();
                $("#nuevaInscripcionForm")[0].reset();
            } else {
                // Manejar diferentes tipos de errores
                switch (response.message) {
                    case "Datos de entrada inválidos. Por favor, verifica los campos del formulario.":
                        $("#mensajeError").text("Por favor, verifica que todos los campos del formulario estén completos.").fadeIn().delay(5000).fadeOut();
                        break;
                    case "El estudiante ya está inscrito en este curso.":
                        $("#mensajeError").text("Ya estás inscrito en este curso.").fadeIn().delay(5000).fadeOut();
                        break;
                    case "Ya existe una inscripción para este estudiante en este curso.":
                        $("#mensajeError").text("Ya existe una inscripción para este curso.").fadeIn().delay(5000).fadeOut();
                        break;
                    case "El curso o el estudiante especificado no existe.":
                        $("#mensajeError").text("Hubo un problema con los datos del curso o del estudiante. Por favor, inténtalo de nuevo.").fadeIn().delay(5000).fadeOut();
                        break;
                    default:
                        $("#mensajeError").text("Ocurrió un error: " + response.message).fadeIn().delay(5000).fadeOut();
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Mostrar el error exacto devuelto por el servidor o el error de la llamada AJAX
            var errorMessage = jqXHR.responseJSON ? jqXHR.responseJSON.message : "Hubo un problema en el servidor.";
            $("#mensajeError").text("Error específico: " + errorMessage + " (AJAX error: " + textStatus + " - " + errorThrown + ")").fadeIn().delay(5000).fadeOut();
            console.error("Error AJAX:", textStatus, errorThrown, "Response:", jqXHR.responseText);
        }
    });
});


        // Función para ver detalles de una inscripción
        function verDetalles(idInscripcion) {
            $.ajax({
                url: "ajax/detalles_inscripcion.php",
                method: "GET",
                data: {
                    id_inscripcion: idInscripcion
                },
                dataType: 'json',
                success: function(data) {
                    var detallesModal = document.getElementById("detallesModal");
                    detallesModal.innerHTML = `
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title">Detalles de la inscripción</h2>
                                <span class="close">&times;</span>
                            </div>
                            <div class="modal-body">
                                <a href="${data.comprobante_pago}" target="_blank">
                                    <img src="${data.comprobante_pago}" alt="Comprobante de Pago" style="max-width: 100%; height: auto;">
                                </a>
                                <p><strong>ID:</strong> ${data.id_inscripcion}</p>
                                <p><strong>Estudiante:</strong> ${data.nombre} ${data.apellido}</p>
                                <p><strong>Curso:</strong> ${data.nombre_curso}</p>
                                <p><strong>Fecha de inscripción:</strong> ${data.fecha_inscripcion}</p>
                                <p><strong>Estado:</strong> ${data.estado}</p>
                                <p><strong>Historial de cambios:</strong></p>
                                <ul>
                                    ${data.historial_cambios.map(cambio => `<li>${cambio.estado_anterior} -> ${cambio.estado_nuevo} (${cambio.fecha_cambio})</li>`).join('')}
                                </ul>
                            </div>
                        </div>
                    `;
                    detallesModal.style.display = "block";

                    // Cierre del modal de detalles
                    var detallesCloseBtn = detallesModal.querySelector(".close");

                    detallesCloseBtn.onclick = function() {
                        detallesModal.style.display = "none";
                    }

                    window.onclick = function(event) {
                        if (event.target == detallesModal) {
                            detallesModal.style.display = "none";
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error cargando detalles:", textStatus, errorThrown);
                }
            });
        }

        document.getElementById('comprobante_pago').addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : 'Inserte Comprobante De Pago';
            document.getElementById('file-name-display').textContent = fileName;
        });
    </script>

</body>

</html>