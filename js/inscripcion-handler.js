// Wrap everything in an IIFE to avoid polluting the global scope
(function () {
  // Check for required browser features
  if (!window.fetch || !window.FormData) {
    console.error(
      "This browser does not support required features. Please upgrade your browser."
    );
    return;
  }

  // Debug flag
  const DEBUG = false;

  // Helper functions
  const log = (...args) => DEBUG && console.log(...args);
  const error = console.error;

  const showLoading = () => {
    if (!document.getElementById("loading-spinner")) {
      const spinner = document.createElement("div");
      spinner.id = "loading-spinner";
      document.body.appendChild(spinner);
    }
  };

  const hideLoading = () => {
    const spinner = document.getElementById("loading-spinner");
    if (spinner) spinner.remove();
  };

  const showMessage = (message, type) => {
    const messageDiv = document.createElement("div");
    messageDiv.className = `mensaje ${type}-mensaje`;
    messageDiv.textContent = message;
    document.body.appendChild(messageDiv);
    setTimeout(() => messageDiv.remove(), 5000);
  };

  const showError = (message) => showMessage(message, "error");
  const showSuccess = (message) => showMessage(message, "success");

  const closeModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "none";
  };

  // Main functions
  const addStyles = () => {
    if (document.getElementById("enrollmentStyles")) return;

    const styles = `
            #loading-spinner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

#loading-spinner::after {
    content: '';
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.mensaje {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 9999;
    text-align: center;
    max-width: 80%;
}

.error-mensaje {
    background-color: #f44336;
    color: white;
}

.success-mensaje {
    background-color: #4CAF50;
    color: white;
}

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

.btn:hover {
    background-color: #45a049;
}

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

    const styleElement = document.createElement("style");
    styleElement.id = "enrollmentStyles";
    styleElement.textContent = styles;
    document.head.appendChild(styleElement);
  };

  const getUserData = async () => {
    log("Fetching user data");
    try {
      const response = await fetch("scripts/obtener_datos_usuario.php", {
        method: "GET",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
      });

      if (!response.ok) {
        if (response.status === 401) {
          log("User not authenticated");
          return null;
        }
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      log("User data:", data);
      return data;
    } catch (error) {
      error("Error fetching user data:", error);
      throw error;
    }
  };

  const showEnrollmentModal = (courseId) => {
    const modalHTML = `
            <div class="modal" id="inscripcionModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Opciones de Inscripción</h5>
                        <span class="close" onclick="enrollmentApp.closeModal('inscripcionModal')">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>Elija una opción para inscribirse al curso:</p>
                        <button class="btn btn-primary" id="preinscripcionBtn">Preinscripción Rápida</button>
                        <button class="btn btn-secondary" id="inscripcionCompletaBtn">Inscripción Completa</button>
                    </div>
                </div>
            </div>
        `;

    // Remove existing modal if present
    const existingModal = document.getElementById("inscripcionModal");
    if (existingModal) existingModal.remove();

    // Add new modal to body
    document.body.insertAdjacentHTML("beforeend", modalHTML);
    const modal = document.getElementById("inscripcionModal");

    // Show modal
    modal.style.display = "block";

    // Add event listeners
    document
      .getElementById("preinscripcionBtn")
      .addEventListener("click", () => {
        closeModal("inscripcionModal");
        quickEnrollment(courseId);
      });

    document
      .getElementById("inscripcionCompletaBtn")
      .addEventListener("click", () => {
        closeModal("inscripcionModal");
        fullEnrollment(courseId);
      });
  };

  const quickEnrollment = async (courseId) => {
    log("Starting quick enrollment for course:", courseId);
    try {
      const userData = await getUserData();
      if (userData && userData.nombre && userData.email && userData.telefono) {
        sendEnrollment(courseId, userData);
      } else {
        showEnrollmentForm(courseId, userData || {});
      }
    } catch (error) {
      error("Error in quick enrollment:", error);
      showEnrollmentForm(courseId, {});
    }
  };

  const sendEnrollment = async (courseId, userData) => {
    const formData = new FormData();
    formData.append("curso_id", courseId);
    formData.append("nombre", userData.nombre);
    formData.append("email", userData.email);
    formData.append("telefono", userData.telefono);

    try {
      const response = await fetch("scripts/preinscribir.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) throw new Error("Network response was not ok");

      const data = await response.text();
      showSuccess(data);
    } catch (error) {
      error("Error:", error);
      showError(
        "Error al intentar preinscribirte. Por favor, intenta nuevamente más tarde."
      );
    }
  };

  const showEnrollmentForm = (courseId, userData = {}) => {
    const formHTML = `
            <div class="modal" id="preinscripcionModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Preinscripción Rápida</h5>
                        <span class="close" onclick="enrollmentApp.closeModal('preinscripcionModal')">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="preinscripcionForm">
                            <input type="hidden" name="curso_id" value="${courseId}">
                            <div class="form-group">
                                <label for="nombre">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre" value="${
                                  userData.nombre || ""
                                }" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Correo electrónico</label>
                                <input type="email" id="email" name="email" value="${
                                  userData.email || ""
                                }" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" value="${
                                  userData.telefono || ""
                                }" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar Preinscripción</button>
                        </form>
                    </div>
                </div>
            </div>
        `;

    // Remove existing modal if present
    const existingModal = document.getElementById("preinscripcionModal");
    if (existingModal) existingModal.remove();

    // Add new modal to body
    document.body.insertAdjacentHTML("beforeend", formHTML);
    const modal = document.getElementById("preinscripcionModal");

    // Show modal
    modal.style.display = "block";

    // Handle form submission
    document
      .getElementById("preinscripcionForm")
      .addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
          const response = await fetch("scripts/preinscribir.php", {
            method: "POST",
            body: formData,
          });

          if (!response.ok) throw new Error("Network response was not ok");

          const data = await response.text();
          showSuccess(data);
          closeModal("preinscripcionModal");
        } catch (error) {
          error("Error:", error);
          showError(
            "Error al intentar preinscribirte. Por favor, intenta nuevamente más tarde."
          );
        }
      });
  };

  const fullEnrollment = async (courseId) => {
    if (!courseId || isNaN(courseId)) {
      showError("ID de curso inválido.");
      return;
    }

    showLoading();

    try {
      const response = await fetch("scripts/verificar_usuario.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: `curso_id=${encodeURIComponent(courseId)}`,
        credentials: "same-origin",
      });

      if (!response.ok)
        throw new Error(
          "Error en la respuesta del servidor: " + response.status
        );

      const data = await response.json();

      if (data.error) throw new Error(data.error);

      if (data.status === "logueado") {
        if (data.ya_inscrito) {
          showError("Ya estás inscrito en este curso.");
        } else {
          window.location.href = `inscripcion/inscripcion_completa.php?curso_id=${encodeURIComponent(
            courseId
          )}`;
        }
      } else if (data.status === "no_logueado") {
        window.location.href = `login/login.php?redirect=inscripcion_completa.php&curso_id=${encodeURIComponent(
          courseId
        )}`;
      } else {
        throw new Error("Respuesta inesperada del servidor");
      }
    } catch (error) {
      error("Error:", error);
      showError("Hubo un problema al procesar tu solicitud: " + error.message);
    } finally {
      hideLoading();
    }
  };

  // Initialize
  const init = () => {
    addStyles();

    // Select all enrollment buttons
    const enrollmentBtns = document.querySelectorAll(
      ".inscribirse-btn, .inscripcion-completa-btn"
    );

    // Add event listener to each button
    enrollmentBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const courseId = btn.getAttribute("data-curso-id");
        if (courseId) {
          if (btn.classList.contains("inscribirse-btn")) {
            showEnrollmentModal(courseId);
          } else if (btn.classList.contains("inscripcion-completa-btn")) {
            fullEnrollment(courseId);
          }
        } else {
          showError("No se pudo obtener el ID del curso.");
        }
      });
    });
  };

  // Run initialization when DOM is loaded
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Expose necessary functions to global scope
  window.enrollmentApp = {
    closeModal: closeModal,
  };
})();
