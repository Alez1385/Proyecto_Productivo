  // Wrap everything in an IIFE to avoid polluting the global scope
  (function () {
    const DEBUG = false;
    const log = (...args) => DEBUG && console.log(...args);
    const logError = (...args) => console.error(...args);

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

    const fetchWithTimeout = (url, options = {}, timeout = 5000) => {
        return Promise.race([
            fetch(url, options),
            new Promise((_, reject) =>
                setTimeout(() => reject(new Error('Request timed out')), timeout)
            )
        ]);
    };

    // Main functions
    const addStyles = () => {
      if (document.getElementById("enrollmentStyles")) return;

      const styles = `
             /* Base styles */


/* Loading spinner */
#loading-spinner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

#loading-spinner::after {
    content: '';
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Message styles */
.mensaje {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 12px 20px;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 9999;
    text-align: center;
    max-width: 80%;
    font-size: 14px;
    transition: opacity 0.3s ease-in-out;
}

.error-mensaje {
    background-color: #ff4d4d;
    color: white;
}

.success-mensaje {
    background-color: #4CAF50;
    color: white;
}

/* Modal styles */
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
    background-color: #ffffff;
    margin: auto;
    padding: 25px;
    border: none;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.close {
    color: #999;
    float: right;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover,
.close:focus {
    color: #333;
    text-decoration: none;
}

/* Button styles */
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
    border-radius: 4px;
    transition: background-color 0.3s, transform 0.1s;
    font-size: 14px;
    font-weight: 500;
}

.btn:hover {
    background-color: #45a049;
}

.btn:active {
    background-color: #3e8e41;
    transform: translateY(1px);
}

.btn-primary {
    background-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #545b62;
}

/* Form styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
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
        logError("Error fetching user data:", error);
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
        logError("Error in quick enrollment:", error);
        showEnrollmentForm(courseId, {});
      }
    };

    const sendEnrollment = async (courseId, userData) => {
      showLoading();
      const formData = new FormData();
      formData.append("curso_id", courseId);
      formData.append("nombre", userData.nombre);
      formData.append("email", userData.email);
      formData.append("telefono", userData.telefono);
  
      try {
          const response = await fetchWithTimeout("scripts/preinscribir.php", {
              method: "POST",
              body: formData,
          });
  
          if (!response.ok) {
              const errorText = await response.text(); // Obtiene la respuesta como texto
              throw new Error(`Server error: ${response.status}. ${errorText}`);
          }
  
          const textResponse = await response.text(); // Obtiene la respuesta como texto
          let data;
  
          try {
              data = JSON.parse(textResponse); // Intenta analizarla
          } catch (err) {
              // Muestra el error de parseo
              logError("Error parsing JSON:", err);
              throw new Error("Error parsing JSON: " + textResponse);
          }
  
          log("Server response:", data);
  
          if (data.error) {
              showError(data.error);
          } else if (data.success) {
              showSuccess(data.success);
              if (data.newUser) {
                  showNewUserInfo(data.newUser);
              }
          } else {
              throw new Error("Unexpected server response");
          }
      } catch (err) {
          logError("Error:", err);
          showError(err.message); // Muestra el mensaje de error específico
      } finally {
          hideLoading();
      }
  };
  

  const showNewUserInfo = (newUser) => {
      const modalHTML = `
        <div class="modal" id="newUserModal">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">New Account Created</h5>
              <span class="close" onclick="enrollmentApp.closeModal('newUserModal')">&times;</span>
            </div>
            <div class="modal-body">
              <p>A new account has been created for you:</p>
              <p>Email: ${newUser.email}</p>
              <p>Temporary password: ${newUser.tempPassword}</p>
              <p>Please change your password after logging in.</p>
            </div>
          </div>
        </div>
      `;

      document.body.insertAdjacentHTML("beforeend", modalHTML);
      const modal = document.getElementById("newUserModal");
      modal.style.display = "block";
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
            window.location.href = `/inscripcion/inscripcion_completa.php?curso_id=${encodeURIComponent(
              courseId
            )}`;
          }
        } else if (data.status === "no_logueado") {
          window.location.href = `/login/login.php?redirect=/inscripcion/inscripcion_completa.php?curso_id=${encodeURIComponent(
            courseId
          )}`;
        } else {
          throw new Error("Respuesta inesperada del servidor");
        }
      } catch (error) {
        logError("Error:", error);
        showError("Hubo un problema al procesar tu solicitud: " + error.message);
      } finally {
        hideLoading();
      }
    };

    // Initialize
    const init = () => {
      if (!window.fetch || !window.FormData) {
          console.error("Este navegador no soporta características requeridas. Por favor, actualiza tu navegador.");
          return;
      }

      addStyles();

      const enrollmentBtns = document.querySelectorAll(
          ".inscribirse-btn, .inscripcion-completa-btn"
      );

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

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}

window.enrollmentApp = {
    closeModal: closeModal,
};
})();