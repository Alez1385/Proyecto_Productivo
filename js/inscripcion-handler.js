document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar todos los botones de inscripción
    var inscribirseBtns = document.querySelectorAll(".inscribirse-btn");

    // Añadir event listener a cada botón
    inscribirseBtns.forEach(function (btn) {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            var cursoId = this.getAttribute("data-curso-id");
            agregarEstilos();
            mostrarModalInscripcion(cursoId);
        });
    });

    // Función para agregar estilos dinámicamente
    function agregarEstilos() {
        if (!document.getElementById('estilosModal')) {
            var estilos = `
                .modal {
                    display: none;
                    position: fixed;
                    z-index: 1000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    overflow: hidden;
                    background-color: rgba(0,0,0,0.4);
                    backdrop-filter: blur(5px);
                }
                .modal-content {
                    background-color: #fefefe;
                    margin: auto;
                    padding: 20px;
                    border: 1px solid #888;
                    width: 80%;
                    max-width: 500px;
                    max-height: 80vh;
                    overflow-y: auto;
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .close {
                    color: #aaa;
                    float: right;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .close:hover,
                .close:focus {
                    color: black;
                    text-decoration: none;
                    cursor: pointer;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    margin: 10px 0;
                    border: none;
                    cursor: pointer;
                    text-align: center;
                    text-decoration: none;
                    outline: none;
                    color: #fff;
                    background-color: #4CAF50;
                    border-radius: 5px;
                    transition: background-color 0.3s;
                }
                .btn:hover {background-color: #45a049;}
                .btn:active {
                    background-color: #3e8e41;
                    transform: translateY(1px);
                }
                .btn-primary {
                    background-color: #008CBA;
                }
                .btn-secondary {
                    background-color: #555555;
                }
                .form-group {
                    margin-bottom: 15px;
                }
                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                }
                .form-group input {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    box-sizing: border-box;
                }
            `;

            var styleElement = document.createElement('style');
            styleElement.id = 'estilosModal';
            styleElement.textContent = estilos;
            document.head.appendChild(styleElement);
        }
    }

    // Función para mostrar el modal de inscripción
    function mostrarModalInscripcion(cursoId) {
        // Crear el modal reutilizable
        var modalHTML = `
            <div class="modal" id="inscripcionModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Opciones de Inscripción</h5>
                        <span class="close" onclick="cerrarModal('inscripcionModal')">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>Elija una opción para inscribirse al curso:</p>
                        <button class="btn btn-primary" id="preinscripcionBtn">Preinscripción Rápida</button>
                        <button class="btn btn-secondary" id="inscripcionCompletaBtn">Inscripción Completa</button>
                    </div>
                </div>
            </div>
        `;

        // Eliminar modal existente si lo hay
        var existingModal = document.getElementById("inscripcionModal");
        if (existingModal) {
            existingModal.remove();
        }

        // Añadir el nuevo modal al body
        document.body.insertAdjacentHTML("beforeend", modalHTML);
        var modal = document.getElementById("inscripcionModal");

        // Mostrar el modal
        modal.style.display = "block";

        // Añadir eventos a los botones del modal
        document.getElementById("preinscripcionBtn").addEventListener("click", function () {
            cerrarModal('inscripcionModal');
            preinscripcionRapida(cursoId);
        });

        document.getElementById("inscripcionCompletaBtn").addEventListener("click", function () {
            cerrarModal('inscripcionModal');
            inscripcionCompleta(cursoId);
        });
    }
    
    // Función para obtener datos del usuario desde el servidor
    async function obtenerDatosUsuario() {
        try {
            const response = await fetch('scripts/obtener_datos_usuario.php', {
                method: 'GET',
                credentials: 'include', // Asegura que las cookies se envíen con la solicitud
                headers: {
                    'Content-Type': 'application/json'
                }
            });
    
            if (!response.ok) {
                throw new Error('No estás autenticado para obtener tus datos');
            }
    
            const data = await response.json();
            // Procesar los datos del usuario aquí
            console.log(data);
        } catch (error) {
            console.error('Error al obtener datos del usuario:', error);
        }
    }
    

    // Función para manejar la preinscripción rápida
    function preinscripcionRapida(cursoId) {
        obtenerDatosUsuario().then(datosUsuario => {
            console.log("Datos del usuario obtenidos:", datosUsuario);

            if (datosUsuario.nombre && datosUsuario.email && datosUsuario.telefono) {
                // Si tenemos todos los datos necesarios, enviar directamente la preinscripción
                enviarPreinscripcion(cursoId, datosUsuario);
            } else {
                // Si falta algún dato, mostrar el formulario de preinscripción
                mostrarFormularioPreinscripcion(cursoId, datosUsuario);
            }
        });
    }

    function enviarPreinscripcion(cursoId, datosUsuario) {
        let formData = new FormData();
        formData.append('curso_id', cursoId);
        formData.append('nombre', datosUsuario.nombre);
        formData.append('email', datosUsuario.email);
        formData.append('telefono', datosUsuario.telefono);

        fetch('scripts/preinscribir.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            alert(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error al intentar preinscribirte. Por favor, intenta nuevamente más tarde.");
        });
    }

    function mostrarFormularioPreinscripcion(cursoId, datosUsuario = {}) {
        var preinscripcionHTML = `
            <div class="modal" id="preinscripcionModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Preinscripción Rápida</h5>
                        <span class="close" onclick="cerrarModal('preinscripcionModal')">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="preinscripcionForm">
                            <input type="hidden" name="curso_id" value="${cursoId}">
                            <div class="form-group">
                                <label for="nombre">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre" value="${datosUsuario.nombre || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Correo electrónico</label>
                                <input type="email" id="email" name="email" value="${datosUsuario.email || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" value="${datosUsuario.telefono || ''}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar Preinscripción</button>
                        </form>
                    </div>
                </div>
            </div>
        `;

        // Eliminar modal existente si lo hay
        var existingModal = document.getElementById("preinscripcionModal");
        if (existingModal) {
            existingModal.remove();
        }

        // Añadir el nuevo modal al body
        document.body.insertAdjacentHTML("beforeend", preinscripcionHTML);
        var preinscripcionModal = document.getElementById("preinscripcionModal");

        // Mostrar el modal
        preinscripcionModal.style.display = "block";

        // Manejar el envío del formulario
        document.getElementById("preinscripcionForm").addEventListener("submit", function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            // Enviar los datos vía fetch
            fetch('scripts/preinscribir.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                alert(data);
                cerrarModal('preinscripcionModal');
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Error al intentar preinscribirte. Por favor, intenta nuevamente más tarde.");
            });
        });
    }

    // Función para manejar la inscripción completa
    function inscripcionCompleta(cursoId) {
        fetch('scripts/verificar_usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'curso_id=' + cursoId
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            if (data === "logueado") {
                // Redirigir al formulario de inscripción completa
                window.location.href = "inscripcion_completa.php?curso_id=" + cursoId;
            } else {
                // Redirigir al login si no está logueado
                window.location.href = "login.php?redirect=inscripcion_completa.php&curso_id=" + cursoId;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error al verificar la sesión. Por favor, intenta nuevamente más tarde.");
        });
    }

    // Función para cerrar modales
    function cerrarModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Hacer global la función cerrarModal
    window.cerrarModal = cerrarModal;
});