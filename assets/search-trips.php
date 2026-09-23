<!-- Passenger Trips Search Section (Unified Card Layout) -->
<section class="velox-search-pin-container" id="passengerSearch">
    <div class="velox-search-sticky-viewport">
        <!-- The background that fades from dark to white -->
        <div class="velox-search-master-card" id="searchCardTarget">
            <form class="search-trip-card velox-card" method="get" action="results.php" id="searchTripForm">
            
                <div class="search-trip-form">
                <span class="search-eyebrow">Book a trip</span>
            
                <!-- ============ PASSENGERS (wired by dropdown.js) ============ -->
                <div class="showcase-group" style="position: relative;">
                    <div class="velox-input-box passenger-trigger" id="passengerDropdownToggle" tabindex="0">
                    <span class="trigger-label">
                        <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4 4-6 8-6s8 2 8 6"></path></svg>
                        <span id="passengerSummaryText">1 Passenger(s)</span>
                    </span>
                    <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
            
                    <div class="passenger-popup-menu" id="passengerPopupMenu">
                    <div class="popup-header">Passengers</div>
            
                    <div class="stepper-row">
                        <div class="stepper-info">
                        <span class="p-type">Regular</span>
                        <span class="p-desc">Age 18-59</span>
                        </div>
                        <div class="stepper-controls">
                        <button type="button" class="step-btn" data-action="decrease" data-target="p_regular">&minus;</button>
                        <input type="text" class="step-input" id="p_regular" name="regular_passengers" value="1" readonly>
                        <button type="button" class="step-btn" data-action="increase" data-target="p_regular">+</button>
                        </div>
                    </div>
            
                    <div class="stepper-row">
                        <div class="stepper-info">
                        <span class="p-type">Senior / PWD</span>
                        <span class="p-desc">Discounted fare</span>
                        </div>
                        <div class="stepper-controls">
                        <button type="button" class="step-btn" data-action="decrease" data-target="p_senior">&minus;</button>
                        <input type="text" class="step-input" id="p_senior" name="senior_passengers" value="0" readonly>
                        <button type="button" class="step-btn" data-action="increase" data-target="p_senior">+</button>
                        </div>
                    </div>
                    </div>
                </div>
            
                <!-- ============ ORIGIN / DESTINATION ============ -->
                <div class="showcase-group location-box">
                    <div class="velox-input-box stacked-box">
            
                    <div class="stacked-row" id="originRow">
                        <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <input type="text" name="origin" id="originInput" placeholder="Origin" autocomplete="off">
            
                        <div class="velox-custom-dropdown" id="originDropdown">
                        <?php foreach ($origin_suggestions as $place): ?>
                        <div class="custom-option" data-value="<?= htmlspecialchars($place) ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <?= htmlspecialchars($place) ?>
                        </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
            
                    <div class="input-divider"></div>
            
                    <div class="stacked-row" id="destinationRow">
                        <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        <input type="text" name="destination" id="destinationInput" placeholder="Destination" autocomplete="off">
            
                        <div class="velox-custom-dropdown" id="destinationDropdown">
                        <?php foreach ($destination_suggestions as $place): ?>
                        <div class="custom-option" data-value="<?= htmlspecialchars($place) ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            <?= htmlspecialchars($place) ?>
                        </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
            
                    <button type="button" class="swap-btn" id="swapLocations" title="Swap origin and destination" aria-label="Swap origin and destination">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                    </button>
                    </div>
                </div>
            
                <!-- ============ DEPARTURE DATE (wired by datepicker.js) ============ -->
                <div class="showcase-group" style="position: relative;">
                    <div class="velox-input-box date-trigger" id="veloxDateTrigger" tabindex="0">
                    <span class="trigger-label">
                        <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span id="veloxDateValueDisplay">Select departure date</span>
                    </span>
                    </div>
                    <input type="hidden" name="departure_date" id="veloxHiddenDateInput">
            
                    <div class="velox-calendar-dropdown-modal" id="veloxCalendarModal">
                    <div class="cal-header">
                        <button type="button" class="cal-nav-btn" id="calPrevBtn">&lsaquo;</button>
                        <button type="button" class="cal-title-toggle" id="calTitleToggle"></button>
                        <button type="button" class="cal-nav-btn" id="calNextBtn">&rsaquo;</button>
                    </div>
                    <div class="cal-body" id="calBodyGrid"></div>
                    </div>
                </div>
            
                <!-- ============ SUBMIT ============ -->
                <button type="submit" class="btn-primary search-submit-btn">Search trips</button>
            
                <!-- ============ POPULAR ROUTES ============ -->
                <div class="popular-routes">
                    <span class="popular-routes-label">Popular routes</span>
                    <?php foreach ($popular_routes as $route): ?>
                    <button type="button" class="route-chip"
                            data-origin="<?= htmlspecialchars($route['from']) ?>"
                            data-destination="<?= htmlspecialchars($route['to']) ?>">
                    <?= htmlspecialchars($route['from']) ?> &rarr; <?= htmlspecialchars($route['to']) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                </div>
            
                <!-- ============ SHOWCASE IMAGE ============ -->
                <div class="search-trip-visual">
                <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1000&q=80" alt="Bus travelling on an open highway at golden hour">
                <div class="visual-caption">
                    <strong>Go anywhere, easily.</strong>
                    <span>Compare routes and book your next trip in minutes.</span>
                </div>
                </div>
            
            </form>
        </div>
    </div>
</section>