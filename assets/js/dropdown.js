const dropdownToggle = document.getElementById("passengerDropdownToggle");
        const popupMenu = document.getElementById("passengerPopupMenu");
        const summaryText = document.getElementById("passengerSummaryText");

        dropdownToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            popupMenu.classList.toggle("active");
        });

        document.addEventListener("click", () => {
            popupMenu.classList.remove("active");
        });

        popupMenu.addEventListener("click", (e) => e.stopPropagation());

        document.querySelectorAll(".step-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                const action = btn.getAttribute("data-action");
                const target = document.getElementById(btn.getAttribute("data-target"));
                let val = parseInt(target.value);
                if(action === "increase" && val < 10) target.value = val + 1;
                if(action === "decrease" && val > 0) target.value = val - 1;
                
                const reg = parseInt(document.getElementById("p_regular").value);
                const sen = parseInt(document.getElementById("p_senior").value);
                summaryText.textContent = `${reg + sen} Passenger(s)`;
            });
        });