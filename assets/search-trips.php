<!-- Passenger Trips Search Section (Unified Card Layout) -->
<section class="velox-search-pin-container" id="passengerSearch">
    <div class="velox-search-sticky-viewport">
        <!-- The background that fades from dark to white -->
        <div class="velox-search-master-card" id="searchCardTarget">
            <div class="velox-search-wrapper-split">
                
                <!-- LEFT SIDE: Form & Header -->
                <div class="velox-search-form-container">
                    <div class="search-form-header">
                        <h3>BOOK A TRIP</h3>
                    </div>

                    <form action="trips-results.php" method="GET" class="velox-vertical-form">
                        
                        <!-- 1. Passenger Dropdown Trigger Field -->
                        <div class="velox-input-box dropdown-trigger-box" id="passengerDropdownToggle">
                            <svg class="plain-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <div class="input-content">
                                <span class="input-placeholder" id="passengerSummaryText">Passengers</span>
                            </div>
                            <svg class="plain-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>

                            <!-- Popup Stepper Menu Container -->
                            <div class="passenger-popup-menu" id="passengerPopupMenu">
                                <div class="popup-header">Select Passengers</div>
                                
                                <!-- Regular Stepper -->
                                <div class="stepper-row">
                                    <div class="stepper-info">
                                        <span class="p-type">Regular</span>
                                        <span class="p-desc">Standard fare</span>
                                    </div>
                                    <div class="stepper-controls">
                                        <button type="button" class="step-btn" data-action="decrease" data-target="p_regular">-</button>
                                        <input type="number" name="p_regular" id="p_regular" value="1" min="0" max="10" readonly class="step-input">
                                        <button type="button" class="step-btn" data-action="increase" data-target="p_regular">+</button>
                                    </div>
                                </div>

                                <!-- Student Stepper -->
                                <div class="stepper-row">
                                    <div class="stepper-info">
                                        <span class="p-type">Student</span>
                                        <span class="p-desc">With ID discount</span>
                                    </div>
                                    <div class="stepper-controls">
                                        <button type="button" class="step-btn" data-action="decrease" data-target="p_student">-</button>
                                        <input type="number" name="p_student" id="p_student" value="0" min="0" max="10" readonly class="step-input">
                                        <button type="button" class="step-btn" data-action="increase" data-target="p_student">+</button>
                                    </div>
                                </div>

                                <!-- Senior Citizen Stepper -->
                                <div class="stepper-row">
                                    <div class="stepper-info">
                                        <span class="p-type">Senior Citizen</span>
                                        <span class="p-desc">Discount privilege</span>
                                    </div>
                                    <div class="stepper-controls">
                                        <button type="button" class="step-btn" data-action="decrease" data-target="p_senior">-</button>
                                        <input type="number" name="p_senior" id="p_senior" value="0" min="0" max="10" readonly class="step-input">
                                        <button type="button" class="step-btn" data-action="increase" data-target="p_senior">+</button>
                                    </div>
                                </div>

                                <!-- PWD Stepper -->
                                <div class="stepper-row">
                                    <div class="stepper-info">
                                        <span class="p-type">PWD</span>
                                        <span class="p-desc">Disability discount</span>
                                    </div>
                                    <div class="stepper-controls">
                                        <button type="button" class="step-btn" data-action="decrease" data-target="p_pwd">-</button>
                                        <input type="number" name="p_pwd" id="p_pwd" value="0" min="0" max="10" readonly class="step-input">
                                        <button type="button" class="step-btn" data-action="increase" data-target="p_pwd">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Combined Origin & Destination Box -->
                        <div class="velox-input-box stacked-box">
                            <div class="stacked-row">
                                <svg class="plain-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7z"></path><circle cx="12" cy="9" r="2.5"></circle></svg>
                                <select name="origin" id="origin" class="velox-native-select" required>
                                    <option value="" disabled selected>Origin</option>
                                    <option value="qc-main">Quezon City Main Hub</option>
                                    <option value="manila-sampaloc">Manila (Sampaloc Terminal)</option>
                                    <option value="cubao">Cubao Terminal Exchange</option>
                                    <option value="pasay">Pasay Bus Station</option>
                                </select>
                            </div>
                            <div class="input-divider"></div>
                            <div class="stacked-row">
                                <svg class="plain-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.5V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                <select name="destination" id="destination" class="velox-native-select" required>
                                    <option value="" disabled selected>Destination</option>
                                    <option value="baguio">Baguio City Terminal</option>
                                    <option value="bicol">Bicol / Naga Hub</option>
                                    <option value="ilocos">Ilocos Norte (Laoag)</option>
                                    <option value="la-union">La Union (San Fernando)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 3. Date Picker Box -->
                        <div class="velox-input-box">
                            <svg class="plain-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <input type="date" name="departure_date" id="departure_date" class="velox-native-date" required>
                        </div>

                        <!-- 4. Search Submit Button -->
                        <button type="submit" class="velox-search-action-btn">
                            Search trips
                        </button>

                    </form>

                    <!-- Popular Routes Tucked Underneath on the Left -->
                    <div class="velox-quick-routes">
                        <span class="quick-label">Popular routes</span>
                        <button type="button" class="route-chip" data-origin="qc-main" data-dest="baguio">QC ➔ Baguio</button>
                        <button type="button" class="route-chip" data-origin="cubao" data-dest="bicol">Cubao ➔ Bicol</button>
                        <button type="button" class="route-chip" data-origin="pasay" data-dest="la-union">Pasay ➔ La Union</button>
                    </div>
                </div>

                <!-- RIGHT SIDE: Promotional Image Banner Container -->
                <div class="velox-search-image-container">
                    <div class="image-placeholder-box">
                        <span>IMAGE</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>