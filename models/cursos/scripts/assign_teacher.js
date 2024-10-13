document.getElementById('assignTeacherForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const courseId = document.getElementById('courseId').value;
    const teacherId = document.getElementById('teacherSelect').value;

    console.log('Enviando solicitud - CourseId:', courseId, 'TeacherId:', teacherId);

    if (!courseId || !teacherId) {
        alert('Por favor, asegúrate de que se ha seleccionado un profesor.');
        return;
    }

    fetch('assign_teacher.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `courseId=${encodeURIComponent(courseId)}&teacherId=${encodeURIComponent(teacherId)}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.success) {
            alert('Profesor asignado/actualizado con éxito');
            window.opener.location.reload(); // Recarga la página principal
            window.close(); // Cierra la ventana actual
        } else {
            alert('Error al asignar/actualizar el profesor: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al asignar/actualizar el profesor.');
    });
});
