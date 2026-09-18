<?php
session_start(); // Start session for auth handling
// require_once 'includes/supabase.php'; // Uncomment when ready
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloxity Terminal & Logistics Portal</title>
    <!-- Link your design system globals.css -->
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
</head>
<body style="overflow: auto; display: block;"> <!-- Adjusted body styling for scrolling content test -->

    <!-- Include the Reusable Navbar -->
    <?php include 'assets/navbar.php'; ?>

    <!-- Page Content Section -->
    <main style="padding-top: 120px; max-width: 1280px; margin: 0 auto; padding-left: 2rem; padding-right: 2rem;">
        <?php
        echo "<h1>Welcome to Veloxity Terminal & Logistics Portal</h1>";
        echo "<p style='color: var(--text-muted); margin-top: 10px;'>Scroll down to see the navbar smoothly pack away into the sticky menu trigger button!</p>";
        
        // Dummy content spacer to let you test scrolling behavior
        for ($i = 1; $i <= 10; $i++) {
            echo "<div style='height: 300px; margin-top: 20px; background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #CBD5E1;'>Content Section $i</div>";
        }
        ?>
    </main>

    <!-- Include Navbar Interactive Script -->
    <script src="assets/js/navbar.js"></script>
</body>
</html>