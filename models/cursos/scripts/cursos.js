function deleteCourse(courseId) {
    if (confirm("Estas seguro de eliminar el curso?")) {
        fetch("./scripts/delete_courses.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: "id_curso=" + courseId,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              const courseElement = document.querySelector(
                `[data-course-id="${courseId}"]`
              );
              if (courseElement) {
                courseElement.remove();
              }
              alert("Curso eliminado correctamente");
            } else {
              alert("Error deleting course: " + data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert(
              "Un error ocurrio al eliminar el curso: " + error.message
            );
          });
    }
}



function filterCourses() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const level = document.getElementById('levelFilter').value;
    let url = 'cursos.php?';

    if (category) url += `category=${category}&`;
    if (status) url += `status=${status}&`;
    if (level) url += `level=${level}&`;

    window.location.href = url;
}

function removeFilter(filterType) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.delete(filterType);

    const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
    window.location.href = newUrl;
}

function resetFilters() {
    window.location.href = 'cursos.php';
}
