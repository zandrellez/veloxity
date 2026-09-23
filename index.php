<?php
session_start(); // Start session for auth handling
// require_once 'includes/supabase.php'; // Uncomment when ready

$popular_routes = [
    ['from' => 'Manila',  'to' => 'Baguio'],
    ['from' => 'Cebu',    'to' => 'Bohol'],
    ['from' => 'Davao',   'to' => 'Cagayan de Oro'],
];
 
$origin_suggestions = ['Manila', 'Quezon City', 'Cebu City', 'Davao City'];
$destination_suggestions = ['Baguio', 'Bohol', 'Cagayan de Oro', 'Iloilo City'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloxity Terminal & Logistics Portal</title>

    <!-- Google Fonts: Syne (Display) & Inter (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/hero.css">
    <link rel="stylesheet" href="assets/css/search.css">
    <link rel="stylesheet" href="assets/css/cargo.css">
    <link rel="stylesheet" href="assets/css/ecosystem.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body style="overflow: auto; display: block;"> 

    <!-- Include the Reusable Navbar -->
    <?php include 'assets/navbar.php'; ?>

    <!-- Include the different sections -->
    <?php include 'assets/hero.php'; ?>
    <?php include 'assets/search-trips.php'; ?>
    <?php include 'assets/cargo-strip.php'; ?>
    <?php include 'assets/ecosystem-grid.php'; ?>
    <?php include 'assets/footer.php'; ?>

    <!-- Include scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/animations.js"></script>
    <script src="assets/js/hero.js"></script>
    <script src="assets/js/search.js"></script>
    <script src="assets/js/cargo.js"></script>
    <script src="assets/js/suggestions.js"></script>
    <script src="assets/js/dropdown.js"></script>
    <script src="assets/js/datepicker.js"></script>
</body>
</html>