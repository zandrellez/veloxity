document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.getElementById("veloxNavbar");
    const triggerBtn = document.getElementById("veloxMenuTriggerBtn");
    const hero = document.getElementById("gp-veloxity");

    let lastScrollY = window.scrollY;
    let hasTriggeredInitialScroll = false;

    function setScrolledState(shouldScroll) {
        if (shouldScroll) {
            navbar.classList.add("scrolled");
            hasTriggeredInitialScroll = true;
        } else if (window.scrollY < 30) {
            navbar.classList.remove("scrolled");
            navbar.classList.remove("menu-open");
            hasTriggeredInitialScroll = false;
            closeAllDropdowns();
        }
    }

    function updateForHeroScroll() {
        if (!hero) return;

        const heroHeight = parseFloat(getComputedStyle(hero).getPropertyValue("--gp-height")) || hero.clientHeight;
        const revealStart = heroHeight * 2.4 * 0.78;
        setScrolledState(hero.scrollTop >= revealStart);
    }

    // Listen to window scroll for normal page movement after the hero section
    window.addEventListener("scroll", function () {
        let currentScrollY = window.scrollY;
        let scrollDifference = currentScrollY - lastScrollY;
        
        if (currentScrollY > 50) {
            navbar.classList.add("scrolled");
            hasTriggeredInitialScroll = true;
            
            if (navbar.classList.contains("menu-open") && Math.abs(scrollDifference) > 40) {
                navbar.classList.remove("menu-open");
                closeAllDropdowns();
            }
        } else if (currentScrollY < 10 && (!hero || hero.scrollTop < 10)) {
            navbar.classList.remove("scrolled");
            navbar.classList.remove("menu-open");
            hasTriggeredInitialScroll = false;
            closeAllDropdowns();
        }
        
        lastScrollY = currentScrollY;
    });

    // Listen to the hero portal's internal scroll during the zoom sequence
    if (hero) {
        hero.addEventListener("scroll", function () {
            let heroHeight = hero.clientHeight;
            // Trigger scrolled navbar once the user scrolls past ~75% of the zoom travel length
            let zoomThreshold = heroHeight * 2.4 * 0.5; 

            if (hero.scrollTop > zoomThreshold) {
                navbar.classList.add("scrolled");
                hasTriggeredInitialScroll = true;
            } else if (hero.scrollTop < 20) {
                navbar.classList.remove("scrolled");
                navbar.classList.remove("menu-open");
                hasTriggeredInitialScroll = false;
                closeAllDropdowns();
            }
        }, { passive: true });
    }

    // Toggle menu expansion when clicking the trigger icon
    triggerBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        navbar.classList.toggle("menu-open");
        if (!navbar.classList.contains("menu-open")) {
            closeAllDropdowns();
        }
    });

    // Close menu or dropdowns when clicking outside
    document.addEventListener("click", function (event) {
        if (!navbar.contains(event.target)) {
            navbar.classList.remove("menu-open");
            closeAllDropdowns();
        }
    });
});

// Reliable Dropdown Toggle Handler
function toggleDropdown(event, dropdownId) {
    event.preventDefault();
    event.stopPropagation();
    
    const currentDropdown = document.getElementById(dropdownId);
    const isOpen = currentDropdown.classList.contains("active");

    closeAllDropdowns();

    if (!isOpen) {
        currentDropdown.classList.add("active");
    }
}

function closeAllDropdowns() {
    document.querySelectorAll(".velox-dropdown").forEach(d => {
        d.classList.remove("active");
    });
}