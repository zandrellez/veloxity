document.addEventListener("DOMContentLoaded", () => {
    const trigger = document.getElementById("veloxDateTrigger");
    const modal = document.getElementById("veloxCalendarModal");
    const display = document.getElementById("veloxDateValueDisplay");
    const hiddenInput = document.getElementById("veloxHiddenDateInput");
    const titleToggle = document.getElementById("calTitleToggle");
    const grid = document.getElementById("calBodyGrid");
    const prevBtn = document.getElementById("calPrevBtn");
    const nextBtn = document.getElementById("calNextBtn");

    let currentDate = new Date(); // Default starts at today
    let selectedDate = new Date(); // Default selected is today
    let viewMode = "days"; // "days" | "months" | "years"
    let yearsViewStart = 2020;

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const shortMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    // Set initial default value to Today
    updateInputDisplay(selectedDate);

    // Toggle Modal
    trigger.addEventListener("click", (e) => {
        e.stopPropagation();
        const shouldOpen = !modal.classList.contains("active");
        if (window.veloxCloseDropdowns) window.veloxCloseDropdowns(modal);
        modal.classList.toggle("active", shouldOpen);
        renderCalendar();
    });

    modal.addEventListener("click", (e) => e.stopPropagation());

    document.addEventListener("click", () => {
        if (window.veloxCloseDropdowns) {
            window.veloxCloseDropdowns();
        } else {
            modal.classList.remove("active");
        }
        viewMode = "days"; // reset view on close
    });

    // Title Click cycles through views: Days -> Months -> Years
    titleToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        if (viewMode === "days") {
            viewMode = "months";
        } else if (viewMode === "months") {
            viewMode = "years";
            yearsViewStart = Math.floor(currentDate.getFullYear() / 12) * 12;
        } else {
            viewMode = "days";
        }
        renderCalendar();
    });

    // Navigation Arrows
    prevBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        if (viewMode === "days") {
            currentDate.setMonth(currentDate.getMonth() - 1);
        } else if (viewMode === "months") {
            currentDate.setFullYear(currentDate.getFullYear() - 1);
        } else {
            yearsViewStart -= 12;
        }
        renderCalendar();
    });

    nextBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        if (viewMode === "days") {
            currentDate.setMonth(currentDate.getMonth() + 1);
        } else if (viewMode === "months") {
            currentDate.setFullYear(currentDate.getFullYear() + 1);
        } else {
            yearsViewStart += 12;
        }
        renderCalendar();
    });

    function renderCalendar() {
        grid.innerHTML = "";
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Normalize today's date to midnight for accurate comparison
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (viewMode === "days") {
            grid.className = "cal-body days-view";
            titleToggle.textContent = `${monthNames[month]} ${year}`;
            prevBtn.style.visibility = "visible";
            nextBtn.style.visibility = "visible";

            // Render Weekday headers
            ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'].forEach(day => {
                const wh = document.createElement("div");
                wh.className = "cal-weekday";
                wh.textContent = day;
                grid.appendChild(wh);
            });

            const firstDayIndex = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();
            const prevTotalDays = new Date(year, month, 0).getDate();

            // Previous month padding days
            for (let i = firstDayIndex; i > 0; i--) {
                const cell = document.createElement("div");
                cell.className = "cal-cell disabled";
                cell.textContent = prevTotalDays - i + 1;
                grid.appendChild(cell);
            }

            // Current month days
            for (let i = 1; i <= totalDays; i++) {
                const cell = document.createElement("div");
                cell.className = "cal-cell";
                cell.textContent = i;

                const thisDate = new Date(year, month, i);
                thisDate.setHours(0, 0, 0, 0);

                if (thisDate.getTime() === today.getTime()) {
                    cell.classList.add("today");
                }

                if (thisDate.getTime() === selectedDate.getTime()) {
                    cell.classList.add("selected");
                }

                // Disable past dates
                if (thisDate < today) {
                    cell.classList.add("disabled");
                } else {
                    cell.addEventListener("click", () => {
                        selectedDate = new Date(year, month, i);
                        updateInputDisplay(selectedDate);
                        modal.classList.remove("active");
                    });
                }
                grid.appendChild(cell);
            }

        } else if (viewMode === "months") {
            grid.className = "cal-body months-view";
            titleToggle.textContent = year;
            prevBtn.style.visibility = "visible";
            nextBtn.style.visibility = "visible";

            shortMonths.forEach((mName, index) => {
                const cell = document.createElement("div");
                cell.className = "cal-cell";
                cell.textContent = mName;
                if (index === month && year === selectedDate.getFullYear()) cell.classList.add("selected");

                cell.addEventListener("click", () => {
                    currentDate.setMonth(index);
                    viewMode = "days";
                    renderCalendar();
                });
                grid.appendChild(cell);
            });

        } else if (viewMode === "years") {
            grid.className = "cal-body years-view";
            titleToggle.textContent = `${yearsViewStart} - ${yearsViewStart + 11}`;
            prevBtn.style.visibility = "visible";
            nextBtn.style.visibility = "visible";

            for (let y = yearsViewStart; y < yearsViewStart + 12; y++) {
                const cell = document.createElement("div");
                cell.className = "cal-cell";
                cell.textContent = y;
                if (y === selectedDate.getFullYear()) cell.classList.add("selected");

                cell.addEventListener("click", () => {
                    currentDate.setFullYear(y);
                    viewMode = "months";
                    renderCalendar();
                });
                grid.appendChild(cell);
            }
        }
    }

    function updateInputDisplay(dateObj) {
        const yyyy = dateObj.getFullYear();
        const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
        const dd = String(dateObj.getDate()).padStart(2, '0');
        const formatted = `${yyyy}-${mm}-${dd}`;

        display.textContent = `${monthNames[dateObj.getMonth()]} ${dateObj.getDate()}, ${yyyy}`;
        display.style.color = "var(--text-main)";
        hiddenInput.value = formatted;
    }
});