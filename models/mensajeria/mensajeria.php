<?php
// Incluir los archivos necesarios de conexión y sesión
include "../../scripts/conexion.php";

include "../../scripts/auth.php";


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Mensajería</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <link rel="stylesheet" href="css/mensajeria.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "../../scripts/sidebar.php"; ?>
    <main class="main-content">
        <div class="header">
            <h1>Sistema de Mensajería</h1>
            <button class="new-message-btn" onclick="toggleNewMessageForm()">Nuevo Mensaje</button>
        </div>
        
        <!-- Formulario para redactar un nuevo mensaje -->
        <div id="newMessageForm" style="display: none;" class="new-message-form">
            <h2>Redactar Nuevo Mensaje</h2>
            <form id="messageForm">
                <div>
                    <label for="tipo_destinatario">Tipo de destinatario:</label>
                    <select id="tipo_destinatario" name="tipo_destinatario" required>
                        <option value="todos">Todos</option>
                        <option value="estudiantes">Estudiantes</option>
                        <option value="profesores">Profesores</option>
                        <option value="individual">Individual</option>
                    </select>
                </div>
                <div id="individualRecipient" style="display: none;">
                    <label for="destinatario">Destinatario (Correo electrónico):</label>
                    <input type="email" id="destinatario" name="destinatario" autocomplete="off">
                    <div id="resultadosBusqueda" class="autocomplete-items"></div>
                </div>
                <div>
                    <label for="asunto">Asunto:</label>
                    <input type="text" id="asunto" name="asunto" required>
                </div>
                <div>
                    <label for="contenido">Mensaje:</label>
                    <textarea id="contenido" name="contenido" rows="5" required></textarea>
                </div>
                <button type="button" onclick="sendMessage()">Enviar</button>
                <button type="button" onclick="toggleNewMessageForm()">Cancelar</button>
            </form>
        </div>
        
        <div class="messaging-container">
            <div class="message-list">
                <div class="search-bar">
                    <input type="text" id="searchMessage" placeholder="Buscar mensaje..." onkeyup="filterMessages()">
                    <button onclick="clearSearch()">Limpiar</button>
                </div>
                <div class="message-table-header">
                    <div>ID</div>
                    <div>Asunto</div>
                    <div>Fecha</div>
                    <div>Acciones</div>
                </div>
                <div id="messageList">
                    <!-- Mensajes cargados dinámicamente -->
                </div>
            </div>
            <div class="message-content" id="messageContent">
                <div class="no-message">No has abierto ningún mensaje</div>
            </div>
        </div>
    </main>
</div>

<script src="mensajeria.js"></script>
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h2>Confirmar eliminación</h2>
        <p>¿Estás seguro de que quieres eliminar este mensaje?</p>
        <div class="modal-buttons">
            <button id="confirmDelete" class="btn btn-danger">Eliminar</button>
            <button id="cancelDelete" class="btn btn-secondary">Cancelar</button>
        </div>
    </div>
</div>
</body>
</html>