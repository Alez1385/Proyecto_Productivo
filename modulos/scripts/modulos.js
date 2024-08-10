
var mySelect = document.getElementById('user');
    mySelect.value ="";



function fetchAssignedModules(userId) {
    if (userId) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '../scripts/fetch_assigned_modules.php?user_id=' + userId, true);
        xhr.onload = function () {
            if (this.status === 200) {
                console.log(this.responseText); // Agregar esto para ver la respuesta en la consola
                try {
                    const response = JSON.parse(this.responseText);
                    
                    const userName = document.getElementById('user');
                    const selectedOption = userName.options[userName.selectedIndex].text;
                    document.getElementById('userName').textContent = selectedOption;

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
        document.getElementById('userName').textContent = 'No se cargo';
        document.getElementById('assignedModulesList').innerHTML = '<li>No hay módulos asignados</li>';
    }
}
