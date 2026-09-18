document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.getElementById("veloxNavbar");
    const triggerBtn = document.getElementById("veloxMenuTriggerBtn");

    let lastScrollY = window.scrollY;

    // Handle scroll events
    window.addEventListener("scroll", function () {
        let currentScrollY = window.scrollY;
        let scrollDifference = currentScrollY - lastScrollY;

        if (currentScrollY > 100) {
            navbar.classList.add("scrolled");
            
            // If menu is open and user scrolls significantly (> 40px), close the menu
            if (navbar.classList.contains("menu-open") && Math.abs(scrollDifference) > 40) {
                navbar.classList.remove("menu-open");
                closeAllDropdowns();
            }
        } else {
            navbar.classList.remove("scrolled");
            navbar.classList.remove("menu-open");
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

    // Close all dropdowns first
    closeAllDropdowns();

    // If it wasn't open, open it now
    if (!isOpen) {
        currentDropdown.classList.add("active");
    }
}

function closeAllDropdowns() {
    document.querySelectorAll(".velox-dropdown").forEach(d => {
        d.classList.remove("active");
    });
}