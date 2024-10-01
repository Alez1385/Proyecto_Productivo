let isResizing = false;
let lastDownX = 0;
const sidebar = document.getElementById('sidebar');
const resizer = document.getElementById('sidebar-resizer');
resizer.addEventListener('mousedown', initResize, false);

function initResize(e) {
    isResizing = true;
    lastDownX = e.clientX;
    document.addEventListener('mousemove', resize, false);
    document.addEventListener('mouseup', stopResize, false);
}

function resize(e) {
    if (!isResizing) return;
    const offsetRight = document.body.offsetWidth - (e.clientX - document.body.offsetLeft);
    const minWidth = 200; // Ancho mínimo del sidebar
    const maxWidth = document.body.offsetWidth / 2; // Máximo 50% del ancho de la ventana
    if (offsetRight > minWidth && offsetRight < maxWidth) {
        sidebar.style.width = offsetRight + 'px';
        sidebar.style.right = '0px';
    }
}

function stopResize(e) {
    isResizing = false;
    document.removeEventListener('mousemove', resize, false);
    document.removeEventListener('mouseup', stopResize, false);
}

// Modificar la función toggleSidebar para manejar el ancho correctamente
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const mainContent = document.querySelector('.main-content');

    if (sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        sidebar.style.right = '-' + sidebar.offsetWidth + 'px'; // Usa offsetWidth en lugar de style.width
        overlay.style.display = 'none';
        mainContent.classList.remove('shifted');
    } else {
        sidebar.classList.add('open');
        sidebar.style.right = '0px';
        overlay.style.display = 'block';
        mainContent.classList.add('shifted');
    }
}

function openSidebar(url) {
    const sidebarContent = document.getElementById('sidebar-content');
    sidebarContent.innerHTML = '<p>Cargando...</p>'; // Indicador de carga
    fetch(url)
        .then(response => response.text())
        .then(data => {
            sidebarContent.innerHTML = data;
            toggleSidebar();
            setupFormValidation();
        })
        .catch(error => {
            sidebarContent.innerHTML = '<p>Error al cargar el contenido</p>';
            console.error('Error:', error);
        });
}
