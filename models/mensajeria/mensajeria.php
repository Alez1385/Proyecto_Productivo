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
                        <label for="destinatario">ID Destinatario (individual):</label>
                        <input type="text" id="destinatario" name="destinatario">
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
                        <div>Asunto</div>
                        <div>Remitente</div>
                        <div>Fecha</div>
                        <div>Acciones</div>
                    </div>
                    <div id="messageList">
                        <!-- Mensajes cargados dinámicamente -->
                    </div>
                </div>

                <div class="message-content" id="messageContent">
                    <!-- Contenido del mensaje seleccionado -->
                </div>
            </div>
        </main>
    </div>

    <script>
        // Mostrar/ocultar formulario de nuevo mensaje
        function toggleNewMessageForm() {
            var form = document.getElementById('newMessageForm');
            var isHidden = form.style.display === 'none';
            form.style.display = isHidden ? 'block' : 'none';
            if (isHidden) {
                document.body.classList.add('modal-active');
            } else {
                document.body.classList.remove('modal-active');
            }
        }


        // Mostrar campo ID de destinatario solo si el destinatario es individual
        document.getElementById('tipo_destinatario').addEventListener('change', function() {
            var individualRecipient = document.getElementById('individualRecipient');
            individualRecipient.style.display = this.value === 'individual' ? 'block' : 'none';
        });

   // Filtrar mensajes
   function filterMessages() {
            var input = document.getElementById("searchMessage").value.toLowerCase();
            var messages = document.getElementsByClassName("message-item");

            Array.from(messages).forEach(function(message) {
                var asunto = message.getElementsByTagName("div")[0].innerText.toLowerCase();
                if (asunto.indexOf(input) > -1) {
                    message.style.display = "";
                } else {
                    message.style.display = "none";
                }
            });

        } // Filtrar mensajes
        function filterMessages() {
            var input = document.getElementById("searchMessage").value.toLowerCase();
            var messages = document.getElementsByClassName("message-item");

            Array.from(messages).forEach(function(message) {
                var asunto = message.getElementsByTagName("div")[0].innerText.toLowerCase();
                if (asunto.indexOf(input) > -1) {
                    message.style.display = "";
                } else {
                    message.style.display = "none";
                }
            });
        }

        // Limpiar búsqueda
        function clearSearch() {
            document.getElementById('searchMessage').value = '';
            filterMessages();
        }

        // Cargar mensajes al iniciar
        function loadMessages() {
    fetch('get_messages.php')  // Solicita los mensajes del servidor
        .then(response => response.json())  // Parsear la respuesta JSON
        .then(data => {
            var messageList = document.getElementById('messageList');
            messageList.innerHTML = '';  // Limpiar la lista de mensajes antes de cargar los nuevos

            data.forEach(message => {
                var messageItem = document.createElement('div');
                messageItem.className = 'message-item';
                messageItem.innerHTML = `
                    <div>${message.asunto}</div>
                    <div>${message.remitente}</div>
                    <div>${message.fecha_envio}</div>
                    <div>
                        <button onclick="showMessage(${message.id_mensaje})">Ver</button>
                        <button onclick="deleteMessage(${message.id_mensaje})" class="delete-btn">
                            <i class="material-icons-sharp">delete</i>
                        </button>
                    </div>
                `;
                messageList.appendChild(messageItem);  // Añadir el mensaje a la lista
            });
        })
        .catch(error => console.error('Error:', error));
}



        loadMessages(); // Cargar los mensajes cuando la página carga
        // Enviar nuevo mensaje
        function sendMessage() {
    var form = document.getElementById('messageForm');
    var formData = new FormData(form);

    fetch('insert_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar notificación de éxito
            showNotification('Mensaje enviado con éxito', 'success');
            // Cerrar el formulario
            toggleNewMessageForm();
            // Limpiar el formulario después de enviar el mensaje
            form.reset();
            // Recargar los mensajes después de enviar
            loadMessages();
        } else {
            // Mostrar notificación de error
            showNotification(data.message || 'Error al enviar el mensaje', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error en la conexión', 'error');
    });
}


          // Mostrar notificación
          function showNotification(message, type) {
            var notification = document.getElementById('notification');
            notification.innerText = message;
            notification.className = 'notification ' + type;
            notification.style.display = 'block';

            // Ocultar la notificación después de 3 segundos
            setTimeout(function() {
                notification.style.display = 'none';
            }, 3000);
        }

        // Eliminar mensaje
        function deleteMessage(id_mensaje) {
            if (confirm('¿Seguro que quieres eliminar este mensaje?')) {
                fetch('delete_message.php?id=' + id_mensaje, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadMessages();
                    } else {
                        alert('Error al eliminar el mensaje');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Inicializar la carga de mensajes
        loadMessages();
    </script>
</body>
</html>
