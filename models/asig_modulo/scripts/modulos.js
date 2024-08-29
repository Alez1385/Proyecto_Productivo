function fetchAssignedModules(userTypeId) {
    if (userTypeId) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '../scripts/fetch_assigned_modules.php?user_type_id=' + userTypeId, true);
        xhr.onload = function () {
            if (this.status === 200) {
                console.log(this.responseText); // Agregar esto para ver la respuesta en la consola
                try {
                    const response = JSON.parse(this.responseText);
                    
                    const userTypeSelect = document.getElementById('userType');
                    const selectedOption = userTypeSelect.options[userTypeSelect.selectedIndex].text;
                    document.getElementById('userTypeName').textContent = selectedOption;

                    const assignedModulesList = document.getElementById('assignedModulesList');
                    assignedModulesList.innerHTML = '';

                    if (response.length > 0) {
                        response.forEach(module => {
                            const li = document.createElement('li');
                            li.textContent = module.nom_modulo;
                            assignedModulesList.appendChild(li);
                        });
                    } else {
                        assignedModulesList.innerHTML = '<li>No hay módulos asignados</li>';
                    }
                } catch (e) {
                    console.error('Error al analizar JSON:', e);
                }
            }
        };
        xhr.send();
    } else {
        document.getElementById('userTypeName').textContent = 'No se cargó';
        document.getElementById('assignedModulesList').innerHTML = '<li>No hay módulos asignados</li>';
    }
}
