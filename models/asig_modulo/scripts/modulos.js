function fetchAssignedModules(userTypeId) {
    const userTypeName = document.getElementById('userType').options[document.getElementById('userType').selectedIndex].text;
    document.getElementById('userTypeName').textContent = 'Módulos asignados a: ' + userTypeName;

    fetch('../scripts/get_assigned_modules.php?userTypeId=' + userTypeId)
        .then(response => response.json())
        .then(data => {
            const assignedModulesList = document.getElementById('assignedModulesList');
            assignedModulesList.innerHTML = '';
            if (Array.isArray(data)) {
                data.forEach(module => {
                    const li = document.createElement('li');
                    li.textContent = module.nom_modulo;
                    assignedModulesList.appendChild(li);
                });
            } else {
                console.error('La respuesta no es un array:', data);
                showNotification('Error al cargar los módulos asignados.', false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar los módulos asignados: ' + error.message, false);
        });
}

document.getElementById('assignmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../scripts/asig_modulo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        showNotification(data);
        fetchAssignedModules(document.getElementById('userType').value);
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud.', false);
    });
});
