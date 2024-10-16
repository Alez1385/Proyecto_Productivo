const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

// Function to show modal messages (both error and success)
function showModal(message, isError = true) {
    const modal = document.getElementById('errorModal');
    const modalMessage = document.getElementById('errorMessage');
    const modalIcon = document.querySelector('.modal-icon');
    const modalTitle = document.querySelector('.modal-content h2');

    modalMessage.textContent = message;
    
    if (isError) {
        modalIcon.innerHTML = '&#9888;'; // Warning icon
        modalTitle.textContent = 'Error';
        modalIcon.style.color = '#c0392b'; // Red color for error
    } else {
        modalIcon.innerHTML = '&#10004;'; // Checkmark icon
        modalTitle.textContent = 'Success';
        modalIcon.style.color = '#27ae60'; // Green color for success
    }

    modal.style.display = 'block';
}

// Close modal when clicking the close button
document.getElementById('closeModal').onclick = function() {
    document.getElementById('errorModal').style.display = 'none';
};

// Close modal when clicking outside the modal content
window.onclick = function(event) {
    const modal = document.getElementById('errorModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};

// Function to show the register form
function showRegisterForm() {
    container.classList.add("active");
}

// Handle URL parameters on page load
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const message = urlParams.get('message');
    const showRegister = urlParams.get('show');

    if (error) {
        showModal(decodeURIComponent(error), true);
    } else if (message) {
        showModal(decodeURIComponent(message), false);
    }

    if (showRegister === 'register') {
        showRegisterForm();
    }
});