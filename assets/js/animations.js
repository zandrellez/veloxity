document.addEventListener("DOMContentLoaded", function () {
    const pinContainer = document.querySelector('.velox-search-pin-container');
    const masterCard = document.getElementById('searchCardTarget');

    if (!pinContainer || !masterCard) return;

    window.addEventListener('scroll', () => {
        const rect = pinContainer.getBoundingClientRect();
        const scrollableDistance = rect.height - window.innerHeight;
        
        if (rect.top <= 0 && rect.bottom >= window.innerHeight) {
            
            let progress = Math.abs(rect.top) / scrollableDistance;
            
            // Card floats in during the first 60% of the scroll
            let cardProgress = Math.min(progress / 0.6, 1);
            let easeOut = 1 - Math.pow(1 - cardProgress, 3); 
            
            masterCard.style.opacity = easeOut;
            masterCard.style.transform = `translateY(${80 - (easeOut * 80)}px)`;
            
        } else if (rect.top > 0) {
            masterCard.style.opacity = 0;
            masterCard.style.transform = `translateY(80px)`;
            
        } else if (rect.bottom < window.innerHeight) {
            masterCard.style.opacity = 1;
            masterCard.style.transform = `translateY(0px)`;
        }
    }, { passive: true });
});