<?php
// actions/facebook_callback.php - Handles Facebook OAuth callback and session creation
require_once '../includes/supabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Manila');

if (!isset($_GET['code'])) {
    $_SESSION['auth_error'] = "Facebook login was cancelled or failed.";
    header("Location: ../auth.php");
    exit();
}

$appId = getenv('FACEBOOK_APP_ID');
$appSecret = getenv('FACEBOOK_APP_SECRET');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$hostName = $_SERVER['HTTP_HOST'];
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$redirectUri = "{$protocol}://{$hostName}{$scriptDir}/facebook_callback.php";

try {
    // 1. Exchange authorization code for access token
    $tokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token';
    $params = [
        'client_id'     => $appId,
        'client_secret' => $appSecret,
        'redirect_uri'  => $redirectUri,
        'code'          => $_GET['code']
    ];

    $ch = curl_init($tokenUrl . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (!isset($tokenData['access_token'])) {
        $_SESSION['auth_error'] = "Failed to authenticate token with Facebook.";
        header("Location: ../auth.php");
        exit();
    }

    // 2. Fetch user profile info from Facebook Graph API
    $userInfoUrl = 'https://graph.facebook.com/v18.0/me?fields=id,name,email&access_token=' . $tokenData['access_token'];
    $ch = curl_init($userInfoUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $userInfoResponse = curl_exec($ch);
    curl_close($ch);

    $fbUser = json_decode($userInfoResponse, true);

    // Note: Facebook users who signed up with phone numbers might not share an email
    if (!isset($fbUser['email'])) {
        $_SESSION['auth_error'] = "Could not retrieve email information from Facebook. Please ensure your Facebook account has a verified email.";
        header("Location: ../auth.php");
        exit();
    }

    $email = strtolower($fbUser['email']);
    $name = $fbUser['name'] ?? 'Facebook User';
    $facebookId = $fbUser['id'];

    // 3. Check if user already exists in Supabase
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Auto-verify if unverified, and link facebook_id if not set
        $pdo->prepare("UPDATE users SET is_verified = 1, facebook_id = COALESCE(facebook_id, ?) WHERE user_id = ?")->execute([$facebookId, $user['user_id']]);
    } else {
        // JIT Provisioning for new user
        $insertStmt = $pdo->prepare("INSERT INTO users (name, email, contact, password, role, is_verified, facebook_id, auth_provider) VALUES (?, ?, 'N/A', NULL, 'customer', 1, ?, 'facebook')");
        $insertStmt->execute([$name, $email, $facebookId]);
        
        $userId = $pdo->lastInsertId();
        
        // Seed default saved passenger row
        $passengerStmt = $pdo->prepare("INSERT INTO saved_passengers (user_id, name, contact, passenger_type) VALUES (?, ?, 'N/A', 'Regular')");
        $passengerStmt->execute([$userId, $name]);

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
    $_SESSION['auth_error'] = "Facebook Login Error: " . $e->getMessage();
    header("Location: ../auth.php");
    exit();
}
?>