document.addEventListener('DOMContentLoaded', function() {
    let index = 0;
    const slides = document.getElementById("slides");
    const slidesArray = slides.querySelectorAll(".carousel-slide");
    const total = slidesArray.length;

    // Crear dots
    const dotsContainer = document.getElementById("dots");
    for (let i = 0; i < total; i++) {
        const dot = document.createElement("div");
        dot.classList.add("dot");
        dot.addEventListener("click", () => showSlide(i));
        dotsContainer.appendChild(dot);
    }

    const dots = document.querySelectorAll(".dot");

    // Ajustar ancho del carrusel dinámicamente
    slides.style.width = `${100 * total}%`;

    function showSlide(i) {
        index = (i + total) % total;
        slides.style.transform = `translateX(-${index * 100}%)`;

        // Actualizar dots activos
        dots.forEach((dot, idx) => {
            if (idx === index) {
                dot.classList.add("active");
            } else {
                dot.classList.remove("active");
            }
        });

        // Reiniciar animación de progreso
        resetProgress();
    }

    function nextSlide() {
        showSlide(index + 1);
    }

    function prevSlide() {
        showSlide(index - 1);
    }

    // Animación de progreso
    const progress = document.getElementById("progress");
    let progressInterval = null; // INICIALIZADA CON null

    function resetProgress() {
        clearInterval(progressInterval); // Ahora es seguro
        progress.style.transform = "scaleX(0)";
        progress.style.transition = "none";

        setTimeout(() => {
            progress.style.transition = "transform 5s linear";
            progress.style.transform = "scaleX(1)";
        }, 50);
    }

    // Auto avanzar
    let slideInterval = setInterval(() => {
        nextSlide();
    }, 5000);

    // Pausar al pasar el ratón
    const carousel = document.querySelector(".carousel-container");
    carousel.addEventListener("mouseenter", () => {
        clearInterval(slideInterval);
        clearInterval(progressInterval);
        progress.style.transition = "none";
    });

    carousel.addEventListener("mouseleave", () => {
        resetProgress();
        slideInterval = setInterval(() => {
            nextSlide();
        }, 5000);
    });

    // Iniciar con el primer slide
    showSlide(0);
    // Iniciar animación de progreso
    resetProgress();
});