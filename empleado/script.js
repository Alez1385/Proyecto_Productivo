function validateForm() {
    const form = document.getElementById('contactForm');
    const phone = form.tel_cliente.value.trim();
    

    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phone)) {
        alert('El número de teléfono debe tener 10 dígitos.');
        return false;
    }

    return true;
}

document.getElementById('contactForm').addEventListener('submit', function(event) {
    var archivoInput = document.getElementById('curriculum');
    var archivos = archivoInput.files;
    var tiposPermitidos = ["application/pdf", "image/jpeg", "image/png"];
    var valido = false;
  
    for (var i = 0; i < archivos.length; i++) {
      var archivo = archivos[i];
      if (tiposPermitidos.includes(archivo.type)) {
        valido = true;
      } else {
        valido = false;
        break;
      }
    }
  
    if (!valido) {
      event.preventDefault();
      alert('Por favor selecciona un archivo PDF o una imagen (JPG o PNG).');
    }
  });

  document.getElementById('contactForm').addEventListener('submit', function(event) {
    var archivoInput = document.getElementById('foto');
    var archivos = archivoInput.files;
    var tiposPermitidos = ["application/pdf", "image/jpeg", "image/png"];
    var valido = false;
  
    for (var i = 0; i < archivos.length; i++) {
      var archivo = archivos[i];
      if (tiposPermitidos.includes(archivo.type)) {
        valido = true;
      } else {
        valido = false;
        break;
      }
    }
  
    if (!valido) {
      event.preventDefault();
      alert('Por favor selecciona un archivo PDF o una imagen (JPG o PNG).');
    }
  });