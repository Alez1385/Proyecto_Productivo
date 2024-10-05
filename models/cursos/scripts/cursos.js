function deleteCourse(courseId) {
  if (confirm("¿Estás seguro de que deseas eliminar este curso?")) {
      fetch('delete_course.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: 'id_curso=' + courseId
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  document.querySelector(`[data-course-id="${courseId}"]`).remove();
                  alert('Curso eliminado con éxito');
              } else {
                  alert('Error eliminando el curso: ' + data.message);
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('Ocurrió un error al eliminar el curso.');
          });
  }
}

function filterCourses() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const categoryFilter = document.getElementById('categoryFilter').value;
  const statusFilter = document.getElementById('statusFilter').value;
  const levelFilter = document.getElementById('levelFilter').value;
  const courses = document.querySelectorAll('.course-item');

  courses.forEach(course => {
      const courseName = course.querySelector('h2').textContent.toLowerCase();
      const courseDescription = course.querySelector('p').textContent.toLowerCase();
      const courseCategory = course.getAttribute('data-category');
      const courseStatus = course.getAttribute('data-status');
      const courseLevel = course.getAttribute('data-level');

      const matchesSearch = courseName.includes(searchInput) || courseDescription.includes(searchInput);
      const matchesCategory = categoryFilter === '' || courseCategory === categoryFilter;
      const matchesStatus = statusFilter === '' || courseStatus === statusFilter;
      const matchesLevel = levelFilter === '' || courseLevel === levelFilter;

      if (matchesSearch && matchesCategory && matchesStatus && matchesLevel) {
          course.style.display = '';
      } else {
          course.style.display = 'none';
      }
  });

  updateAppliedFilters();
}

function updateAppliedFilters() {
  const appliedFilters = document.getElementById('appliedFilters');
  appliedFilters.innerHTML = '';

  const filters = [
      { id: 'categoryFilter', name: 'Categoría' },
      { id: 'statusFilter', name: 'Estado' },
      { id: 'levelFilter', name: 'Nivel' }
  ];

  filters.forEach(filter => {
      const filterElement = document.getElementById(filter.id);
      if (filterElement.value) {
          const filterTag = document.createElement('div');
          filterTag.className = 'filter-tag';
          filterTag.setAttribute('data-filter', filter.id);
          filterTag.innerHTML = `
              <span>${filter.name}: ${filterElement.options[filterElement.selectedIndex].text}</span>
              <span class="filter-close" onclick="removeFilter('${filter.id}')">&times;</span>
          `;
          appliedFilters.appendChild(filterTag);
      }
  });
}

function removeFilter(filterId) {
  document.getElementById(filterId).value = '';
  filterCourses();
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('categoryFilter').value = '';
  document.getElementById('statusFilter').value = '';
  document.getElementById('levelFilter').value = '';
  filterCourses();
}

document.getElementById('overlay').addEventListener('click', toggleSidebar);