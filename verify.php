<?php
// verify.php - Handles account email verification
require_once 'includes/supabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['auth_error'] = "Invalid verification link.";
    header("Location: auth.php");
    exit();
}

try {
    // Look for user with this token and check expiration
    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = ? AND token_expires_at > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['auth_error'] = "Verification link is invalid or has expired.";
        header("Location: auth.php");
        exit();
    }

    // Update user status to verified and clear token fields
    $updateStmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, token_expires_at = NULL WHERE user_id = ?");
    $updateStmt->execute([$user['user_id']]);

    // Automatically log the user in
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    // Redirect to customer dashboard
    header("Location: customer/dashboard.php?verified=success");
    exit();

} catch (PDOException $e) {
    die("Verification Error: " . $e->getMessage());
}
?>