function validateForm() {
    const form = document.getElementById('contactForm');
    const phone = form.phone.value.trim();
    

    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phone)) {
        alert('El número de teléfono debe tener 10 dígitos.');
        return false;
    }

    return true;
}

