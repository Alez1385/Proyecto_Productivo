function deleteCourse(courseId) {
    if (confirm("¿Estás seguro de que deseas eliminar este curso?")) {
        fetch('scripts/delete_courses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_curso=' + encodeURIComponent(courseId)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const courseElement = document.querySelector(`[data-course-id="${courseId}"]`);
                    if (courseElement) {
                        courseElement.remove();
                    }
                    alert('Curso eliminado exitosamente');
                } else {
                    throw new Error(data.message || 'Error desconocido al eliminar el curso');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al eliminar el curso: ' + error.message);
            });
    }
}

function toggleFilters() {
    document.getElementById("filterContent").classList.toggle("show");
}

function updateFilters() {
    const checkboxes = document.querySelectorAll('#filterContent input[type="checkbox"]');
    const filterTags = document.getElementById('filterTags');
    filterTags.innerHTML = '';

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const tag = document.createElement('span');
            tag.className = 'filter-tag';
            tag.innerHTML = `${checkbox.value} <span class="close" onclick="removeFilter('${checkbox.value}')">&times;</span>`;
            filterTags.appendChild(tag);
        }
    });

    filterCourses();
}

function removeFilter(value) {
    const checkbox = document.querySelector(`#filterContent input[value="${value}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateFilters();
    }
}

function filterCourses() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const selectedFilters = Array.from(document.querySelectorAll('#filterContent input[type="checkbox"]:checked')).map(cb => cb.value);
    const courseList = document.querySelector('.course-list');
    const courses = courseList.getElementsByClassName('course-item');
    let found = false;

    for (let i = 0; i < courses.length; i++) {
        const courseDetails = courses[i].getElementsByClassName('course-details')[0];
        const name = courseDetails.getElementsByTagName('h2')[0].textContent.toLowerCase();
        const description = courseDetails.getElementsByTagName('p')[0].textContent.toLowerCase();
        const category = courses[i].dataset.category.toLowerCase();

        const matchesSearch = name.includes(searchInput) || description.includes(searchInput);
        const matchesFilters = selectedFilters.length === 0 || selectedFilters.includes(category);

        if (matchesSearch && matchesFilters) {
            courses[i].style.display = '';
            found = true;
        } else {
            courses[i].style.display = 'none';
        }
    }

    let noResultsMessage = document.getElementById('noResultsMessage');
    if (!found) {
        if (!noResultsMessage) {
            noResultsMessage = document.createElement('p');
            noResultsMessage.id = 'noResultsMessage';
            noResultsMessage.textContent = 'No se encontraron cursos que coincidan con la búsqueda.';
            courseList.appendChild(noResultsMessage);
        }
    } else if (noResultsMessage) {
        noResultsMessage.remove();
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#filterContent input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateFilters);
    });

    document.getElementById('searchInput').addEventListener('keyup', filterCourses);
});