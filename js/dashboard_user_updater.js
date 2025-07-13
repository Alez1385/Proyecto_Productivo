// Dashboard User Updater
const dashboardUserUpdater = {
    init: function() {
        this.setupEventListeners();
        this.startAutoRefresh();
        this.updateDashboardData();
    },

    setupEventListeners: function() {
        // Event listeners para interacciones del usuario
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('mensaje-item')) {
                this.handleMensajeClick(e.target);
            }
        });
    },

    startAutoRefresh: function() {
        // Actualizar datos cada 30 segundos
        setInterval(() => {
            this.updateDashboardData();
        }, 30000);
    },

    updateDashboardData: function() {
        this.updateMensajesCount();
        this.updateCursosDisponibles();
    },

    updateMensajesCount: function() {
        const mensajesCountElement = document.getElementById('mensajes-count');
        if (!mensajesCountElement) return;

        fetch('../scripts/get_user_mensajes_count.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_usuario: this.getUserId()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mensajesCountElement.textContent = data.count;
                
                // Cambiar color si hay mensajes nuevos
                if (data.count > 0) {
                    mensajesCountElement.style.color = '#ff6b6b';
                    mensajesCountElement.style.fontWeight = 'bold';
                } else {
                    mensajesCountElement.style.color = 'inherit';
                    mensajesCountElement.style.fontWeight = 'normal';
                }
            }
        })
        .catch(error => {
            console.error('Error actualizando contador de mensajes:', error);
        });
    },

    updateCursosDisponibles: function() {
        const cursosDisponiblesElement = document.getElementById('cursos-disponibles');
        if (!cursosDisponiblesElement) return;

        fetch('../scripts/get_cursos_disponibles_count.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cursosDisponiblesElement.textContent = data.count;
            }
        })
        .catch(error => {
            console.error('Error actualizando contador de cursos:', error);
        });
    },

    handleMensajeClick: function(mensajeElement) {
        // Marcar mensaje como leído
        const mensajeId = mensajeElement.dataset.mensajeId;
        if (mensajeId) {
            this.marcarMensajeComoLeido(mensajeId);
        }
    },

    marcarMensajeComoLeido: function(mensajeId) {
        fetch('../scripts/marcar_mensaje_leido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_mensaje: mensajeId,
                id_usuario: this.getUserId()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar contador de mensajes
                this.updateMensajesCount();
            }
        })
        .catch(error => {
            console.error('Error marcando mensaje como leído:', error);
        });
    },

    getUserId: function() {
        // Obtener el ID del usuario desde el elemento del DOM
        const userIdElement = document.querySelector('[data-user-id]');
        return userIdElement ? userIdElement.dataset.userId : null;
    },

    showError: function(message) {
        const errorElement = document.getElementById('error-message');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            
            // Ocultar después de 5 segundos
            setTimeout(() => {
                errorElement.style.display = 'none';
            }, 5000);
        }
    },

    showSuccess: function(message) {
        // Crear notificación de éxito
        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        `;

        document.body.appendChild(notification);

        // Remover después de 3 segundos
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    },

    // Función para actualizar la información del usuario
    updateUserInfo: function() {
        fetch('../scripts/get_user_info.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_usuario: this.getUserId()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateUserDetails(data.user);
            }
        })
        .catch(error => {
            console.error('Error actualizando información del usuario:', error);
        });
    },

    updateUserDetails: function(userData) {
        // Actualizar elementos de información del usuario
        const elements = {
            'username': userData.username,
            'email': userData.mail,
            'telefono': userData.telefono || 'No registrado',
            'direccion': userData.direccion,
            'fecha_registro': new Date(userData.fecha_registro).toLocaleDateString('es-ES'),
            'estado': userData.estado
        };

        Object.keys(elements).forEach(key => {
            const element = document.querySelector(`[data-user-${key}]`);
            if (element) {
                element.textContent = elements[key];
            }
        });

        // Actualizar días registrado
        const diasRegistradoElement = document.getElementById('dias-registrado');
        if (diasRegistradoElement && userData.fecha_registro) {
            const fechaRegistro = new Date(userData.fecha_registro);
            const hoy = new Date();
            const diferencia = Math.floor((hoy - fechaRegistro) / (1000 * 60 * 60 * 24));
            diasRegistradoElement.textContent = diferencia;
        }
    },

    // Función para cargar mensajes dinámicamente
    loadMensajes: function() {
        fetch('../scripts/get_user_mensajes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_usuario: this.getUserId(),
                limit: 5
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateMensajesList(data.mensajes);
            }
        })
        .catch(error => {
            console.error('Error cargando mensajes:', error);
        });
    },

    updateMensajesList: function(mensajes) {
        const mensajesList = document.getElementById('mensajes-list');
        if (!mensajesList) return;

        if (mensajes.length === 0) {
            mensajesList.innerHTML = '<p class="no-mensajes">No hay mensajes recientes.</p>';
            return;
        }

        const mensajesHTML = mensajes.map(mensaje => `
            <div class="mensaje-item" data-mensaje-id="${mensaje.id_mensaje}">
                <div class="mensaje-header">
                    <span class="remitente">${mensaje.nombre} ${mensaje.apellido}</span>
                    <span class="fecha">${new Date(mensaje.fecha_envio).toLocaleString('es-ES')}</span>
                </div>
                <div class="mensaje-asunto">${mensaje.asunto}</div>
                <div class="mensaje-contenido">${mensaje.contenido.substring(0, 100)}${mensaje.contenido.length > 100 ? '...' : ''}</div>
            </div>
        `).join('');

        mensajesList.innerHTML = mensajesHTML;
    }
};

// Agregar estilos CSS para las animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style); 