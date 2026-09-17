<?php
// resend_verification.php - Handles resending email verification links
require_once 'includes/supabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = trim($_GET['email'] ?? $_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['auth_error'] = "Invalid email address provided.";
    header("Location: auth.php");
    exit();
}

try {
    // 1. Check if user exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Generic response for security (prevents user enumeration)
        $_SESSION['auth_error'] = "If that email exists, a new verification link has been sent.";
        header("Location: auth.php");
        exit();
    }

    // 2. Check if already verified
    if ($user['is_verified'] == 1) {
        $_SESSION['auth_error'] = "This account is already verified. Please sign in below.";
        header("Location: auth.php");
        exit();
    }

    // 3. Generate a brand new token and new 24-hour expiration
    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $updateStmt = $pdo->prepare("UPDATE users SET verification_token = ?, token_expires_at = ? WHERE user_id = ?");
    $updateStmt->execute([$token, $expiresAt, $user['user_id']]);

    // 4. Send Email via PHPMailer
    require 'includes/PHPMailer/PHPMailer.php';
    require 'includes/PHPMailer/SMTP.php';
    require 'includes/PHPMailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $hostName = $_SERVER['HTTP_HOST'];
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $hostName = $_SERVER['HTTP_HOST'];
    // Dynamically gets the folder path (e.g., '/veloxity' locally, or empty/root on Render)
    $projectFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $verificationLink = "{$protocol}://{$hostName}{$projectFolder}/verify.php?token={$token}";

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_EMAIL');
    $mail->Password   = getenv('SMTP_PASS');
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom(getenv('SMTP_EMAIL'), 'Veloxity System');
    $mail->addAddress($user['email'], $user['name']);
    $mail->isHTML(true);
    $mail->Subject = 'New Verification Link - Veloxity';
    $mail->Body    = "<h3>Hello {$user['name']},</h3>
                      <p>You requested a new verification link. Please click below to verify your email address:</p>
                      <p><a href='{$verificationLink}' style='background:#f97316;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Verify Account</a></p>
                      <p>This link will expire in 24 hours.</p>";

    $mail->send();

    $_SESSION['auth_error'] = "A new verification link has been sent to your email. Please check your inbox.";
    header("Location: auth.php");
    exit();

} catch (Exception $e) {
    $_SESSION['auth_error'] = "Failed to send email. Please try again later.";
    header("Location: auth.php");
    exit();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>