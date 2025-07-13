// Mostrar/ocultar formulario de nuevo mensaje
function toggleNewMessageForm() {
    var form = document.getElementById('newMessageForm');
    var messagingContainer = document.querySelector('.messaging-container');
    var isHidden = form.style.display === 'none';
    
    if (isHidden) {
        form.style.display = 'flex';
        form.classList.remove('hiding');
        messagingContainer.classList.add('modal-active');
        // Enfocar el primer campo
        setTimeout(() => {
            document.getElementById('tipo_destinatario').focus();
        }, 100);
    } else {
        form.classList.add('hiding');
        setTimeout(() => {
            form.style.display = 'none';
            form.classList.remove('hiding');
            messagingContainer.classList.remove('modal-active');
        }, 300);
    }
}

// Mostrar campo ID de destinatario solo si el destinatario es individual
document.getElementById('tipo_destinatario').addEventListener('change', function() {
    var individualRecipient = document.getElementById('individualRecipient');
    if (this.value === 'individual') {
        individualRecipient.style.display = 'block';
        setTimeout(() => {
            document.getElementById('destinatario').focus();
        }, 100);
    } else {
        individualRecipient.style.display = 'none';
        document.getElementById('destinatario').value = '';
        document.getElementById('resultadosBusqueda').innerHTML = '';
    }
});

// Filtrar mensajes con animación
function filterMessages() {
    var input = document.getElementById("searchMessage").value.toLowerCase();
    var messages = document.getElementsByClassName("message-item");
    var visibleCount = 0;

    Array.from(messages).forEach(function(message, index) {
        var asunto = message.querySelector('.message-header')?.innerText.toLowerCase() || '';
        var preview = message.querySelector('.message-preview')?.innerText.toLowerCase() || '';
        var shouldShow = asunto.indexOf(input) > -1 || preview.indexOf(input) > -1;
        
        if (shouldShow) {
            message.style.display = '';
            message.style.animation = `slideInRight 0.3s ease-out ${index * 0.05}s both`;
            visibleCount++;
        } else {
            message.style.display = 'none';
        }
    });

    // Mostrar mensaje si no hay resultados
    var noResults = document.querySelector('.no-results');
    if (visibleCount === 0 && input.length > 0) {
        if (!noResults) {
            noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.innerHTML = `
                <i class="fas fa-search" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 15px;"></i>
                <h3>No se encontraron mensajes</h3>
                <p>Intenta con otros términos de búsqueda</p>
            `;
            document.getElementById('messageList').appendChild(noResults);
        }
        noResults.style.display = 'block';
    } else if (noResults) {
        noResults.style.display = 'none';
    }
}

// Limpiar búsqueda
function clearSearch() {
    document.getElementById('searchMessage').value = '';
    filterMessages();
}

// Cargar mensajes con animación
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
                messageList.innerHTML = `
                    <div class="no-messages">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 15px;"></i>
                        <h3>No hay mensajes disponibles</h3>
                        <p>Comienza enviando tu primer mensaje</p>
                    </div>
                `;
                return;
            }

            data.data.forEach((message, index) => {
                var messageItem = createMessageElement(message);
                messageItem.style.animation = `slideInRight 0.3s ease-out ${index * 0.05}s both`;
                messageList.appendChild(messageItem);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message, 'error');
            var messageList = document.getElementById('messageList');
            messageList.innerHTML = `
                <div class="no-messages">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #e74c3c; margin-bottom: 15px;"></i>
                    <h3>Error al cargar los mensajes</h3>
                    <p>${error.message}</p>
                </div>
            `;
        });
}

// Crear elemento de mensaje con diseño moderno
function createMessageElement(message) {
    var messageItem = document.createElement('div');
    messageItem.className = 'message-item';
    messageItem.setAttribute('data-id', message.id_mensaje);
    
    // Formatear fecha
    var fecha = new Date(message.fecha_envio);
    var fechaFormateada = fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    messageItem.innerHTML = `
        <div class="message-header">${message.asunto}</div>
        <div class="message-preview">${message.contenido ? message.contenido.substring(0, 60) + '...' : 'Sin contenido'}</div>
        <div class="message-meta">
            <small><i class="fas fa-clock" style="margin-right: 5px;"></i>${fechaFormateada}</small>
        </div>
        <button onclick="deleteMessage(${message.id_mensaje}, event)" class="delete-btn" title="Eliminar mensaje">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    messageItem.addEventListener('click', function(e) {
        if (!e.target.closest('.delete-btn')) {
            showMessage(message.id_mensaje);
            document.querySelectorAll('.message-item').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        }
    });
    
    return messageItem;
}

// Mostrar notificación mejorada
function showNotification(message, type) {
    // Remover notificaciones existentes
    var existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    var notification = document.createElement('div');
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}" style="margin-right: 10px;"></i>
        ${message}
    `;
    notification.className = 'notification ' + type;
    document.body.appendChild(notification);

    // Auto-remover después de 5 segundos
    setTimeout(function() {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Eliminar mensaje con confirmación modal
function deleteMessage(id_mensaje, event) {
    event.stopPropagation();
    
    var modal = document.getElementById('deleteModal');
    var confirmBtn = document.getElementById('confirmDelete');
    var cancelBtn = document.getElementById('cancelDelete');
    
    modal.style.display = 'block';
    document.body.classList.add('modal-active');
    
    confirmBtn.onclick = function() {
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
                loadMessages();
                resetMessageContent();
                
                const activeMessage = document.querySelector('.message-item.active');
                if (activeMessage) {
                    activeMessage.classList.remove('active');
                }
            } else {
                showNotification(data.message, 'error');
            }
            modal.style.display = 'none';
            document.body.classList.remove('modal-active');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al eliminar el mensaje', 'error');
            modal.style.display = 'none';
            document.body.classList.remove('modal-active');
        });
    };
    
    cancelBtn.onclick = function() {
        modal.style.display = 'none';
        document.body.classList.remove('modal-active');
    };
    
    // Cerrar modal al hacer clic fuera
    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-active');
        }
    };
}

// Resetear contenido del mensaje
function resetMessageContent() {
    var messageContent = document.getElementById('messageContent');
    messageContent.innerHTML = `
        <div class="no-message">
            <i class="fas fa-inbox"></i>
            <h3>No hay mensaje seleccionado</h3>
            <p>Selecciona un mensaje de la lista para ver su contenido</p>
        </div>
    `;
}

// Mostrar mensaje con diseño mejorado
function showMessage(id_mensaje) {
    fetch('get_message_content.php?id=' + id_mensaje)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            var messageContent = document.getElementById('messageContent');
            var fecha = new Date(data.fecha_envio);
            var fechaFormateada = fecha.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            messageContent.innerHTML = `
                <div class="message-container">
                    <div class="message-header">
                        <h2><i class="fas fa-envelope" style="margin-right: 10px; color: #4A90E2;"></i>${data.asunto}</h2>
                        <div class="message-meta">
                            <span><i class="fas fa-calendar" style="margin-right: 5px;"></i>${fechaFormateada}</span>
                            <span><i class="fas fa-hashtag" style="margin-right: 5px;"></i>ID: ${data.id_mensaje}</span>
                        </div>
                    </div>
                    <div class="message-info">
                        <div class="info-row">
                            <i class="fas fa-user" style="color: #4A90E2;"></i>
                            <strong>De:</strong> ${data.remitente_nombre} ${data.remitente_apellido}
                        </div>
                        <div class="info-row">
                            <i class="fas fa-users" style="color: #4A90E2;"></i>
                            <strong>Para:</strong> ${data.tipo_destinatario === 'individual' ? data.destinatario : data.tipo_destinatario}
                        </div>
                        ${data.id_destinatario ? `
                        <div class="info-row">
                            <i class="fas fa-id-card" style="color: #4A90E2;"></i>
                            <strong>ID Destinatario:</strong> ${data.id_destinatario}
                        </div>
                        ` : ''}
                    </div>
                    <div class="message-body">
                        <h3><i class="fas fa-comment" style="margin-right: 10px; color: #4A90E2;"></i>Contenido del mensaje:</h3>
                        <div class="content-text">${data.contenido}</div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.message === 'Mensaje no encontrado') {
                resetMessageContent();
                showNotification('El mensaje ha sido eliminado o no existe', 'error');
            } else {
                showNotification('Error al cargar el contenido del mensaje: ' + error.message, 'error');
            }
        });
}

// Buscar usuarios para autocompletado
function buscarUsuarios() {
    var input = document.getElementById('destinatario');
    var resultadosDiv = document.getElementById('resultadosBusqueda');
    
    if (input.value.length < 2) {
        resultadosDiv.innerHTML = '';
        return;
    }
    
    fetch('buscar_usuarios.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'email=' + encodeURIComponent(input.value)
    })
    .then(response => response.json())
    .then(data => {
        resultadosDiv.innerHTML = '';
        
        if (data.success && data.usuarios.length > 0) {
            data.usuarios.forEach(usuario => {
                var div = document.createElement('div');
                div.innerHTML = `
                    <i class="fas fa-user" style="margin-right: 8px; color: #4A90E2;"></i>
                    ${usuario.nombre} ${usuario.apellido} (${usuario.mail})
                `;
                div.addEventListener('click', function() {
                    input.value = usuario.mail;
                    resultadosDiv.innerHTML = '';
                });
                resultadosDiv.appendChild(div);
            });
        } else {
            var div = document.createElement('div');
            div.innerHTML = '<i class="fas fa-search" style="margin-right: 8px;"></i>No se encontraron usuarios';
            div.style.color = '#7f8c8d';
            resultadosDiv.appendChild(div);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadosDiv.innerHTML = '<div style="color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i> Error al buscar usuarios</div>';
    });
}

// Enviar mensaje
function sendMessage() {
    var form = document.getElementById('messageForm');
    var formData = new FormData(form);
    
    // Validar campos
    var tipoDestinatario = document.getElementById('tipo_destinatario').value;
    var asunto = document.getElementById('asunto').value.trim();
    var contenido = document.getElementById('contenido').value.trim();
    
    if (!tipoDestinatario) {
        showNotification('Por favor selecciona el tipo de destinatario', 'error');
        return;
    }
    
    if (!asunto) {
        showNotification('Por favor escribe un asunto', 'error');
        return;
    }
    
    if (!contenido) {
        showNotification('Por favor escribe el contenido del mensaje', 'error');
        return;
    }
    
    if (tipoDestinatario === 'individual') {
        var destinatario = document.getElementById('destinatario').value.trim();
        if (!destinatario) {
            showNotification('Por favor ingresa el correo del destinatario', 'error');
            return;
        }
    }
    
    // Mostrar loading
    var sendBtn = document.querySelector('button[onclick="sendMessage()"]');
    var originalText = sendBtn.innerHTML;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    sendBtn.disabled = true;
    
    fetch('send_message.php', {
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
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar el mensaje', 'error');
    })
    .finally(() => {
        sendBtn.innerHTML = originalText;
        sendBtn.disabled = false;
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    
    // Event listener para búsqueda de usuarios
    var destinatarioInput = document.getElementById('destinatario');
    if (destinatarioInput) {
        destinatarioInput.addEventListener('input', buscarUsuarios);
    }
    
    // Cerrar autocompletado al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#individualRecipient')) {
            document.getElementById('resultadosBusqueda').innerHTML = '';
        }
    });
});

// Animaciones CSS adicionales
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .info-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 8px 0;
    }
    
    .info-row i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .content-text {
        line-height: 1.8;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .no-results {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }
    
    .no-messages {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }
`;
document.head.appendChild(style);

