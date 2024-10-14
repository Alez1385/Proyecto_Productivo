// dashboard-updater.js

const dashboardUpdater = {
    updateInterval: 30000, // Update every 30 seconds
    cursos: [], // Store the courses data
  
    init: function () {
      this.updateDashboard();
      setInterval(() => this.updateDashboard(), this.updateInterval);
      this.initModal();
      this.addStylesDashboard();
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
          window.enrollmentApp.showEnrollmentModal(courseId);
        });
      });
    },
  
    initModal: function () {
      // Crear HTML del modal
      const modalHtml = `
            <div id="enrollmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Inscripción al Curso</h2>
            <span id="closeEnrollmentModal" class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div id="modalCourseDetails"></div>
            <form id="enrollmentForm" enctype="multipart/form-data">
                <input type="hidden" id="curso_id" name="curso_id">
                <div class="form-group">
                    <label for="comprobante">Comprobante de pago:</label>
                    <div class="file-upload">
                        <input type="file" id="comprobante" name="comprobante" accept="image/*" required>
                        <label for="comprobante" class="file-upload-label">Seleccionar archivo</label>
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
</div>

        `;
  
      // Añadir el modal al body
      document.body.insertAdjacentHTML("beforeend", modalHtml);
  
      // Obtener elementos del modal
      const modal = document.getElementById("enrollmentModal");
      const closeBtn = document.getElementById("closeEnrollmentModal");
  
      // Cerrar el modal al hacer clic en el botón de cerrar o fuera del modal
      closeBtn.onclick = () => {
        modal.style.display = "none";
        this.removeStyles(); // Remove styles when closing the modal
    };
      // Modify the window click event
    window.onclick = (event) => {
      if (event.target === modal) {
          modal.style.display = "none";
          this.removeStyles(); // Remove styles when closing the modal
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
      this.addStylesDashboard();
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
                          <h2>${this.escapeHtml(curso.nombre_curso)}</h2>
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
            this.removeStyles();
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
  
    addStylesDashboard: function() {
      const styleId = 'dashboard-dynamic-styles';
      let style = document.getElementById(styleId);
  
      if (!style) {
          style = document.createElement("style");
          style.id = styleId;
          document.head.appendChild(style);
      }
  
      style.textContent = `
          #dashboard-modal, .modal {
    display: none;
    position: fixed;
    z-index: 1001;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6);
    animation: fadeIn 0.3s ease-out;
}

.modal-content {
    background-color: #fff;
    padding: 2rem;
    border: none;
    width: 90%;
    max-width: 600px;
    border-radius: 1rem;
    margin: 2rem auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-5%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.modal-title {
    font-size: 1.75rem;
    font-weight: bold;
    color: #333;
}

.modal-body {
    margin-bottom: 1.5rem;
}

img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 1.5rem auto;
    border-radius: 0.5rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

p {
    margin-bottom: 1rem;
    line-height: 1.6;
    color: #555;
}

h2 {
    margin-bottom: 1rem;
    color: #333;
}

.close {
    color: #888;
    font-size: 2rem;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.2s, transform 0.2s;
}

.close:hover, .close:focus {
    color: #333;
    transform: scale(1.1);
}

.image-preview {
    display: none;
    margin-top: 1.5rem;
    text-align: center;
}

.image-preview img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 0.5rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.file-upload {
    position: relative;
    overflow: hidden;
    margin: 1.5rem 0;
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
    cursor: pointer;
}

.file-upload-label {
    display: block;
    padding: 1rem 1.5rem;
    background-color: #f8f9fa;
    border: 2px dashed #ddd;
    border-radius: 0.5rem;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s;
}

.file-upload-label:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.file-info {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 0.75rem;
}

.message {
    display: none;
    position: fixed;
    z-index: 1002;
    left: 50%;
    bottom: 2rem;
    transform: translateX(-50%);
    padding: 1rem 1.5rem;
    color: white;
    text-align: center;
    border-radius: 0.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    animation: slideUp 0.3s ease-out;
}

.message.error {
    background-color: #dc3545;
}

.message.success {
    background-color: #28a745;
}

@keyframes slideUp {
    from { transform: translate(-50%, 1rem); opacity: 0; }
    to { transform: translate(-50%, 0); opacity: 1; }
}

/* Estilos para el botón de envío */
button[type="submit"] {
    width: 100%;
    background-color: #4a90e2;
    color: white;
    font-weight: bold;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

button[type="submit"]:hover {
    background-color: #3a7bc8;
    transform: translateY(-2px);
}

button[type="submit"]:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        padding: 1.5rem;
        margin: 1rem auto;
    }

    .modal-title {
        font-size: 1.5rem;
    }

    .file-upload-label {
        padding: 0.75rem 1rem;
    }
}
      `;
  },
  
  removeStyles: function() {
      const style = document.getElementById('dashboard-dynamic-styles');
      if (style) {
          style.textContent = '';
      }
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
});
  
      
