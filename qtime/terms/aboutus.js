document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    let currentIndex = 0;
    let slideInterval;

    function initSlideshow() {
        if (slides.length === 0) return;
        slides[0].classList.add('active');
        dots[0].classList.add('active');
        startSlideShow();
        prevBtn.addEventListener('click', prevSlide);
        nextBtn.addEventListener('click', nextSlide);
        
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                goToSlide(parseInt(this.dataset.index));
            });
        });
    }

    function showSlide(index) {
        if (index >= slides.length) index = 0;
        if (index < 0) index = slides.length - 1;
   
        currentIndex = index;
 
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    function nextSlide() {
        resetSlideTimer();
        showSlide(currentIndex + 1);
    }

    function prevSlide() {
        resetSlideTimer();
        showSlide(currentIndex - 1);
    }

    function goToSlide(index) {
        resetSlideTimer();
        showSlide(index);
    }

    function startSlideShow() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    function resetSlideTimer() {
        clearInterval(slideInterval);
        startSlideShow();
    }

    initSlideshow();
});