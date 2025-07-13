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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/mensajeria.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "../../scripts/sidebar.php"; ?>
    <main class="main-content">
        <div class="header">
            <h1><i class="fas fa-envelope-open-text" style="margin-right: 15px; color: #4A90E2;"></i>Sistema de Mensajería</h1>
            <button class="new-message-btn" onclick="toggleNewMessageForm()">
                <i class="fas fa-plus" style="margin-right: 8px;"></i>Nuevo Mensaje
            </button>
        </div>
        
        <div class="messaging-container">
            <div class="modal-overlay"></div>
            <div id="newMessageForm" class="new-message-form" style="display: none;">
                <div class="modal-content">
                    <h2><i class="fas fa-edit" style="margin-right: 10px;"></i>Redactar Nuevo Mensaje</h2>
                    <form id="messageForm">
                        <div>
                            <label for="tipo_destinatario"><i class="fas fa-users" style="margin-right: 5px;"></i>Tipo de destinatario:</label>
                            <select id="tipo_destinatario" name="tipo_destinatario" required>
                                <option value="">Selecciona el tipo de destinatario</option>
                                <option value="todos">Todos los usuarios</option>
                                <option value="estudiantes">Solo estudiantes</option>
                                <option value="profesores">Solo profesores</option>
                                <option value="individual">Usuario individual</option>
                            </select>
                        </div>
                        <div id="individualRecipient" style="display: none;">
                            <label for="destinatario"><i class="fas fa-user" style="margin-right: 5px;"></i>Destinatario:</label>
                            <div style="position: relative;">
                                <input type="email" id="destinatario" name="destinatario" placeholder="Buscar por correo electrónico..." autocomplete="off">
                                <i class="fas fa-search search-icon"></i>
                                <div id="resultadosBusqueda" class="autocomplete-items"></div>
                            </div>
                        </div>
                        <div>
                            <label for="asunto"><i class="fas fa-tag" style="margin-right: 5px;"></i>Asunto:</label>
                            <input type="text" id="asunto" name="asunto" placeholder="Escribe el asunto del mensaje..." required>
                        </div>
                        <div>
                            <label for="contenido"><i class="fas fa-comment" style="margin-right: 5px;"></i>Mensaje:</label>
                            <textarea id="contenido" name="contenido" rows="6" placeholder="Escribe tu mensaje aquí..." required></textarea>
                        </div>
                        <div style="display: flex; gap: 15px; margin-top: 20px;">
                            <button type="button" onclick="sendMessage()" style="flex: 1;">
                                <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>Enviar Mensaje
                            </button>
                            <button type="button" onclick="toggleNewMessageForm()" style="flex: 1;">
                                <i class="fas fa-times" style="margin-right: 8px;"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="message-list">
                <div class="search-bar">
                    <div style="position: relative; width: 100%;">
                        <input type="text" id="searchMessage" placeholder="🔍 Buscar mensajes..." onkeyup="filterMessages()">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
                <div id="messageList">
                    <!-- Mensajes cargados dinámicamente -->
                </div>
            </div>
            <div class="message-content" id="messageContent">
                <div class="no-message">
                    <i class="fas fa-inbox"></i>
                    <h3>No hay mensaje seleccionado</h3>
                    <p>Selecciona un mensaje de la lista para ver su contenido</p>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal de confirmación de eliminación -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h2><i class="fas fa-exclamation-triangle" style="color: #e74c3c; margin-right: 10px;"></i>Confirmar eliminación</h2>
        <p>¿Estás seguro de que quieres eliminar este mensaje? Esta acción no se puede deshacer.</p>
        <div class="modal-buttons">
            <button id="confirmDelete" class="btn btn-danger">
                <i class="fas fa-trash" style="margin-right: 5px;"></i>Eliminar
            </button>
            <button id="cancelDelete" class="btn btn-secondary">
                <i class="fas fa-times" style="margin-right: 5px;"></i>Cancelar
            </button>
        </div>
    </div>
</div>

<script src="mensajeria.js"></script>
</body>
</html>
