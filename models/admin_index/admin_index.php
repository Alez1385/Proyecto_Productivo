<?php
require_once "../../scripts/auth.php";
require_once "../../scripts/conexion.php";

// Check if the user is logged in and has admin permissions
requireLogin();
checkPermission("admin");

// Define admin pages
$adminPages = [
    ['title' => 'Gestión de Carrusel', 'href' => '../../anuncios/admin_carousel.php'],
    ['title' => 'Resumen de Cursos', 'href' => '../../resume/admin_resume_cursos.php'],
  
   
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <style>
       * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-color: #f0f2f5;
    
}

.dashboard-container {
    display: flex;
    min-height: 100vh;
}

.main-content {
    flex: 1;
    padding: 2rem;
}

.header {
    margin-bottom: 2rem;
}

h1 {
    color: #333;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .card-grid {
        grid-template-columns: 1fr;
    }
}

.card {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.card h2 {
    color: #333;
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.card-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
    font-size: 1rem;
}

.card-button:hover {
    background-color: #0056b3;
}
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>

        <div class="main-content">
            <header class="header">
                <h1>Panel de Administración</h1>
            </header>

            <div class="card-grid">
                <?php foreach ($adminPages as $page): ?>
                    <div class="card">
                        <h2><?php echo htmlspecialchars($page['title']); ?></h2>
                        <button class="card-button" onclick="window.location.href='<?php echo htmlspecialchars($page['href']); ?>'">
                            Editar
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>