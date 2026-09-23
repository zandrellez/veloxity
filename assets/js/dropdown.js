const dropdownSelectors = [
    ".passenger-popup-menu",
    ".velox-custom-dropdown",
    ".velox-calendar-dropdown-modal"
];

window.veloxCloseDropdowns = function (except) {
    document.querySelectorAll(dropdownSelectors.join(", ")).forEach((menu) => {
        if (menu !== except) menu.classList.remove("active");
    });
    const passengerToggle = document.getElementById("passengerDropdownToggle");
    if (passengerToggle && except !== document.getElementById("passengerPopupMenu")) {
        passengerToggle.classList.remove("open");
    }
};

const dropdownToggle = document.getElementById("passengerDropdownToggle");
const popupMenu = document.getElementById("passengerPopupMenu");
const summaryText = document.getElementById("passengerSummaryText");

if (dropdownToggle && popupMenu) {
    dropdownToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        const shouldOpen = !popupMenu.classList.contains("active");
        window.veloxCloseDropdowns(popupMenu);
        popupMenu.classList.toggle("active", shouldOpen);
        dropdownToggle.classList.toggle("open", shouldOpen);
    });

    popupMenu.addEventListener("click", (e) => e.stopPropagation());
}

document.addEventListener("click", () => {
    window.veloxCloseDropdowns();
});

document.querySelectorAll(".step-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
        const target = document.getElementById(btn.getAttribute("data-target"));
        if (!target) return;

        const action = btn.getAttribute("data-action");
        const value = parseInt(target.value, 10) || 0;
        if (action === "increase" && value < 10) target.value = value + 1;
        if (action === "decrease" && value > 0) target.value = value - 1;

        const regular = parseInt(document.getElementById("p_regular").value, 10) || 0;
        const senior = parseInt(document.getElementById("p_senior").value, 10) || 0;
        if (summaryText) summaryText.textContent = `${regular + senior} Passenger(s)`;
    });
});