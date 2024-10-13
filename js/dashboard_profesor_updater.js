const dashboardProfesorUpdater = {
    updateInterval: 30000, // Actualizar cada 30 segundos
    cursos: [],

    init: function () {
        this.updateDashboard();
        setInterval(() => this.updateDashboard(), this.updateInterval);
    },

    updateDashboard: function () {
        fetch("../dashboard/dashboard_data_profesor.php")
            .then(response => response.json())
            .then(data => {
                console.log("Datos recibidos:", data); // Para depuración
                if (data.error) {
                    console.error("Error:", data.message);
                    this.displayErrorToUser(data.message);
                    return;
                }
                if (!data.cursos || !Array.isArray(data.cursos)) {
                    console.error("Datos de cursos inválidos:", data);
                    this.displayErrorToUser("Los datos de cursos recibidos son inválidos.");
                    return;
                }
                this.updateDashboardSummary(data.cursos);
                this.updateCursosAsignados(data.cursos);
                this.createEstudiantesPorCursoChart(data.cursos);
                this.cursos = data.cursos;
            })
            .catch((error) => {
                console.error("Error de conexión:", error);
                this.displayErrorToUser("Error de conexión. Por favor, intenta de nuevo más tarde. Detalles: " + error.message);
            });
    },

    updateDashboardSummary: function (cursos) {
        console.log("Actualizando resumen del dashboard con cursos:", cursos);
        const cursosCount = cursos.length;
        let estudiantesCount = 0;
        let horasClase = 0;

        cursos.forEach(curso => {
            estudiantesCount += parseInt(curso.num_estudiantes || 0);
            horasClase += this.calcularHorasClase(curso.horarios);
        });

        document.getElementById('cursos-count').textContent = cursosCount;
        document.getElementById('estudiantes-count').textContent = estudiantesCount;
        document.getElementById('horas-clase').textContent = horasClase;
    },

    updateCursosAsignados: function (cursos) {
        console.log("Actualizando cursos asignados:", cursos);
        const cursosContainer = document.getElementById('cursos-asignados-list');
        if (!cursosContainer) {
            console.error("No se encontró el contenedor de cursos asignados");
            return;
        }

        cursosContainer.innerHTML = '';

        if (!cursos || cursos.length === 0) {
            cursosContainer.innerHTML = '<p>No tienes cursos asignados actualmente.</p>';
            return;
        }

        cursos.forEach(curso => {
            const cursoElement = document.createElement('div');
            cursoElement.className = 'curso-item';
            cursoElement.innerHTML = `
                <h3>${this.escapeHtml(curso.nombre_curso)}</h3>
                <p><strong>Categoría:</strong> ${this.escapeHtml(curso.nombre_categoria)}</p>
                <p><strong>Descripción:</strong> ${this.escapeHtml(curso.descripcion)}</p>
                <p><strong>Nivel:</strong> ${this.escapeHtml(curso.nivel_educativo)}</p>
                <p><strong>Duración:</strong> ${this.escapeHtml(curso.duracion)} semanas</p>
                <p><strong>Horarios:</strong> ${this.escapeHtml(curso.horarios || 'No especificado')}</p>
                <p><strong>Estudiantes inscritos:</strong> ${this.escapeHtml(curso.num_estudiantes)}</p>
                <a href="../models/asistencia/registrar_asistencia.php?id_curso=${this.escapeHtml(curso.id_curso)}" class="btn-registrar">
                    Registrar Asistencia
                </a>
            `;
            cursosContainer.appendChild(cursoElement);
        });
    },

    createEstudiantesPorCursoChart: function (cursos) {
        const ctx = document.getElementById('estudiantesPorCursoChart');
        if (!ctx) return;

        if (this.chart) {
            this.chart.destroy();
        }

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: cursos.map(curso => curso.nombre_curso),
                datasets: [{
                    label: 'Número de Estudiantes',
                    data: cursos.map(curso => curso.num_estudiantes),
                    backgroundColor: 'rgba(52, 152, 219, 0.6)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Estudiantes'
                        }
                    }
                }
            }
        });
    },

    calcularHorasClase: function (horarios) {
        if (!horarios) return 0;
        const horariosList = horarios.split(', ');
        let totalHoras = 0;
        horariosList.forEach(horario => {
            const [, horaInicio, horaFin] = horario.match(/(\d{2}:\d{2})-(\d{2}:\d{2})/) || [];
            if (horaInicio && horaFin) {
                const inicio = new Date(`1970-01-01T${horaInicio}:00`);
                const fin = new Date(`1970-01-01T${horaFin}:00`);
                totalHoras += (fin - inicio) / 3600000; // Convertir milisegundos a horas
            }
        });
        return Math.round(totalHoras);
    },

    displayErrorToUser: function (message) {
        const errorDiv = document.getElementById("error-message");
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = "block";
        } else {
            alert(message);
        }
        console.error("Error mostrado al usuario:", message);
    },

    escapeHtml: function (unsafe) {
        if (unsafe === null || unsafe === undefined) {
            return '';
        }
        if (typeof unsafe !== 'string') {
            unsafe = String(unsafe);
        }
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
};
