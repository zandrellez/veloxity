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

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/hero.css">
    <link rel="stylesheet" href="assets/css/search.css">
</head>
<body style="overflow: auto; display: block;"> 

    <!-- Include the Reusable Navbar -->
    <?php include 'assets/navbar.php'; ?>

    <!-- Include the Hero Scroll-Zoom Component -->
    <?php include 'assets/hero.php'; ?>

    
    <!-- Include the Search Trip Component -->
    <?php include 'assets/search-trips.php'; ?>

    <!-- Include Navbar Interactive Script -->
    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/hero.js"></script>
    <script src="assets/js/search.js"></script>
</body>
</html>