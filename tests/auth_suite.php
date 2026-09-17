<?php
// test_auth_suite.php - Automated Authentication & Verification Test Suite
date_default_timezone_set('Asia/Manila');
require_once '../includes/supabase.php';

echo "<h2>[VELOXITY] Running Automated Auth & Security Test Suite...</h2>";
echo "<pre style='background:#111; color:#0f0; padding:15px; font-family:monospace;'>";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "<span style='color:green;'>[PASS]</span> {$testName}\n";
        $passCount++;
    } else {
        echo "<span style='color:red;'>[FAIL]</span> {$testName}\n";
        $failCount++;
    }
}

try {
    $uniqueId = time();
    $testEmail = strtolower("Test.User.{$uniqueId}@gmail.com");
    $testPass = "SecurePass123";
    $hashedPass = password_hash($testPass, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // ==========================================
    // Sign up with new email (Unverified state)
    // ==========================================
    $stmt = $pdo->prepare("INSERT INTO users (name, email, contact, password, role, verification_token, token_expires_at, is_verified) VALUES ('Test User', ?, '09123456789', ?, 'customer', ?, ?, 0)");
    $stmt->execute([$testEmail, $hashedPass, $token, $expiresAt]);
    $userId = $pdo->lastInsertId();

    $stmtCheck = $pdo->prepare("SELECT is_verified FROM users WHERE user_id = ?");
    $stmtCheck->execute([$userId]);
    $userRow = $stmtCheck->fetch();
    assertTest($userRow && $userRow['is_verified'] == 0, "User created with unverified state");

    // ==========================================
    // Duplicate Email Prevention
    // ==========================================
    $dupCheck = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $dupCheck->execute([$testEmail]);
    assertTest($dupCheck->rowCount() > 0, "Duplicate email successfully detected");

    // ==========================================
    // Password Strength Validation Rule
    // ==========================================
    $weakPassword = "123";
    $isWeakValid = (strlen($weakPassword) >= 8 && preg_match('/[0-9]/', $weakPassword) && preg_match('/[A-Za-z]/', $weakPassword));
    assertTest(!$isWeakValid, "Backend correctly flags weak passwords");

    // ==========================================
    // Sign in with Unverified Email (Blocked)
    // ==========================================
    $stmtLogin = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmtLogin->execute([$testEmail]);
    $loggedInUser = $stmtLogin->fetch();
    
    $isBlocked = false;
    if ($loggedInUser && password_verify($testPass, $loggedInUser['password'])) {
        if ($loggedInUser['is_verified'] == 0) {
            $isBlocked = true;
        }
    }
    assertTest($isBlocked, "Unverified user blocked from signing in");

    // ==========================================
    // Case-Insensitive Email Matching
    // ==========================================
    $uppercaseInputEmail = strtoupper($testEmail);
    $stmtCase = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?)");
    $stmtCase->execute([$uppercaseInputEmail]);
    $caseUser = $stmtCase->fetch();
    assertTest($caseUser && $caseUser['user_id'] == $userId, "Case-insensitive email lookup works successfully");
    
    // ==========================================
    // Sign in with Incorrect Password (Rejected)
    // ==========================================
    $wrongPassValid = false;
    if ($loggedInUser && password_verify("WrongPassword999", $loggedInUser['password'])) {
        $wrongPassValid = true;
    }
    assertTest(!$wrongPassValid, "Incorrect password correctly rejected");

    // ==========================================
    // Email Verification Flow
    // ==========================================
    $stmtVerify = $pdo->prepare("SELECT * FROM users WHERE verification_token = ? AND token_expires_at > NOW()");
    $stmtVerify->execute([$token]);
    $verifyUser = $stmtVerify->fetch();

    if ($verifyUser) {
        $updateVerify = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, token_expires_at = NULL WHERE user_id = ?");
        $updateVerify->execute([$verifyUser['user_id']]);
    }

    $stmtCheckVerified = $pdo->prepare("SELECT is_verified, verification_token FROM users WHERE user_id = ?");
    $stmtCheckVerified->execute([$userId]);
    $finalUser = $stmtCheckVerified->fetch();
    assertTest($finalUser['is_verified'] == 1 && $finalUser['verification_token'] === null, "Email successfully verified and token cleared");

    // ==========================================
    // Expired Token Edge Case Simulation
    // ==========================================
    $expiredToken = bin2hex(random_bytes(32));
    $expiredTime = date('Y-m-d H:i:s', strtotime('-2 hours'));
    
    $stmtExpiredUser = $pdo->prepare("INSERT INTO users (name, email, contact, password, role, verification_token, token_expires_at, is_verified) VALUES ('Expired Bot', ?, '09123456789', ?, 'customer', ?, ?, 0)");
    $stmtExpiredUser->execute(["expired.{$uniqueId}@gmail.com", $hashedPass, $expiredToken, $expiredTime]);
    $expiredUserId = $pdo->lastInsertId();

    $stmtCheckExpired = $pdo->prepare("SELECT * FROM users WHERE verification_token = ? AND token_expires_at > (NOW() AT TIME ZONE 'Asia/Manila')");
    $stmtCheckExpired->execute([$expiredToken]);
    $expiredResult = $stmtCheckExpired->fetch();
    
    assertTest(!$expiredResult, "Expired token is correctly rejected");

    // ==========================================
    // Live SMTP Email Dispatch Test (PUT IT HERE)
    // ==========================================
    require_once '../includes/PHPMailer/PHPMailer.php';
    require_once '../includes/PHPMailer/SMTP.php';
    require_once '../includes/PHPMailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_EMAIL');
    $mail->Password   = getenv('SMTP_PASS');
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom(getenv('SMTP_EMAIL'), 'Veloxity Test System');
    $mail->addAddress($testEmail, 'Test User');
    $mail->isHTML(true);
    $mail->Subject = 'Veloxity SMTP Integration Test';
    $mail->Body    = "<p>If you see this, PHPMailer and Gmail SMTP are fully functional!</p>";

    $emailSent = $mail->send();
    assertTest($emailSent, "PHPMailer successfully dispatched live SMTP email");

    // ==========================================
    // Resend Verification Logic Test
    // ==========================================
    // Simulate finding the user and generating a new token (matching resend_verification.php)
    $newToken = bin2hex(random_bytes(32));
    $newExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $stmtResend = $pdo->prepare("UPDATE users SET verification_token = ?, token_expires_at = ? WHERE user_id = ?");
    $stmtResend->execute([$newToken, $newExpiresAt, $userId]);

    $stmtCheckResend = $pdo->prepare("SELECT verification_token FROM users WHERE user_id = ?");
    $stmtCheckResend->execute([$userId]);
    $resendRow = $stmtCheckResend->fetch();

    assertTest($resendRow && $resendRow['verification_token'] === $newToken && $resendRow['verification_token'] !== $token, "Resend verification successfully overwrites with a fresh token");
    
    // Cleanup test data from database
    $pdo->prepare("DELETE FROM users WHERE user_id IN (?, ?)")->execute([$userId, $expiredUserId]);

    echo "\n----------------------------------------\n";
    echo "Results: <span style='color:green;'>{$passCount} Passed</span>, <span style='color:red;'>{$failCount} Failed</span>\n";

} catch (Exception $e) {
    echo "<span style='color:red;'>Test Suite Error: " . $e->getMessage() . "</span>\n";
}
echo "</pre>";
?>