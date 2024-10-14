(function () {
    const DEBUG = false;
    const log = (...args) => DEBUG && console.log(...args);
    const logError = (...args) => console.error(...args);

    const showLoading = () => {
        if (!document.getElementById("loading-spinner")) {
            const spinner = document.createElement("div");
            spinner.id = "loading-spinner";
            spinner.className = "fixed inset-0 bg-white bg-opacity-80 flex justify-center items-center z-[1000]";
            spinner.innerHTML = `
                                    <div class="grid min-h-[140px] w-full place-items-center overflow-x-scroll rounded-lg p-6 lg:overflow-visible">
                      <svg class="text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24">
                        <path
                          d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                          stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                          d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                          stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-900">
                        </path>
                      </svg>
                    </div>
            `;
            document.body.appendChild(spinner);
        }
    };

    const hideLoading = () => {
        const spinner = document.getElementById("loading-spinner");
        if (spinner) spinner.remove();
    };

    const showMessage = (message, type) => {
        const messageDiv = document.createElement("div");
        messageDiv.className = `fixed top-5 left-1/2 transform -translate-x-1/2 px-4 py-2 rounded-md shadow-lg z-50 text-white text-sm ${
            type === 'error' ? 'bg-red-500' : 'bg-green-500'
        }`;
        messageDiv.textContent = message;
        document.body.appendChild(messageDiv);
        setTimeout(() => messageDiv.remove(), 5000);
    };

    const showError = (message) => showMessage(message, "error");
    const showSuccess = (message) => showMessage(message, "success");

    // Update the closeModal function
// Update the closeModal function
const closeModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.remove();
    }
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

   // Update the showEnrollmentModal function
const showEnrollmentModal = (courseId) => { 
    const modalHTML = `
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="inscripcionModal">
            <div style="top: 50%; transform: translateY(-50%);" class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Opciones de Inscripción</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">Elija una opción para inscribirse al curso:</p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button id="preinscripcionBtn" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                            Preinscripción Rápida
                        </button>
                        <button id="inscripcionCompletaBtn" class="mt-3 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Inscripción Completa
                        </button>
                    </div>
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

    // Close modal when clicking outside the modal
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal("inscripcionModal");
        }
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
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="preinscripcionModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150" aria-label="Close" onclick="enrollmentApp.closeModal('preinscripcionModal')">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 text-center">Preinscripción Rápida</h3>
                    <form id="preinscripcionForm" class="mt-4">
                        <input type="hidden" name="curso_id" value="${courseId}">
                        <div class="mb-4">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                            <input type="text" id="nombre" name="nombre" value="${userData.nombre || ""}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                            <input type="email" id="email" name="email" value="${userData.email || ""}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="mb-4">
                            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" value="${userData.telefono || ""}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Enviar Preinscripción
                        </button>
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
            const response = await fetch("/scripts/preinscribir.php", {
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
    showSuccess: showSuccess,
    showError: showError,
    showLoading: showLoading,
    hideLoading: hideLoading,
    showEnrollmentModal: showEnrollmentModal,
    sendEnrollment: sendEnrollment,
    fullEnrollment: fullEnrollment
};
})();