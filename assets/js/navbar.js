document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.getElementById("veloxNavbar");
    const triggerBtn = document.getElementById("veloxMenuTriggerBtn");

    let lastScrollY = window.scrollY;
    let hasTriggeredInitialScroll = false;

    window.addEventListener("scroll", function () {
        let currentScrollY = window.scrollY;
        let scrollDifference = currentScrollY - lastScrollY;
        
        // Dynamically grab full screen height for the initial trigger threshold
        let initialThreshold = window.innerHeight; 
        let subsequentThreshold = 80; // Lower threshold after the first full screen scroll

        let activeThreshold = hasTriggeredInitialScroll ? subsequentThreshold : initialThreshold;

        if (currentScrollY > activeThreshold) {
            navbar.classList.add("scrolled");
            hasTriggeredInitialScroll = true;
            
            // If menu is open and user scrolls significantly down/up (> 40px), close the menu
            if (navbar.classList.contains("menu-open") && Math.abs(scrollDifference) > 40) {
                navbar.classList.remove("menu-open");
                closeAllDropdowns();
            }
        } else if (currentScrollY < 30) {
            // Reset back to full navbar when scrolled back up near the very top
            navbar.classList.remove("scrolled");
            navbar.classList.remove("menu-open");
            hasTriggeredInitialScroll = false;
            closeAllDropdowns();
        }
        
        lastScrollY = currentScrollY;
    });

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