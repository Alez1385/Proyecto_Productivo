document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const avatarUpload = document.getElementById('avatarUpload');
    const avatarImage = document.getElementById('avatarImage');
    const confirmModal = document.getElementById('confirmModal');
    const confirmUpdate = document.getElementById('confirmUpdate');
    const cancelUpdate = document.getElementById('cancelUpdate');
    const notification = document.getElementById('notification');

    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        openModal();
    });

    confirmUpdate.addEventListener('click', function() {
        closeModal();
        updateProfile();
    });

    cancelUpdate.addEventListener('click', function() {
        closeModal();
    });

    avatarUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarImage.src = e.target.result;
            }
            reader.readAsDataURL(file);
            updateAvatar(file);
        }
    });

    function openModal() {
        confirmModal.style.display = 'block';
        setTimeout(() => confirmModal.classList.add('show'), 10);
    }

    function closeModal() {
        confirmModal.classList.remove('show');
        setTimeout(() => confirmModal.style.display = 'none', 300);
    }

    function updateProfile() {
        const formData = new FormData(profileForm);

        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification();
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                alert('Error al actualizar el perfil: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al actualizar el perfil');
        });
    }

    function updateAvatar(file) {
        const formData = new FormData();
        formData.append('avatar', file);

        fetch('update_avatar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Avatar actualizado correctamente');
            } else {
                alert('Error al actualizar el avatar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al actualizar el avatar');
        });
    }

    function showNotification(message = 'Perfil actualizado correctamente') {
        notification.textContent = message;
        notification.style.display = 'block';
        setTimeout(() => notification.classList.add('show'), 10);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.style.display = 'none', 500);
        }, 3000);
    }
});