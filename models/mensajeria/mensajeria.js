// Mostrar/ocultar formulario de nuevo mensaje
function toggleNewMessageForm() {
    var form = document.getElementById('newMessageForm');
    var isHidden = form.style.display === 'none';
    
    if (isHidden) {
        form.style.display = 'block';
        form.classList.remove('hiding');
        document.body.classList.add('modal-active');
    } else {
        form.classList.add('hiding');
        setTimeout(() => {
            form.style.display = 'none';
            form.classList.remove('hiding');
            document.body.classList.remove('modal-active');
        }, 300); // Este tiempo debe coincidir con la duración de la animación
    }
}

// Mostrar campo ID de destinatario solo si el destinatario es individual
document.getElementById('tipo_destinatario').addEventListener('change', function() {
    var individualRecipient = document.getElementById('individualRecipient');
    if (this.value === 'individual') {
        individualRecipient.style.display = 'block';
    } else {
        individualRecipient.style.display = 'none';
        document.getElementById('destinatario').value = ''; // Limpiar el campo de destinatario individual
    }
});

// Filtrar mensajes
function filterMessages() {
    var input = document.getElementById("searchMessage").value.toLowerCase();
    var messages = document.getElementsByClassName("message-item");

    Array.from(messages).forEach(function(message) {
        var asunto = message.getElementsByTagName("div")[1].innerText.toLowerCase(); // Cambiado para buscar en el asunto
        message.style.display = asunto.indexOf(input) > -1 ? "" : "none";
    });
}

// Limpiar búsqueda
function clearSearch() {
    document.getElementById('searchMessage').value = '';
    filterMessages();
}
function loadMessages() {
fetch('get_messages.php')
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Error desconocido');
        }
        
        var messageList = document.getElementById('messageList');
        messageList.innerHTML = '';

        if (!Array.isArray(data.data) || data.data.length === 0) {
            messageList.innerHTML = '<div class="no-messages">No hay mensajes disponibles</div>';
            return;
        }

        data.data.forEach(message => {
            var messageItem = document.createElement('div');
            messageItem.className = 'message-item';
            messageItem.innerHTML = `
                <div>${message.id_mensaje}</div>
                <div>${message.asunto}</div>
                <div>${message.fecha_envio}</div>
                <div>
                    <button onclick="deleteMessage(${message.id_mensaje}, event)" class="delete-btn">
                        <i class="material-icons-sharp">delete</i>
                    </button>
                </div>
            `;
            messageItem.addEventListener('click', function() {
                showMessage(message.id_mensaje);
                document.querySelectorAll('.message-item').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
            messageList.appendChild(messageItem);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message, 'error');
        var messageList = document.getElementById('messageList');
        messageList.innerHTML = '<div class="no-messages">Error al cargar los mensajes</div>';
    });
}

// Enviar nuevo mensaje


// Mostrar notificación
function showNotification(message, type) {
    var notification = document.createElement('div');
    notification.textContent = message;
    notification.className = 'notification ' + type;
    document.body.appendChild(notification);

    setTimeout(function() {
        notification.remove();
    }, 5000);
}

// Eliminar mensaje
function deleteMessage(id_mensaje, event) {
event.stopPropagation(); // Evita que se abra el mensaje al hacer clic en eliminar
if (confirm('¿Estás seguro de que quieres eliminar este mensaje?')) {
    fetch('delete_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_mensaje=' + id_mensaje
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadMessages(); // Recargar la lista de mensajes
            resetMessageContent(); // Volver al estado inicial
            
            // Remover la clase 'active' del mensaje eliminado
            const activeMessage = document.querySelector('.message-item.active');
            if (activeMessage) {
                activeMessage.classList.remove('active');
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al eliminar el mensaje', 'error');
    });
}
}

function resetMessageContent() {
var messageContent = document.getElementById('messageContent');
messageContent.innerHTML = '<div class="no-message">No has abierto ningún mensaje</div>';
}

function showMessage(id_mensaje) {
fetch('get_message_content.php?id=' + id_mensaje)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        var messageContent = document.getElementById('messageContent');
        messageContent.innerHTML = `
            <div class="message-container">
                <div class="message-header">
                    <div class="message-subject">
                        <span class="subject-label">Asunto:</span>
                        <h2>${data.asunto}</h2>
                    </div>
                    <p class="message-meta">ID: ${data.id_mensaje}</p>
                </div>
                <div class="message-info">
                    <p><strong>De:</strong> ${data.remitente_nombre} ${data.remitente_apellido} (ID: ${data.id_remitente})</p>
                    <p><strong>Fecha:</strong> ${new Date(data.fecha_envio).toLocaleString()}</p>
                    <p><strong>Para:</strong> ${data.tipo_destinatario === 'individual' ? data.destinatario : data.tipo_destinatario}</p>
                    ${data.id_destinatario ? `<p><strong>ID Destinatario:</strong> ${data.id_destinatario}</p>` : ''}
                </div>
                <div class="content-box">
                    <h3>Contenido:</h3>
                    <div class="content-box-inner">${data.contenido}</div>
                </div>
            </div>
        `;
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.message === 'Mensaje no encontrado') {
            resetMessageContent();
            showNotification('El mensaje ha sido eliminado o no existe', 'info');
        } else {
            showNotification('Error al cargar el contenido del mensaje: ' + error.message, 'error');
        }
    });
}


function deleteMessage(id_mensaje, event) {
event.stopPropagation(); // Evita que se abra el mensaje al hacer clic en eliminar
if (confirm('¿Estás seguro de que quieres eliminar este mensaje?')) {
    fetch('delete_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_mensaje=' + id_mensaje
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadMessages(); // Recargar la lista de mensajes
            resetMessageContent(); // Volver al estado inicial
            
            // Remover la clase 'active' del mensaje eliminado
            const activeMessage = document.querySelector('.message-item.active');
            if (activeMessage) {
                activeMessage.classList.remove('active');
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al eliminar el mensaje', 'error');
    });
}
}

function resetMessageContent() {
var messageContent = document.getElementById('messageContent');
messageContent.innerHTML = '<div class="no-message">No has abierto ningún mensaje</div>';
}
// Inicializar la carga de mensajes
window.onload = function() {
    loadMessages();
};



// Función para buscar usuarios
function buscarUsuarios() {
    var input = document.getElementById('destinatario');
    var resultadosContainer = document.getElementById('resultadosBusqueda');
    var valor = input.value;

    // Limpiar resultados anteriores
    resultadosContainer.innerHTML = '';

    if (!valor) { return false; }

    fetch('buscar_usuarios.php?q=' + encodeURIComponent(valor))
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                data.forEach(usuario => {
                    var item = document.createElement("div");
                    item.innerHTML = `<strong>${usuario.mail.substr(0, valor.length)}</strong>${usuario.mail.substr(valor.length)} (${usuario.nombre} ${usuario.apellido})`;
                    item.addEventListener("click", function(e) {
                        input.value = usuario.mail;
                        resultadosContainer.innerHTML = '';
                    });
                    resultadosContainer.appendChild(item);
                });
            } else {
                var noResult = document.createElement("div");
                noResult.textContent = 'No se encontraron resultados';
                resultadosContainer.appendChild(noResult);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            var errorItem = document.createElement("div");
            errorItem.textContent = 'Error al buscar usuarios';
            resultadosContainer.appendChild(errorItem);
        });
}

// Agregar evento de input al campo de destinatario
document.getElementById('destinatario').addEventListener('input', buscarUsuarios);

// Cerrar la lista de autocompletado al hacer clic fuera de ella
document.addEventListener("click", function (e) {
    var resultadosContainer = document.getElementById('resultadosBusqueda');
    if (e.target.id !== 'destinatario' && e.target.id !== 'resultadosBusqueda') {
        resultadosContainer.innerHTML = '';
    }
});

function createMessageElement(message) {
    const messageItem = document.createElement('div');
    messageItem.className = 'message-item';
    messageItem.setAttribute('data-id', message.id);

    const avatar = document.createElement('div');
    avatar.className = 'message-avatar';
    avatar.textContent = message.sender.charAt(0).toUpperCase();

    const messageInfo = document.createElement('div');
    messageInfo.className = 'message-info';

    const sender = document.createElement('div');
    sender.className = 'message-sender';
    sender.textContent = message.sender;

    const preview = document.createElement('div');
    preview.className = 'message-preview';
    preview.textContent = message.subject;

    messageInfo.appendChild(sender);
    messageInfo.appendChild(preview);

    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'delete-btn';
    deleteBtn.innerHTML = '<i class="material-icons-sharp">delete</i>';
    deleteBtn.onclick = function() { showDeleteModal(message.id); };

    messageItem.appendChild(avatar);
    messageItem.appendChild(messageInfo);
    messageItem.appendChild(deleteBtn);

    messageItem.onclick = function() { loadMessageContent(message.id); };

    return messageItem;
}

// Función para actualizar la posición del manejador de redimensionamiento


// Función para hacer elementos redimensionables

// Asegurarse de que el DOM esté cargado antes de manipular elementos


// Modifica la función sendMessage para manejar mejor los errores
function sendMessage() {
    var form = document.getElementById('messageForm');
    var formData = new FormData(form);
    var tipo_destinatario = document.getElementById('tipo_destinatario').value;

    if (tipo_destinatario === 'individual') {
        var destinatario = document.getElementById('destinatario').value;
        formData.append('destinatario_email', destinatario);
    }

    fetch('insert_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            toggleNewMessageForm();
            form.reset();
            loadMessages();
        } else {
            showNotification(data.message || 'Error al enviar el mensaje', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error en la conexión: ' + error.message, 'error');
    });
}

