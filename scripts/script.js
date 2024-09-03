document.addEventListener('DOMContentLoaded', function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slider-item');
    const totalSlides = slides.length;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.style.display = 'block';
            } else {
                slide.style.display = 'none';
            }
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }

    // Mostrar el primer slide
    showSlide(currentSlide);

    // Cambiar slide cada 5 segundos
    setInterval(nextSlide, 5000);

    // Opcional: Añadir botones de navegación
    const prevButton = document.createElement('button');
    prevButton.textContent = 'Anterior';
    prevButton.addEventListener('click', prevSlide);

    const nextButton = document.createElement('button');
    nextButton.textContent = 'Siguiente';
    nextButton.addEventListener('click', nextSlide);

    document.querySelector('.slider').appendChild(prevButton);
    document.querySelector('.slider').appendChild(nextButton);
});