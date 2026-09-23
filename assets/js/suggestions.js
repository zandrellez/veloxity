document.addEventListener('DOMContentLoaded', function () {
  /* ---------------------------------------------------------------
     ORIGIN / DESTINATION suggestion dropdowns + swap + popular routes.
     (Passenger stepper and date picker are handled by dropdown.js /
     datepicker.js, loaded below.)
  --------------------------------------------------------------- */
  var originInput = document.getElementById('originInput');
  var destinationInput = document.getElementById('destinationInput');
  var originDropdown = document.getElementById('originDropdown');
  var destinationDropdown = document.getElementById('destinationDropdown');

  function closeLocationDropdowns(except) {
    if (window.veloxCloseDropdowns) window.veloxCloseDropdowns(except);
    [originDropdown, destinationDropdown].forEach(function (dd) {
      if (dd !== except) dd.classList.remove('active');
    });
  }

  document.addEventListener('click', function () {
    closeLocationDropdowns(null);
  });

  function wireLocationField(input, dropdown) {
    input.addEventListener('focus', function (e) {
      e.stopPropagation();
      closeLocationDropdowns(dropdown);
      dropdown.classList.add('active');
    });
    input.addEventListener('click', function (e) { e.stopPropagation(); });
    dropdown.addEventListener('click', function (e) { e.stopPropagation(); });
    dropdown.querySelectorAll('.custom-option').forEach(function (opt) {
      opt.addEventListener('click', function () {
        input.value = opt.getAttribute('data-value');
        dropdown.classList.remove('active');
      });
    });
  }

  wireLocationField(originInput, originDropdown);
  wireLocationField(destinationInput, destinationDropdown);

  document.getElementById('swapLocations').addEventListener('click', function (e) {
    e.stopPropagation();
    var tmp = originInput.value;
    originInput.value = destinationInput.value;
    destinationInput.value = tmp;
  });

  document.querySelectorAll('.route-chip').forEach(function (chip) {
    chip.addEventListener('click', function () {
      originInput.value = chip.getAttribute('data-origin');
      destinationInput.value = chip.getAttribute('data-destination');
    });
  });
});