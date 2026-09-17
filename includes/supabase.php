<?php
// includes/supabase.php - Connected to cloud PostgreSQL (Supabase)

/**
 * Lightweight custom function to load a .env file without Composer
 */
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comment lines
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split into variable name and value
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim(trim($value), '"\''); // Strip surrounding quotes if any

        // Set environment variables if not already defined
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load the .env file from the root directory (assuming .env is one level above 'includes/')
loadEnv(__DIR__ . '/../.env');

// Fetch variables using getenv() with safety checks
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');

// --- TEMPORARY DEBUG OUTPUT ---
echo "<div style='background: #111; color: #00ff00; padding: 15px; font-family: monospace; border-bottom: 3px solid red;'>";
echo "<h3>[RENDER DEBUG CHECK]</h3>";
echo "DB_HOST: " . ($host ? htmlspecialchars($host) : "<span style='color:red;'>EMPTY / NULL</span>") . "<br>";
echo "DB_PORT: " . ($port ? htmlspecialchars($port) : "<span style='color:red;'>EMPTY / NULL</span>") . "<br>";
echo "DB_NAME: " . ($dbname ? htmlspecialchars($dbname) : "<span style='color:red;'>EMPTY / NULL</span>") . "<br>";
echo "DB_USER: " . ($user ? htmlspecialchars($user) : "<span style='color:red;'>EMPTY / NULL</span>") . "<br>";
echo "DB_PASS Length: " . (!empty($password) ? strlen($password) . " characters loaded" : "<span style='color:red;'>EMPTY / NULL</span>") . "<br>";
echo "</div>";
// ------------------------------

try {
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";
    
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>