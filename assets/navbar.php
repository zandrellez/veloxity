<?php
$isLoggedIn = isset($_SESSION['user_id']); 
?>

<header class="velox-navbar-wrapper" id="veloxNavbar">
    <div class="velox-nav-container">
        <!-- Logo Stays Fixed on the Left -->
        <a href="index.php" class="velox-logo">Veloxity</a>

        <!-- Floating Pill Navbar (Right Side) -->
        <nav class="velox-nav-pill" id="veloxNavPill">
            <!-- Menu Trigger Icon (Visible only when scrolled & collapsed) -->
            <button class="menu-icon-trigger" id="veloxMenuTriggerBtn" aria-label="Open Menu">⌘</button>

            <!-- Primary Navigation Links -->
            <ul class="velox-nav-links">
                <li><a href="trips.php">Trips</a></li>
                <li><a href="cargo.php">Cargo</a></li>
                <li><a href="terminals.php">Terminals</a></li>
                
                <!-- Support Dropdown -->
                <li class="velox-dropdown" id="supportDropdown">
                    <button type="button" class="dropdown-toggle" onclick="toggleDropdown(event, 'supportDropdown')">Support ▾</button>
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
                        <button type="button" class="profile-toggle" onclick="toggleDropdown(event, 'profileDropdown')">👤 Account ▾</button>
                        <ul class="dropdown-menu">
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="bookings.php">Bookings</a></li>
                            <li><a href="shipments.php">Shipments</a></li>
                            <li><a href="settings.php">Settings</a></li>
                            <li><a href="logout.php" style="color: var(--velox-red);">Log Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="auth.php" class="velox-nav-login-btn">Get Started</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>