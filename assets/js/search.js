document.addEventListener("DOMContentLoaded", function () {
});

// Custom Dropdown Logic for Origin & Destination
    const customSelects = document.querySelectorAll('.custom-select-trigger');
    
    customSelects.forEach(trigger => {
        const dropdown = trigger.querySelector('.velox-custom-dropdown');
        const displayText = trigger.querySelector('.select-display-text');
        const hiddenInput = trigger.querySelector('.select-hidden-input');
        const options = trigger.querySelectorAll('.custom-option');

        // Toggle dropdown on click
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // Close any other open dropdowns first
            document.querySelectorAll('.velox-custom-dropdown.active').forEach(menu => {
                if (menu !== dropdown) menu.classList.remove('active');
            });
            
            dropdown.classList.toggle('active');
        });

        // Handle selecting an option
        options.forEach(option => {
            option.addEventListener('click', (e) => {
                e.stopPropagation(); // Stop the trigger click from firing again
                
                // Update text and hidden input
                displayText.textContent = option.textContent;
                hiddenInput.value = option.getAttribute('data-value');
                
                // Close the dropdown
                dropdown.classList.remove('active');
            });
        });
    });

    // Close all custom dropdowns when clicking anywhere outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.velox-custom-dropdown').forEach(menu => {
            menu.classList.remove('active');
        });
    });

    // --- 3. Custom Date Picker (Flatpickr) ---
    const dateInput = document.getElementById("departure_date");
    if (dateInput) {
        flatpickr(dateInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            disableMobile: true, // Forces the custom UI on mobile devices instead of the native dial
            position: "auto center"
        });
    }