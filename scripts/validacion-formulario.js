document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('personalDataForm');
    const inputs = form.querySelectorAll('input, select');

    // Función para validar un campo
    function validateField(input) {
        const errorElement = document.getElementById(`error-${input.name}`);
        let isValid = true;
        let errorMessage = '';

        if (input.required && !input.value.trim()) {
            isValid = false;
            errorMessage = 'Este campo es requerido';
        } else {
            switch (input.name) {
                case 'nombre':
                case 'apellido':
                    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(input.value)) {
                        isValid = false;
                        errorMessage = 'Solo se permiten letras y espacios';
                    }
                    break;
                case 'documento':
                    if (!/^\d+$/.test(input.value)) {
                        isValid = false;
                        errorMessage = 'Solo se permiten números';
                    }
                    break;
                case 'fecha_nac':
                    const fechaNac = new Date(input.value);
                    const hoy = new Date();
                    if (fechaNac > hoy) {
                        isValid = false;
                        errorMessage = 'La fecha no puede ser futura';
                    }
                    break;
                case 'mail':
                    if (!/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/.test(input.value)) {
                        isValid = false;
                        errorMessage = 'Ingrese un email válido';
                    }
                    break;
                case 'telefono':
                    if (!/^\d{10}$/.test(input.value)) {
                        isValid = false;
                        errorMessage = 'Ingrese un número de teléfono válido (10 dígitos)';
                    }
                    break;
                case 'clave':
                    if (input.value.length < 8) {
                        isValid = false;
                        errorMessage = 'La clave debe tener al menos 8 caracteres';
                    }
                    break;
                case 'foto':
                    if (input.files.length > 0) {
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (!allowedTypes.includes(input.files[0].type)) {
                            isValid = false;
                            errorMessage = 'El archivo debe ser una imagen (JPEG, PNG o GIF)';
                        }
                    }
                    break;
            }
        }

        if (!isValid) {
            errorElement.textContent = errorMessage;
            input.classList.add('invalid');
            input.classList.remove('valid');
        } else {
            errorElement.textContent = '';
            input.classList.remove('invalid');
            input.classList.add('valid');
        }

        return isValid;
    }

    // Validar todos los campos cuando se envía el formulario
    form.addEventListener('submit', function(e) {
        let isFormValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            e.preventDefault();
        }
    });

    // Validar cada campo cuando cambia su valor
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });

    // Actualizar el nombre del archivo seleccionado
    document.getElementById('foto').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo';
        document.getElementById('file-name').textContent = fileName;
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#clave');

    togglePassword.addEventListener('click', function(e) {
        // Prevenir que el botón envíe el formulario
        e.preventDefault();
        
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the icon
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});