document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("departure_date");
    const dropdownToggle = document.getElementById("passengerDropdownToggle");
    const popupMenu = document.getElementById("passengerPopupMenu");
    const summaryText = document.getElementById("passengerSummaryText");
    const stepButtons = document.querySelectorAll(".step-btn");

    // Auto-fill today's date as default
    if (dateInput && !dateInput.value) {
        const today = new Date().toISOString().split("T")[0];
        dateInput.value = today;
        dateInput.min = today;
    }

    // Toggle Passenger Popup Menu on Click
    if (dropdownToggle && popupMenu) {
        dropdownToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            popupMenu.classList.toggle("active");
        });

        // Prevent clicks inside the menu from closing it
        popupMenu.addEventListener("click", function (e) {
            e.stopPropagation();
        });

        // Close menu when clicking anywhere else on the page
        document.addEventListener("click", function () {
            popupMenu.classList.remove("active");
        });
    }

    // Stepper logic & dynamic passenger summary update
    stepButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            const action = this.getAttribute("data-action");
            const targetId = this.getAttribute("data-target");
            const inputField = document.getElementById(targetId);

            if (inputField) {
                let currentValue = parseInt(inputField.value) || 0;
                
                if (action === "increase" && currentValue < 10) {
                    inputField.value = currentValue + 1;
                } else if (action === "decrease" && currentValue > 0) {
                    inputField.value = currentValue - 1;
                }

                updatePassengerSummary();
            }
        });
    });

    function updatePassengerSummary() {
        const reg = parseInt(document.getElementById("p_regular").value) || 0;
        const stu = parseInt(document.getElementById("p_student").value) || 0;
        const sen = parseInt(document.getElementById("p_senior").value) || 0;
        const pwd = parseInt(document.getElementById("p_pwd").value) || 0;

        const total = reg + stu + sen + pwd;
        if (summaryText) {
            summaryText.textContent = total === 1 ? "1 Passenger" : `${total} Passengers`;
        }
    }
});