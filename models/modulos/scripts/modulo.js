function showSection(sectionId) {
    document.getElementById('moduleList').style.display = 'none';
    document.getElementById('assignModules').style.display = 'none';
    document.getElementById(sectionId).style.display = 'block';
}

function filterModules() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const moduleList = document.querySelector('.module-list');
    const modules = moduleList.getElementsByClassName('module-item');
    for (let i = 0; i < modules.length; i++) {
        const moduleDetails = modules[i].getElementsByClassName('module-details')[0];
        const name = moduleDetails.getElementsByTagName('h2')[0].textContent.toLowerCase();
        if (name.indexOf(filter) > -1) {
            modules[i].style.display = '';
        } else {
            modules[i].style.display = 'none';
        }
    }
}

function deleteModule(moduleId) {
    if (confirm("¿Estás seguro de que quieres eliminar este módulo?")) {
        fetch('../delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_modulo=' + encodeURIComponent(moduleId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const moduleElement = document.querySelector(`[data-module-id="${moduleId}"]`);
                if (moduleElement) {
                    moduleElement.remove();
                }
                alert('Módulo eliminado exitosamente');
            } else {
                alert('Error al eliminar el módulo: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al eliminar el módulo');
        });
    }
}

function fetchAssignedModules(userTypeId) {
    fetch(`../../asig_modulo/scripts/fetch_assigned_modules.php?id_tipo_usuario=${userTypeId}`)
        .then(response => response.json())
        .then(data => {
            const assignedModulesList = document.getElementById('assignedModulesList');
            assignedModulesList.innerHTML = '';
            data.forEach(module => {
                const li = document.createElement('li');
                li.textContent = module.nom_modulo;
                assignedModulesList.appendChild(li);
            });
            document.getElementById('userTypeName').textContent = data.userTypeName;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al obtener los módulos asignados');
        });
}

document.addEventListener('DOMContentLoaded', function() {
    showSection('moduleList');

    document.getElementById('assignmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('../../asig_modulo/scripts/asig_modulo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                fetchAssignedModules(document.getElementById('userType').value);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al asignar los módulos');
        });
    });
});