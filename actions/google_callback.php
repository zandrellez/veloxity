<?php
// google_callback.php - Handles Google OAuth callback and session creation
require_once '../includes/supabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Manila');

if (!isset($_GET['code'])) {
    $_SESSION['auth_error'] = "Google login was cancelled or failed.";
    header("Location: ../auth.php");
    exit();
}

$clientId = getenv('GOOGLE_CLIENT_ID');
$clientSecret = getenv('GOOGLE_CLIENT_SECRET');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$hostName = $_SERVER['HTTP_HOST'];
$projectFolder = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
$redirectUri = "{$protocol}://{$hostName}{$projectFolder}/actions/google_callback.php";

try {
    // 1. Exchange authorization code for access token
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'code'          => $_GET['code'],
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (!isset($tokenData['access_token'])) {
        $_SESSION['auth_error'] = "Failed to authenticate token with Google.";
        header("Location: ../auth.php");
        exit();
    }

    // 2. Fetch user profile info from Google API
    $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenData['access_token'];
    $ch = curl_init($userInfoUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $userInfoResponse = curl_exec($ch);
    curl_close($ch);

    $googleUser = json_decode($userInfoResponse, true);

    if (!isset($googleUser['email'])) {
        $_SESSION['auth_error'] = "Could not retrieve email information from Google.";
        header("Location: ../auth.php");
        exit();
    }

    $email = strtolower($googleUser['email']);
    $name = $googleUser['name'] ?? 'Google User';

    // 3. Check if user already exists in Supabase
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // If user exists but was unverified via standard sign up, auto-verify them since Google verified their email
        if ($user['is_verified'] == 0) {
            $pdo->prepare("UPDATE users SET is_verified = 1 WHERE user_id = ?")->execute([$user['user_id']]);
        }
    } else {
        // User doesn't exist yet, automatically register them as verified
        $insertStmt = $pdo->prepare("INSERT INTO users (name, email, contact, password, role, is_verified) VALUES (?, ?, 'N/A', NULL, 'customer', 1)");
        $insertStmt->execute([$name, $email]);
        
        $userId = $pdo->lastInsertId();
        
        // Seed default saved passenger row matching your standard registration flow
        $passengerStmt = $pdo->prepare("INSERT INTO saved_passengers (user_id, name, contact, passenger_type) VALUES (?, ?, 'N/A', 'Regular')");
        $passengerStmt->execute([$userId, $name]);

        // Fetch newly created user record
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    }

    // 4. Establish Session Variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    // 5. Role-based Redirection
    switch ($user['role']) {
        case 'admin':
            header("Location: ../admin/dashboard.php");
            exit();
        case 'operator':
            header("Location: ../operator/dashboard.php");
            exit();
        case 'customer':
        default:
            header("Location: ../customer/dashboard.php");
            exit();
    }

} catch (Exception $e) {
    $_SESSION['auth_error'] = "Google Login Error: " . $e->getMessage();
    header("Location: ../auth.php");
    exit();
}
?>