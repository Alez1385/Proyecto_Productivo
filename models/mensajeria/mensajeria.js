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
    individualRecipient.style.display = this.value === 'individual' ? 'block' : 'none';
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
function sendMessage() {
    var form = document.getElementById('messageForm');
    var formData = new FormData(form);

    fetch('insert_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text(); 
    })
    .then(text => {
        console.log('Respuesta del servidor:', text); // Depuración
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showNotification(data.message, 'success');
                toggleNewMessageForm();
                form.reset();
                loadMessages();
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error al parsear JSON:', error);
            showNotification('Error en la respuesta del servidor', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error en la conexión: ' + error.message, 'error');
    });
}

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
                <div class="message-content">
                    <h3>Contenido:</h3>
                    <div class="content-box">${data.contenido}</div>
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

// Función para hacer la lista de mensajes redimensionable
function makeResizable() {
    const messageListWrapper = document.querySelector('.message-list-wrapper');
    const messageList = document.querySelector('.message-list');
    const resizeHandle = document.querySelector('.resize-handle');
    const container = document.querySelector('.messaging-container');

    let isResizing = false;

    resizeHandle.addEventListener('mousedown', (e) => {
        isResizing = true;
        document.addEventListener('mousemove', resize);
        document.addEventListener('mouseup', stopResize);
    });

    function resize(e) {
        if (!isResizing) return;
        const containerRect = container.getBoundingClientRect();
        const newWidth = e.clientX - containerRect.left;
        const maxWidth = container.offsetWidth * 0.8; // 80% del ancho del contenedor
        const minWidth = 200; // Ancho mínimo

        if (newWidth > minWidth && newWidth < maxWidth) {
            messageListWrapper.style.width = `${newWidth}px`;
        }
    }

    function stopResize() {
        isResizing = false;
        document.removeEventListener('mousemove', resize);
        document.removeEventListener('mouseup', stopResize);
    }

    // Manejar el scroll horizontal
    messageList.addEventListener('scroll', () => {
        const scrollPercentage = messageList.scrollLeft / (messageList.scrollWidth - messageList.clientWidth);
        const handlePosition = scrollPercentage * (messageListWrapper.clientWidth - resizeHandle.clientWidth);
        resizeHandle.style.right = `${handlePosition}px`;
    });
}

// Llamar a esta función cuando se carga la página
window.addEventListener('load', makeResizable);
loadMessages();



function buscarUsuarios() {
    var input = document.getElementById('destinatario');
    var a = document.getElementById('resultadosBusqueda');
    var valor = input.value;

    // Cerrar cualquier lista ya abierta de valores autocompletados
    cerrarTodasListas();

    if (!valor) { return false; }

    fetch('buscar_usuarios.php?q=' + valor)
        .then(response => response.json())
        .then(data => {
            data.forEach(usuario => {
                var b = document.createElement("DIV");
                b.innerHTML = "<strong>" + usuario.mail.substr(0, valor.length) + "</strong>";
                b.innerHTML += usuario.mail.substr(valor.length);
                b.innerHTML += " (" + usuario.nombre + " " + usuario.apellido + ")";
                b.addEventListener("click", function(e) {
                    input.value = usuario.mail;
                    cerrarTodasListas();
                });
                a.appendChild(b);
            });
        })
        .catch(error => console.error('Error:', error));
}

function cerrarTodasListas(elmnt) {
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
        if (elmnt != x[i] && elmnt != document.getElementById('destinatario')) {
            x[i].parentNode.removeChild(x[i]);
        }
    }
}

document.addEventListener("click", function (e) {
    cerrarTodasListas(e.target);
});

document.getElementById('destinatario').addEventListener('input', buscarUsuarios);