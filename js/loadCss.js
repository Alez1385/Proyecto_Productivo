// Función para cargar un archivo CSS dinámicamente
function loadCSS(filename) {
    var link = document.createElement('link'); // Crear un elemento <link>
    link.rel = 'stylesheet';                   // Establecer el tipo de relación
    link.type = 'text/css';                    // Especificar que es un archivo CSS
    link.href = filename;                      // La ruta al archivo CSS

    // Añadir el <link> al <head>
    document.head.appendChild(link);
}

