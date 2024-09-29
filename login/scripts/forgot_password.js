// File: scripts/forgot_password.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const messageContainer = document.getElementById('messageContainer');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        fetch('scripts/send_reset_link.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                messageContainer.innerHTML = `<div class="error-message"><p>${data.error}</p></div>`;
            } else {
                messageContainer.innerHTML = `<div class="success-message"><p>${data.message}</p></div>`;
                form.reset();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageContainer.innerHTML = '<div class="error-message"><p>An error occurred. Please try again later.</p></div>';
        });
    });
});