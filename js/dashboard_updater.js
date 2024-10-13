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
          window.enrollmentHandler.showEnrollmentModal(courseId);
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
              display: none !important;
              position: fixed !important;
              z-index: 1001 !important;
              top: 0 !important;
              left: 0 !important;
              width: 100% !important;
              height: 100vh !important;
              overflow: auto !important;
              background-color: rgba(0,0,0,0.4) !important;
          }
          .modal {
              display: none !important;
              position: fixed !important;
              z-index: 1000 !important;
              top: 0 !important;
              left: 0 !important;
              width: 100% !important;
              height: 100vh !important;
              overflow: auto !important;
              background-color: rgba(0,0,0,0.4) !important;
          }
          .modal-content {
              background-color: #fefefe !important;
              padding: 20px !important;
              border: 1px solid #888 !important;
              width: 80% !important;
              max-width: 600px !important;
              border-radius: 5px !important;
              margin: 20px auto !important;
          }
          .close {
              color: #aaa !important;
              float: right !important;
              font-size: 28px !important;
              font-weight: bold !important;
              cursor: pointer !important;
          }
          .close:hover,
          .close:focus {
              color: black !important;
              text-decoration: none !important;
              cursor: pointer !important;
          }
          .image-preview {
              display: none !important;
              margin-top: 10px !important;
          }
          .image-preview img {
              max-width: 100% !important;
              max-height: 200px !important;
              border-radius: 5px !important;
          }
          .file-upload {
              position: relative !important;
              overflow: hidden !important;
              margin: 10px 0 !important;
          }
          .file-upload input[type=file] {
              position: absolute !important;
              top: 0 !important;
              right: 0 !important;
              min-width: 100% !important;
              min-height: 100% !important;
              font-size: 100px !important;
              text-align: right !important;
              filter: alpha(opacity=0) !important;
              opacity: 0 !important;
          }
          .file-upload-label {
              display: block !important;
              padding: 10px !important;
              background-color: #f1f1f1 !important;
              border: 1px solid #ddd !important;
              border-radius: 5px !important;
              cursor: pointer !important;
          }
          .file-upload-label:hover {
              background-color: #ddd !important;
          }
          .file-info {
              font-size: 0.8em !important;
              color: #666 !important;
          }
          .message {
              display: none !important;
              position: fixed !important;
              z-index: 1000 !important;
              left: 0 !important;
              bottom: 0 !important;
              width: 100% !important;
              padding: 10px !important;
              background-color: #4CAF50 !important;
              color: white !important;
              text-align: center !important;
              border-radius: 5px !important;
          }
          .message.error {
              background-color: #f44336 !important;
          }
          .message.success {
              background-color: #4CAF50 !important;
          }
      `;
    document.head.appendChild(style);
  });
