<?php
// Database configuration constants
$host = 'localhost';
$dbname = 'veloxity';
$username = 'root'; // Default XAMPP MySQL username
$password = '';     // Default XAMPP MySQL password is blank

try {
    // Create a new PDO instance with UTF-8 encoding and error mode set to exceptions
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    // Set PDO error mode to exception so we can catch any database errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: Set default fetch mode to associative array for cleaner data handling
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Uncomment the line below temporarily to test your connection success message
    // echo "Database connected successfully!";

} catch (PDOException $e) {
    // Stop execution and display a secure error message if connection fails
    die("Database Connection Failed: " . $e.getMessage());
}
?>