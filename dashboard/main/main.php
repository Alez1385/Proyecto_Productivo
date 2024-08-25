<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestión de Cursos</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="#usuarios">Usuarios</a></li>
                <li><a href="#cursos">Cursos</a></li>
                <li><a href="#profesores">Profesores</a></li>
                <li><a href="#estudiantes">Estudiantes</a></li>
                <li><a href="#asignaciones">Asignaciones</a></li>
                <li><a href="#pagos">Pagos</a></li>
            </ul>
        </aside>

        <main class="content">
            <header class="content-header">
                <h1>Dashboard</h1>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </header>

            <section id="dashboard-overview">
                <div class="card">
                    <h3>Total Usuarios</h3>
                    <p>35</p>
                </div>
                <div class="card">
                    <h3>Total Cursos</h3>
                    <p>12</p>
                </div>
                <div class="card">
                    <h3>Total Profesores</h3>
                    <p>8</p>
                </div>
                <div class="card">
                    <h3>Total Estudiantes</h3>
                    <p>120</p>
                </div>
            </section>

            <section id="usuarios">
                <h2>Gestión de Usuarios</h2>
                <!-- Aquí iría la tabla o lista de usuarios -->
            </section>

            <section id="cursos">
                <h2>Gestión de Cursos</h2>
                <!-- Aquí iría la tabla o lista de cursos -->
            </section>

            <section id="profesores">
                <h2>Gestión de Profesores</h2>
                <!-- Aquí iría la tabla o lista de profesores -->
            </section>

            <section id="estudiantes">
                <h2>Gestión de Estudiantes</h2>
                <!-- Aquí iría la tabla o lista de estudiantes -->
            </section>

            <section id="asignaciones">
                <h2>Gestión de Asignaciones</h2>
                <!-- Aquí iría la tabla o lista de asignaciones -->
            </section>

            <section id="pagos">
                <h2>Gestión de Pagos</h2>
                <!-- Aquí iría la tabla o lista de pagos -->
            </section>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
