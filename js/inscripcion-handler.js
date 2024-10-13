//inscripcion-handler.js
// Immediately Invoked Function Expression (IIFE) to avoid polluting the global scope
(function() {
  const DEBUG = false;
  const log = (...args) => DEBUG && console.log(...args);
  const logError = (...args) => console.error(...args);

  // Utility functions
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
          /* ... (styles from both files combined) ... */
      `;

      const styleElement = document.createElement("style");
      styleElement.id = "enrollmentStyles";
      styleElement.textContent = styles;
      document.head.appendChild(styleElement);
  };


  const getUserData = async () => {
      log("Fetching user data");
      try {
          const response = await fetch("/scripts/obtener_datos_usuario.php", {
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
      document.getElementById("preinscripcionBtn").addEventListener("click", () => {
          closeModal("inscripcionModal");
          quickEnrollment(courseId);
      });


      document.getElementById("inscripcionCompletaBtn").addEventListener("click", () => {
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
          const response = await fetchWithTimeout("/scripts/preinscribir.php", {
              method: "POST",
              body: formData,
          });

          if (!response.ok) {
              const errorText = await response.text();
              throw new Error(`Server error: ${response.status}. ${errorText}`);
          }

          const textResponse = await response.text();
          let data;

          try {
              data = JSON.parse(textResponse);
          } catch (err) {
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
          showError(err.message);
      } finally {
          hideLoading();
      }
  };

  const showNewUserInfo = (newUser) => {
      const modalHTML = `
          <div class="modal" id="newUserModal">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Nueva Cuenta Creada</h5>
                      <span class="close" onclick="enrollmentApp.closeModal('newUserModal')">&times;</span>
                  </div>
                  <div class="modal-body">
                      <p>Se ha creado una nueva cuenta para usted:</p>
                      <p>Email: ${newUser.email}</p>
                      <p>Contraseña temporal: ${newUser.tempPassword}</p>
                      <p>Por favor, cambie su contraseña después de iniciar sesión.</p>
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
                              <input type="text" id="nombre" name="nombre" value="${userData.nombre || ""}" required>
                          </div>
                          <div class="form-group">
                              <label for="email">Correo electrónico</label>
                              <input type="email" id="email" name="email" value="${userData.email || ""}" required>
                          </div>
                          <div class="form-group">
                              <label for="telefono">Teléfono</label>
                              <input type="tel" id="telefono" name="telefono" value="${userData.telefono || ""}" required>
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
      document.getElementById("preinscripcionForm").addEventListener("submit", async (e) => {
          e.preventDefault();
          const formData = new FormData(e.target);

          try {
              const response = await fetch("/scripts/preinscribir.php", {
                  method: "POST",
                  body: formData,
              });

              if (!response.ok) throw new Error("Network response was not ok");

              const data = await response.text();
              showSuccess(data);
              closeModal("preinscripcionModal");
          } catch (error) {
              logError("Error:", error);
              showError("Error al intentar preinscribirte. Por favor, intenta nuevamente más tarde.");
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
          const response = await fetch("/scripts/verificar_usuario.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
                  "X-Requested-With": "XMLHttpRequest",
              },
              body: `curso_id=${encodeURIComponent(courseId)}`,
              credentials: "same-origin",
          });

          if (!response.ok)
              throw new Error("Error en la respuesta del servidor: " + response.status);

          const data = await response.json();

          if (data.error) throw new Error(data.error);

          if (data.status === "logueado") {
              if (data.ya_inscrito) {
                  showError("Ya estás inscrito en este curso.");
              } else {
                  window.location.href = `/inscripcion/inscripcion_completa.php?curso_id=${encodeURIComponent(courseId)}`;
              }
          } else if (data.status === "no_logueado") {
              window.location.href = `/login/login.php?redirect=/inscripcion/inscripcion_completa.php?curso_id=${encodeURIComponent(courseId)}`;
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

      const enrollmentBtns = document.querySelectorAll(".inscribirse-btn, .inscripcion-completa-btn");

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

  // Run initialization
  if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", init);
  } else {
      init();
  }

  // Expose necessary functions to global scope
  // dashboard-updater.js

const dashboardUpdater = {
    updateInterval: 30000, // Update every 30 seconds
    cursos: [], // Store the courses data
  
    init: function () {
      this.updateDashboard();
      setInterval(() => this.updateDashboard(), this.updateInterval);
      this.initModal();
    },
  
    escapeHtml: function (unsafe) {
      if (typeof unsafe !== "string") {
        return unsafe;
      }
      return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    },
  
    updateDashboard: function () {
      fetch("../dashboard/dashboard_data.php")
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            console.error("Error:", data.message);
            console.error("Details:", data.details);
            console.error("File:", data.file);
            console.error("Line:", data.line);
            this.displayErrorToUser(data.message);
            return;
          }
          this.updateInscripciones(data.inscripciones);
          this.updatePreinscripciones(data.preinscripciones);
          this.updateCursos(data.cursos);
          this.cursos = data.cursos; // Store the courses data
        })
        .catch((error) => {
          console.error("Error:", error);
          this.displayErrorToUser(
            "Error de conexión. Por favor, intenta de nuevo más tarde."
          );
        });
    },
  
    displayErrorToUser: function (message) {
      const errorDiv = document.getElementById("error-message");
      if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = "block";
      } else {
        alert(message);
      }
    },
  
    updateInscripciones: function (inscripciones) {
      const container = document.getElementById("inscripciones-list");
      if (!container) return;
  
      if (inscripciones.length === 0) {
        container.innerHTML = "<p>No tienes inscripciones activas.</p>";
        return;
      }
  
      let html =
        "<table><thead><tr><th>Curso</th><th>Fecha de Inscripción</th><th>Estado</th><th>Última Actualización</th><th>Acción</th></tr></thead><tbody>";
      inscripciones.forEach((inscripcion) => {
        html += `
                  <tr class="${
                    ["cancelado", "rechazado"].includes(inscripcion.estado)
                      ? "inscripcion-inactiva"
                      : ""
                  }">
                      <td>${this.escapeHtml(inscripcion.nombre_curso)}</td>
                      <td>${this.escapeHtml(inscripcion.fecha_inscripcion)}</td>
                      <td>${this.escapeHtml(inscripcion.estado)}</td>
                      <td>${this.escapeHtml(inscripcion.fecha_actualizacion)}</td>
                      <td>
                          ${
                            inscripcion.estado !== "cancelado" &&
                            inscripcion.estado !== "rechazado"
                              ? `<button onclick="dashboardUpdater.cancelInscripcion(${inscripcion.id_inscripcion})">Cancelar</button>`
                              : ""
                          }
                      </td>
                  </tr>
              `;
      });
      html += "</tbody></table>";
      container.innerHTML = html;
    },
  
    updatePreinscripciones: function (preinscripciones) {
      const container = document.getElementById("preinscripciones-list");
      if (!container) return;
  
      if (preinscripciones.length === 0) {
        container.innerHTML = "<p>No tienes preinscripciones pendientes.</p>";
        return;
      }
  
      let html =
        "<table><thead><tr><th>Curso</th><th>Fecha de Preinscripción</th><th>Estado</th><th>Acción</th></tr></thead><tbody>";
      preinscripciones.forEach((preinscripcion) => {
        html += `
                  <tr class="${
                    ["cancelado", "rechazado"].includes(preinscripcion.estado)
                      ? "preinscripcion-inactiva"
                      : ""
                  }">
                      <td>${this.escapeHtml(preinscripcion.nombre_curso)}</td>
                      <td>${this.escapeHtml(
                        preinscripcion.fecha_preinscripcion
                      )}</td>
                      <td>${this.escapeHtml(preinscripcion.estado)}</td>
                      <td>
                          ${
                            preinscripcion.estado === "pendiente"
                              ? `<button onclick="dashboardUpdater.showEnrollmentModal2(${preinscripcion.id_curso})">Inscribirse</button>`
                              : ""
                          }
                          ${
                            preinscripcion.estado !== "cancelado" &&
                            preinscripcion.estado !== "rechazado"
                              ? `<button onclick="dashboardUpdater.cancelPreinscripcion(${preinscripcion.id_preinscripcion})">Cancelar</button>`
                              : ""
                          }
                      </td>
                  </tr>
              `;
      });
      html += "</tbody></table>";
      container.innerHTML = html;
    },
  
    cancelInscripcion: function (inscripcionId) {
      if (confirm("¿Estás seguro de que deseas cancelar esta inscripción?")) {
        fetch("../inscripcion/scripts/cancelar_inscripcion.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `id_inscripcion=${inscripcionId}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              this.showMessage("success", data.message);
              this.updateDashboard();
            } else {
              this.showMessage(
                "error",
                data.message || "Error al cancelar la inscripción."
              );
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            this.showMessage(
              "error",
              "Error de conexión. Por favor, intenta de nuevo más tarde."
            );
          });
      }
    },
  
    cancelPreinscripcion: function (preinscripcionId) {
      if (confirm("¿Estás seguro de que deseas cancelar esta preinscripción?")) {
        fetch("../inscripcion/scripts/cancelar_preinscripcion.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `id_preinscripcion=${preinscripcionId}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              this.showMessage("success", data.message);
              this.updateDashboard();
            } else {
              this.showMessage(
                "error",
                data.message || "Error al cancelar la preinscripción."
              );
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            this.showMessage(
              "error",
              "Error de conexión. Por favor, intenta de nuevo más tarde."
            );
          });
      }
    },
  
    updateCursos: function (cursos) {
      this.cursos = cursos; // Update the stored courses data
      const container = document.getElementById("course-list");
      if (!container) return;
  
      if (cursos.length === 0) {
        container.innerHTML = "<p>No hay cursos disponibles en este momento.</p>";
        return;
      }
  
      let html = "";
      cursos.forEach((curso) => {
        html += `
                  <div class="course-item">
                      <img src="../../uploads/icons/${this.escapeHtml(
                        curso.icono
                      )}" alt="${this.escapeHtml(curso.nombre_curso)}">
                      <div class="course-content">
                          <div class="course-details">
                              <h3>${this.escapeHtml(curso.nombre_curso)}</h3>
                              <p>${this.escapeHtml(curso.descripcion)}</p>
                              <p><strong>Categoría:</strong> ${this.escapeHtml(
                                curso.nombre_categoria
                              )}</p>
                              <p><strong>Nivel:</strong> ${this.escapeHtml(
                                curso.nivel_educativo
                              )}</p>
                              <p><strong>Duración:</strong> ${this.escapeHtml(
                                curso.duracion
                              )} semanas</p>
                              <p><strong>Horarios:</strong> ${this.escapeHtml(
                                curso.horarios
                              )}</p>
                              ${
                                curso.estado_inscripcion === "rechazado" ||
                                curso.estado_inscripcion === "cancelado"
                                  ? `<p class="inscripcion-rechazada">Tu inscripción anterior fue ${this.escapeHtml(
                                      curso.estado_inscripcion
                                    )}</p>`
                                  : ""
                              }
                              ${
                                curso.estado_preinscripcion === "rechazado" ||
                                curso.estado_preinscripcion === "cancelado"
                                  ? `<p class="preinscripcion-rechazada">Tu preinscripción anterior fue ${this.escapeHtml(
                                      curso.estado_preinscripcion
                                    )}</p>`
                                  : ""
                              }
                          </div>
                          <div class="course-actions">
                              ${
                                curso.estado_inscripcion === null &&
                                curso.estado_preinscripcion === null
                                  ? `<button id="inscripcion-modal " class="inscripcion-open-btn inscribirse-btn" data-curso-id="${curso.id_curso}">Inscribirse</button>`
                                  : ""
                              }
                          </div>
                      </div>
                  </div>
              `;
      });
      container.innerHTML = html;
  
      // Reattach event listeners for enrollment buttons
      this.attachEnrollmentListeners();
    },
  
    attachEnrollmentListeners: function () {
      const inscripcionBtns = document.querySelectorAll(
        ".inscribirse-btn, .terminar-preinscripcion-btn"
      );
      inscripcionBtns.forEach((btn) => {
        btn.addEventListener("click", (e) => {
          e.preventDefault();
          const courseId = btn.getAttribute("data-curso-id");
          this.showEnrollmentModal(courseId);
        });
      });
    },
  
    initModal: function () {
      // Crear HTML del modal
      const modalHtml = `
            <div id="enrollmentModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="closeEnrollmentModal">&times;</span>
                    <h2>Inscripción al Curso</h2>
                    <div id="modalCourseDetails"></div>
                    <form id="enrollmentForm" enctype="multipart/form-data">
                        <input type="hidden" id="curso_id" name="curso_id">
                        <div class="form-group">
                            <label for="comprobante">Comprobante de pago:</label>
                            <div class="file-upload">
                                <input type="file" id="comprobante" name="comprobante" accept="image/*" required>
                                <span class="file-upload-label">Seleccionar archivo</span>
                            </div>
                            <p class="file-info">Formatos aceptados: JPG, JPEG, PNG, GIF. Tamaño máximo: 500KB.</p>
                            <div id="imagePreview" class="image-preview">
                                <img id="previewImage" src="#" alt="Vista previa del comprobante">
                            </div>
                        </div>
                        <button type="submit">Completar Inscripción</button>
                    </form>
                </div>
            </div>
        `;
  
      // Añadir el modal al body
      document.body.insertAdjacentHTML("beforeend", modalHtml);
  
      // Obtener elementos del modal
      const modal = document.getElementById("enrollmentModal");
      const closeBtn = document.getElementById("closeEnrollmentModal");
  
      // Cerrar el modal al hacer clic en el botón de cerrar o fuera del modal
      closeBtn.onclick = () => (modal.style.display = "none");
      window.onclick = (event) => {
        if (event.target === modal) {
          modal.style.display = "none";
        }
      };
  
      // Manejar el cambio en el input de archivo
      const fileInput = document.getElementById("comprobante");
      const imagePreview = document.getElementById("imagePreview");
      const previewImage = document.getElementById("previewImage");
  
      fileInput.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            previewImage.src = e.target.result;
            imagePreview.style.display = "block";
          };
          reader.readAsDataURL(file);
        }
      });
  
      // Manejar el envío del formulario
      const form = document.getElementById("enrollmentForm");
      form.addEventListener("submit", this.handleEnrollmentSubmit.bind(this));
    },
  
    showEnrollmentModal2: function (courseId) {
      const modal = document.getElementById("enrollmentModal");
      const courseDetails = document.getElementById("modalCourseDetails");
      const curso = this.getCursoById(courseId);
  
      if (curso) {
        courseDetails.innerHTML = `
                  <div class="course-details">
                      <div class="course-image">
                          <img src="../../uploads/icons/${this.escapeHtml(
                            curso.icono
                          )}" alt="${this.escapeHtml(curso.nombre_curso)}">
                      </div>
                      <div class="course-info">
                          <h3>${this.escapeHtml(curso.nombre_curso)}</h3>
                          <p class="description">${this.escapeHtml(
                            curso.descripcion
                          )}</p>
                          <div class="course-meta">
                              <span class="duration"><i  class="icon-clock"></i> ${this.escapeHtml(
                                curso.duracion
                              )} semanas</span>
                              <span class="level"><i class="icon-graduation-cap"></i> ${this.escapeHtml(
                                curso.nivel_educativo
                              )}</span>
                          </div>
                      </div>
                  </div>
              `;
      }
  
      document.getElementById("curso_id").value = courseId;
      modal.style.display = "block";
    },
  
    getCursoById: function (courseId) {
      return this.cursos.find((curso) => curso.id_curso === courseId);
    },
  
    handleEnrollmentSubmit: function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
  
      fetch("../inscripcion/scripts/inscripcion_completa_modal.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            this.showMessage("success", data.message);
            document.getElementById("enrollmentModal").style.display = "none";
            this.updateDashboard();
          } else {
            this.showMessage(
              "error",
              data.message || "Error al procesar la inscripción."
            );
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          this.showMessage(
            "error",
            "Error de conexión. Por favor, intenta de nuevo más tarde."
          );
        });
    },
  
    showMessage: function (type, message) {
      const messageDiv = document.createElement("div");
      messageDiv.className = `message ${type}`;
      messageDiv.textContent = message;
      document.body.appendChild(messageDiv);
      setTimeout(() => {
        messageDiv.remove();
      }, 5000);
    },
  };
  
  document.addEventListener("DOMContentLoaded", function () {
    dashboardUpdater.init();
    const dashboardModal = document.getElementById("dashboard-modal");
  
    // Mostrar modal del dashboard
    function showDashboardModal() {
      dashboardModal.style.display = "block";
    }
  
    // Cerrar modal del dashboard
    function closeDashboardModal() {
      dashboardModal.style.display = "none";
    }
  
    // Añadimos eventos específicos al modal del dashboard
    document
      .querySelector(".dashboard-open-btn")
      .addEventListener("click", showDashboardModal);
    document
      .querySelector(".dashboard-close-btn")
      .addEventListener("click", closeDashboardModal);
  
    // Add CSS styles for the modal and messages
    const style = document.createElement("style");
    style.textContent = `
      #dashboard-modal {
              display: none;
              position: fixed;
              z-index: 1001;
          }
          .modal {
              display: none;
              position: fixed;
              z-index: 1000;
              left: 0;
              top: 0;
              width: 100%;
              height: 100%;
              overflow: auto;
              background-color: rgba(0,0,0,0.4);
          }
          .modal-content {
              background-color: #fefefe;
              padding: 20px;
              border: 1px solid #888;
              width: 80%;
              max-width: 600px;
              border-radius: 5px;
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
          .image-preview {
              display: none;
              margin-top: 10px;
          }
          .image-preview img {
              max-width: 100%;
              max-height: 200px;
          }
          .file-upload {
              position: relative;
              overflow: hidden;
              margin: 10px 0;
          }
          .file-upload input[type=file] {
              position: absolute;
              top: 0;
              right: 0;
              min-width: 100%;
              min-height: 100%;
              font-size: 100px;
              text-align: right;
              filter: alpha(opacity=0);
              opacity: 0;
          }
          .file-upload-label {
              display: block;
              padding: 10px;
              background-color: #f1f1f1;
              border: 1px solid #ddd;
              border-radius: 5px;
              cursor: pointer;
          }
          .file-upload-label:hover {
              background-color: #ddd;
          }
          .file-info {
              font-size: 0.8em;
              color: #666;
          }
          .message {
              display: none;
              position: fixed;
              z-index: 1000;
              left: 0;
              bottom: 0;
              width: 100%;
              padding: 10px;
              background-color: #4CAF50;
              color: white;
              text-align: center;
          }
          .message.error {
              background-color: #f44336;
          }
          .message.success {
              background-color: #4CAF50;
          }
      `;
    document.head.appendChild(style);
  });

  // Immediately Invoked Function Expression (IIFE) to avoid polluting the global scope
(function() {
  const DEBUG = false;
  const log = (...args) => DEBUG && console.log(...args);
  const logError = (...args) => console.error(...args);

  // Utility functions
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
          /* ... (styles from both files combined) ... */
      `;

      const styleElement = document.createElement("style");
      styleElement.id = "enrollmentStyles";
      styleElement.textContent = styles;
      document.head.appendChild(styleElement);
  };


  const getUserData = async () => {
      log("Fetching user data");
      try {
          const response = await fetch("/scripts/obtener_datos_usuario.php", {
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
      document.getElementById("preinscripcionBtn").addEventListener("click", () => {
          closeModal("inscripcionModal");
          quickEnrollment(courseId);
      });


      document.getElementById("inscripcionCompletaBtn").addEventListener("click", () => {
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
          const response = await fetchWithTimeout("/scripts/preinscribir.php", {
              method: "POST",
              body: formData,
          });

          if (!response.ok) {
              const errorText = await response.text();
              throw new Error(`Server error: ${response.status}. ${errorText}`);
          }

          const textResponse = await response.text();
          let data;

          try {
              data = JSON.parse(textResponse);
          } catch (err) {
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
          showError(err.message);
      } finally {
          hideLoading();
      }
  };

  const showNewUserInfo = (newUser) => {
      const modalHTML = `
          <div class="modal" id="newUserModal">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Nueva Cuenta Creada</h5>
                      <span class="close" onclick="enrollmentApp.closeModal('newUserModal')">&times;</span>
                  </div>
                  <div class="modal-body">
                      <p>Se ha creado una nueva cuenta para usted:</p>
                      <p>Email: ${newUser.email}</p>
                      <p>Contraseña temporal: ${newUser.tempPassword}</p>
                      <p>Por favor, cambie su contraseña después de iniciar sesión.</p>
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
                              <input type="text" id="nombre" name="nombre" value="${userData.nombre || ""}" required>
                          </div>
                          <div class="form-group">
                              <label for="email">Correo electrónico</label>
                              <input type="email" id="email" name="email" value="${userData.email || ""}" required>
                          </div>
                          <div class="form-group">
                              <label for="telefono">Teléfono</label>
                              <input type="tel" id="telefono" name="telefono" value="${userData.telefono || ""}" required>
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
      document.getElementById("preinscripcionForm").addEventListener("submit", async (e) => {
          e.preventDefault();
          const formData = new FormData(e.target);

          try {
              const response = await fetch("/scripts/preinscribir.php", {
                  method: "POST",
                  body: formData,
              });

              if (!response.ok) throw new Error("Network response was not ok");

              const data = await response.text();
              showSuccess(data);
              closeModal("preinscripcionModal");
          } catch (error) {
              logError("Error:", error);
              showError("Error al intentar preinscribirte. Por favor, intenta nuevamente más tarde.");
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
          const response = await fetch("/scripts/verificar_usuario.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
                  "X-Requested-With": "XMLHttpRequest",
              },
              body: `curso_id=${encodeURIComponent(courseId)}`,
              credentials: "same-origin",
          });

          if (!response.ok)
              throw new Error("Error en la respuesta del servidor: " + response.status);

          const data = await response.json();

          if (data.error) throw new Error(data.error);

          if (data.status === "logueado") {
              if (data.ya_inscrito) {
                  showError("Ya estás inscrito en este curso.");
              } else {
                  window.location.href = `/inscripcion/inscripcion_completa.php?curso_id=${encodeURIComponent(courseId)}`;
              }
          } else if (data.status === "no_logueado") {
              window.location.href = `/login/login.php?redirect=/inscripcion/inscripcion_completa.php?curso_id=${encodeURIComponent(courseId)}`;
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

      const enrollmentBtns = document.querySelectorAll(".inscribirse-btn, .inscripcion-completa-btn");

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
  

  // Run initialization
  if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", init);
  } else {
      init();
  }

  // Expose necessary functions to global scope
  window.enrollmentHandler = {
    showEnrollmentModal: showEnrollmentModal,
    quickEnrollment: quickEnrollment,
    fullEnrollment: fullEnrollment,
    showError: showError,
    showLoading: showLoading,
    hideLoading: hideLoading,
    
    // Add any other functions you want to expose
};

})();
})();
