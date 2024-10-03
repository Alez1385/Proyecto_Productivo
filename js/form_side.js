// sidebar-management.js

const SidebarManager = (function() {
    let isResizing = false;
    let lastDownX = 0;

    function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        const resizer = document.getElementById('sidebar-resizer');
        const overlay = document.getElementById('overlay');

        if (resizer) {
            resizer.addEventListener('mousedown', initResize, false);
        }

        if (overlay) {
            overlay.addEventListener('click', toggleSidebar);
        }
    }
    const style = document.createElement('style');
    style.textContent = `
        /*estilos de formularios de creacion y edicion */

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 999;
            display: none;
        }

        #sidebar {
            position: fixed;
            top: 0;
            right: -400px; /* Ajusta esto según el ancho inicial deseado */
            width: 400px; /* Ancho inicial del sidebar */
            height: 100%;
            background-color: #e6f2ff;
            transition: right 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            min-width: 300px; /* Ancho mínimo del sidebar */
            max-width: 50vw; /* Máximo 50% del ancho de la ventana */
        }

        #sidebar.open {
            right: 0;
        }

        #sidebar-resizer {
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100% !important;
            background-color: #f5c792;
            cursor: ew-resize;
        }
            .sidebar-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
}

    /* General form styles */
.form-container {
    margin: 0 auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

form h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
    position: relative;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    font-size: 16px;
    transition: border-color 0.3s ease-in-out;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #00bcff;
    outline: none;
}

input[type="file"] {
    border: none;
}

.error-message {
    color: #c12646;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.success-message {
    color: #00bcff;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

/* Button styles */
.btn-primary {
    width: 100%;
    padding: 12px;
    background-color: #00bcff;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

.btn-primary:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.btn-primary:hover {
    background-color: #008ecf;
}

/* Lock icon */
.lock-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 25px;
    height: 25px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.lock-icon.rotate {
    transform: translateY(-50%) rotate(180deg);
}

.lock-icon:hover {
    transform: translateY(-50%) scale(1.1);
}

/* File input styles */
input[type="file"]::file-selector-button {
    padding: 5px 15px;
    background-color: #00bcff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

textarea {
    background-color: #fff;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 4px;
    transition: border-color 0.3s ease-in-out;
    resize: none;
    width: 100%;
}

textarea:focus {
    border-color: #00bcff;
    outline: none;
}

input[type="file"]::file-selector-button:hover {
    background-color: #008ecf;
}

/* Responsive styles */
@media screen and (max-width: 768px) {
    .form-container {
        padding: 15px;
    }

    .form-group input,
    .form-group select {
        font-size: 14px;
    }

    .btn-primary {
        font-size: 14px;
    }
}

@media screen and (max-width: 480px) {
    .form-container {
        padding: 10px;
    }
}

    
    `;
    document.head.appendChild(style);
    function initResize(e) {
        isResizing = true;
        lastDownX = e.clientX;
        document.addEventListener('mousemove', resize, false);
        document.addEventListener('mouseup', stopResize, false);
    }

    function resize(e) {
        if (!isResizing) return;
        const sidebar = document.getElementById('sidebar');
        const offsetRight = document.body.offsetWidth - (e.clientX - document.body.offsetLeft);
        const minWidth = 200; // Ancho mínimo del sidebar
        const maxWidth = document.body.offsetWidth / 2; // Máximo 50% del ancho de la ventana
        if (offsetRight > minWidth && offsetRight < maxWidth) {
            sidebar.style.width = offsetRight + 'px';
            sidebar.style.right = '0px';
        }
    }

    function stopResize() {
        isResizing = false;
        document.removeEventListener('mousemove', resize, false);
        document.removeEventListener('mouseup', stopResize, false);
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const mainContent = document.querySelector('.main-content');
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            sidebar.style.right = '-' + sidebar.offsetWidth + 'px';
            if (overlay) overlay.style.display = 'none';
            if (mainContent) mainContent.classList.remove('shifted');
        } else {
            sidebar.classList.add('open');
            sidebar.style.right = '0px';
            if (overlay) overlay.style.display = 'block';
            if (mainContent) mainContent.classList.add('shifted');
        }
    }

    function openSidebar(url) {
        const sidebarContent = document.getElementById('sidebar-content');
        if (sidebarContent) {
            sidebarContent.innerHTML = '<p>Cargando...</p>'; // Indicador de carga
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    sidebarContent.innerHTML = data;
                    toggleSidebar();
                })
                .catch(error => {
                    sidebarContent.innerHTML = '<p>Error al cargar el contenido</p>';
                    console.error('Error:', error);
                });
        }
    }

    // Puedes añadir aquí la función setupFormValidation si es necesaria para el sidebar

    return {
        init: initSidebar,
        toggle: toggleSidebar,
        open: openSidebar
    };
})();

// Exportar para uso en módulos ES6
export default SidebarManager;