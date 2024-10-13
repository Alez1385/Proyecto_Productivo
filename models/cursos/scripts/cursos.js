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

function openAssignTeacherModal(courseId, currentTeacherId) {
    const modal = document.getElementById('assignTeacherModal');
    const courseIdInput = document.getElementById('courseId');
    const teacherSelect = document.getElementById('teacherSelect');

    courseIdInput.value = courseId;
    if (currentTeacherId && currentTeacherId !== 'null') {
        teacherSelect.value = currentTeacherId;
    } else {
        teacherSelect.selectedIndex = 0;
    }

    modal.style.display = 'block';
}

document.querySelector('.modal .close').addEventListener('click', function() {
    document.getElementById('assignTeacherModal').style.display = 'none';
});

document.getElementById('assignTeacherForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const courseId = document.getElementById('courseId').value;
    const teacherId = document.getElementById('teacherSelect').value;

    console.log('Enviando solicitud - CourseId:', courseId, 'TeacherId:', teacherId);

    if (!courseId || !teacherId) {
        alert('Por favor, asegúrate de que se ha seleccionado un curso y un profesor.');
        return;
    }

    fetch('assign_teacher.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `courseId=${encodeURIComponent(courseId)}&teacherId=${encodeURIComponent(teacherId)}`
    })
    .then(response => {
        console.log('Respuesta recibida:', response);
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            alert('Profesor asignado con éxito');
            document.getElementById('assignTeacherModal').style.display = 'none';
            // Actualizar la información del curso en la página
            const courseItem = document.querySelector(`[data-course-id="${courseId}"]`);
            const professorInfo = courseItem.querySelector('p:nth-child(5)');
            professorInfo.innerHTML = `<strong>Profesor: </strong>${data.teacherName}`;
            console.log('Información del profesor actualizada en la página');
        } else {
            alert('Error al asignar el profesor: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al asignar el profesor.');
    });
});
