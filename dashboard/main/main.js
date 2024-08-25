document.addEventListener("DOMContentLoaded", function() {
    const menuLinks = document.querySelectorAll(".menu a");
    const sections = document.querySelectorAll("section");

    menuLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const targetId = this.getAttribute("href").substring(1);
            sections.forEach(section => {
                section.style.display = "none";
            });
            document.getElementById(targetId).style.display = "block";
        });
    });

    // Mostrar solo el resumen del dashboard al cargar
    sections.forEach(section => {
        section.style.display = "none";
    });
    document.getElementById("dashboard-overview").style.display = "block";
});
