document.addEventListener("DOMContentLoaded", () => {
    const statContainer = document.querySelector('.cargo-stats-wide');
    const stats = document.querySelectorAll('.stat-number');

    if (!statContainer || stats.length === 0) return;

    // The animation logic
    const animateStats = (entries, observer) => {
        entries.forEach(entry => {
            // When the section snaps into view
            if (entry.isIntersecting) {
                stats.forEach(stat => {
                    const target = parseFloat(stat.getAttribute('data-target'));
                    const suffix = stat.getAttribute('data-suffix') || '';
                    const decimals = parseInt(stat.getAttribute('data-decimals')) || 0;
                    
                    const duration = 1800; // 1.8 seconds for the full count
                    let startTime = null;

                    const step = (timestamp) => {
                        if (!startTime) startTime = timestamp;
                        const progress = Math.min((timestamp - startTime) / duration, 1);
                        
                        // easeOutExpo for a rapid start that slows down at the end
                        const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                        
                        const currentVal = (easeProgress * target).toFixed(decimals);
                        stat.textContent = currentVal + suffix;

                        if (progress < 1) {
                            requestAnimationFrame(step);
                        } else {
                            // Ensure it lands exactly on the target value
                            stat.textContent = target.toFixed(decimals) + suffix;
                        }
                    };
                    
                    requestAnimationFrame(step);
                });
                
                // Stop observing once the animation has run so it doesn't reset on every scroll
                observer.unobserve(entry.target);
            }
        });
    };

    // Set up the observer to trigger when 50% of the row is visible
    const observer = new IntersectionObserver(animateStats, {
        threshold: 0.5 
    });

    observer.observe(statContainer);
});