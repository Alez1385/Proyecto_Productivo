    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Inicio</title>
        <link rel="stylesheet" href="css/dash.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>

    <body>
        <div class="dashboard-container">
            <?php
            include "../scripts/conexion.php";
            include "../scripts/sidebar.php";


            // Función para obtener datos de la base de datos
            function getDatabaseData($conn, $query)
            {
                $result = $conn->query($query);
                if (!$result) {
                    die("Error al ejecutar la consulta: " . $conn->error);
                }
                return $result->fetch_all(MYSQLI_ASSOC);
            }

            // Obtener cursos más populares
            $popular_courses = getDatabaseData($conn, "
    SELECT c.nombre_curso, COUNT(i.id_inscripcion) as inscripciones
    FROM cursos c
    LEFT JOIN inscripciones i ON c.id_curso = i.id_curso
    GROUP BY c.id_curso
    ORDER BY inscripciones DESC
    LIMIT 5
");

            // Obtener progreso promedio de estudiantes por curso
            $student_progress = getDatabaseData($conn, "
    SELECT c.nombre_curso, AVG(a.presente) as promedio_asistencia
    FROM cursos c
    JOIN asistencia a ON c.id_curso = a.id_curso
    GROUP BY c.id_curso
    ORDER BY promedio_asistencia DESC
    LIMIT 5
");

            // Obtener distribución de niveles educativos
            $education_levels = getDatabaseData($conn, "
    SELECT nivel_educativo, COUNT(*) as count
    FROM cursos
    GROUP BY nivel_educativo
");

            // Obtener últimos pagos
            $recent_payments = getDatabaseData(
                $conn,
                "
    SELECT p.monto, p.fecha_pago, u.nombre, u.apellido
    FROM pagos p
    JOIN estudiante e ON p.id_estudiante = e.id_estudiante
    JOIN usuario u ON e.id_usuario = u.id_usuario
    ORDER BY p.fecha_pago DESC
    LIMIT 5
"
            );

            // Manejo de errores en la conexión
            if (!$conn) {
                die("Error de conexión: " . mysqli_connect_error());
            }

            // Obtener información del usuario
            $sql = "SELECT u.*, t.nombre AS tipo_usuario FROM usuario u 
            JOIN tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario 
            WHERE u.username = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                die("Error al ejecutar la consulta: " . $stmt->error);
            }

            $user = $result->fetch_assoc();

            // Manejo de errores si no se encuentra el usuario
            if (!$user) {
                die("Usuario no encontrado");
            }

            // Obtener estadísticas generales
            $total_users = $conn->query("SELECT COUNT(*) as count FROM usuario");
            if (!$total_users) {
                die("Error al obtener total de usuarios: " . $conn->error);
            }
            $total_users = $total_users->fetch_assoc()['count'];

            $total_courses = $conn->query("SELECT COUNT(*) as count FROM cursos");
            if (!$total_courses) {
                die("Error al obtener total de cursos: " . $conn->error);
            }
            $total_courses = $total_courses->fetch_assoc()['count'];

            $total_students = $conn->query("SELECT COUNT(*) as count FROM estudiante");
            if (!$total_students) {
                die("Error al obtener total de estudiantes: " . $conn->error);
            }
            $total_students = $total_students->fetch_assoc()['count'];

            $total_teachers = $conn->query("SELECT COUNT(*) as count FROM profesor");
            if (!$total_teachers) {
                die("Error al obtener total de profesores: " . $conn->error);
            }
            $total_teachers = $total_teachers->fetch_assoc()['count'];

            // Obtener cursos más populares
            $popular_courses = $conn->query("
        SELECT c.nombre_curso, COUNT(i.id_inscripcion) as inscripciones
        FROM cursos c
        LEFT JOIN inscripciones i ON c.id_curso = i.id_curso
        GROUP BY c.id_curso
        ORDER BY inscripciones DESC
        LIMIT 5
    ");

            if (!$popular_courses) {
                die("Error al obtener cursos populares: " . $conn->error);
            }
            $popular_courses = $popular_courses->fetch_all(MYSQLI_ASSOC);

            ?>
            <div class="main-content">
                <header class="header">

                    <section class="user-info">

                        <h2>Información del Usuario</h2>
                        <div class="user-details">
                            <img src="../../uploads/<?php echo htmlspecialchars($user['foto']); ?>" alt="Foto de perfil">
                            <div>
                                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['mail']); ?></p>
                                <p><strong>Tipo de Usuario:</strong> <?php echo htmlspecialchars($user['tipo_usuario']); ?></p>
                                <p><strong>Último Acceso:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($user['ultimo_acceso']))); ?></p>
                            </div>
                        </div>
                    </section>
                </header>

                <section class="dashboard-summary">
                    <div class="summary-card">
                        <i class="fas fa-users"></i>
                        <h3>Usuarios Totales</h3>
                        <p><?php echo $total_users; ?></p>
                    </div>
                    <div class="summary-card">
                        <i class="fas fa-book"></i>
                        <h3>Cursos Totales</h3>
                        <p><?php echo $total_courses; ?></p>
                    </div>
                    <div class="summary-card">
                        <i class="fas fa-user-graduate"></i>
                        <h3>Estudiantes</h3>
                        <p><?php echo $total_students; ?></p>
                    </div>
                    <div class="summary-card">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h3>Profesores</h3>
                        <p><?php echo $total_teachers; ?></p>
                    </div>
                </section>

                <div class="dashboard-charts">
                    <div class="chart-container">
                        <h3>Cursos Más Populares</h3>
                        <canvas id="popularCoursesChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3>Progreso de Estudiantes por Curso</h3>
                        <canvas id="studentProgressChart"></canvas>
                    </div>
                </div>

                <div class="dashboard-charts chart-full-width">
                    <div class="chart-container">
                        <h3>Distribución de Niveles Educativos</h3>
                        <canvas id="educationLevelsChart"></canvas>
                    </div>
                </div>

                <section class="recent-payments">
                    <h3>Últimos Pagos</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['nombre'] . ' ' . $payment['apellido']); ?></td>
                                    <td>$<?php echo htmlspecialchars($payment['monto']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['fecha_pago']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </div>

        <script>
            // Gráfico de cursos populares
            var ctxPopular = document.getElementById('popularCoursesChart').getContext('2d');
            var popularCoursesChart = new Chart(ctxPopular, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($popular_courses, 'nombre_curso')); ?>,
                    datasets: [{
                        label: 'Inscripciones',
                        data: <?php echo json_encode(array_column($popular_courses, 'inscripciones')); ?>,
                        backgroundColor: 'rgba(52, 152, 219, 0.6)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Número de Inscripciones'
                            }
                        }
                    }
                }
            });

            // Gráfico de progreso de estudiantes por curso
            var ctxProgress = document.getElementById('studentProgressChart').getContext('2d');
            var studentProgressChart = new Chart(ctxProgress, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($student_progress, 'nombre_curso')); ?>,
                    datasets: [{
                        label: 'Promedio de Asistencia',
                        data: <?php echo json_encode(array_column($student_progress, 'promedio_asistencia')); ?>,
                        fill: false,
                        borderColor: 'rgb(231, 76, 60)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 1,
                            title: {
                                display: true,
                                text: 'Promedio de Asistencia'
                            }
                        }
                    }
                }
            });

            // Gráfico de distribución de niveles educativos
            var ctxEducation = document.getElementById('educationLevelsChart').getContext('2d');
            var educationLevelsChart = new Chart(ctxEducation, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode(array_column($education_levels, 'nivel_educativo')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($education_levels, 'count')); ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)'
                        ]
                    }]
                },
                // Dentro de las opciones del gráfico educationLevelsChart
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Distribución de Niveles Educativos'
                        }
                    }
                }
            });
        </script>
    </body>

    </html>