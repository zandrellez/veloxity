<?php
$isLoggedIn = isset($_SESSION['user_id']); 
?>

<header class="velox-navbar-wrapper" id="veloxNavbar">
    <div class="velox-nav-container">
        <!-- Standalone Logo Image on Left -->
        <a href="index.php">
            <img src="assets/images/veloxity-logo.svg" alt="Veloxity Logo" class="velox-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <span style="display:none; font-weight:800; font-size:1.25rem; color:var(--text-main);">Veloxity</span>
        </a>

        <!-- Floating Pill Navbar (Right Side) -->
        <nav class="velox-nav-pill" id="veloxNavPill">
            <button class="menu-icon-trigger" id="veloxMenuTriggerBtn" aria-label="Open Menu">⌘</button>

            <!-- Primary Navigation Links -->
            <ul class="velox-nav-links">
                <li><a href="trips.php">Trips</a></li>
                <li><a href="cargo.php">Cargo</a></li>
                <li><a href="terminals.php">Terminals</a></li>
                
                <!-- Support Dropdown -->
                <li class="velox-dropdown" id="supportDropdown">
                    <button type="button" class="dropdown-toggle" onclick="toggleDropdown(event, 'supportDropdown')">
                        Support 
                        <svg class="nav-chevron" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="faqs.php">FAQs</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Dynamic Auth Section -->
            <div class="velox-auth-section">
                <?php if ($isLoggedIn): ?>
                    <div class="velox-dropdown" id="profileDropdown">
                        <button type="button" class="profile-toggle" onclick="toggleDropdown(event, 'profileDropdown')">
                            <span>👤 Account</span> 
                            <svg class="nav-chevron" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="bookings.php">Bookings</a></li>
                            <li><a href="shipments.php">Shipments</a></li>
                            <li><a href="settings.php">Settings</a></li>
                            <li><a href="logout.php" style="color: var(--velox-red);">Log Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="auth.php" class="velox-nav-login-btn">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>