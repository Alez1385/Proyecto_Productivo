// Función para añadir estilos al head
function addStylesToHead(passwordId, toggleId) {
    const styleElement = document.createElement('style');
    styleElement.textContent = `
        #${passwordId} {
            position: relative;
            width: 300px;
        }
        #${toggleId} {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    `;
    document.head.appendChild(styleElement);
}

function togglePassword(passwordId, toggleId) {
    const passwordField = document.getElementById(passwordId);
    const lockIcon = document.getElementById(toggleId);
    
    // Añadir estilos si aún no se han añadido
    if (!document.querySelector(`style[data-for="${passwordId}"]`)) {
        const styleElement = document.createElement('style');
        styleElement.setAttribute('data-for', passwordId);
        styleElement.textContent = `
            #${passwordId} {
                position: relative;
                width: 300px;
            }
            #${toggleId} {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
            }
        `;
        document.head.appendChild(styleElement);
    }
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';  // Cambiar a texto para mostrar la contraseña
        lockIcon.src = '/login/img/eye-close.svg';  // Cambiar el ícono a 'cerrado'
    } else {
        passwordField.type = 'password';  // Cambiar a contraseña para ocultarla
        lockIcon.src = '/login/img/eye-open.svg';  // Cambiar el ícono a 'abierto'
    }
}