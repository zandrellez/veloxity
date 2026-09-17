<?php
// auth_process.php - Handles Sign Up and Sign In backend logic securely via PDO
require_once '../includes/supabase.php';

date_default_timezone_set('Asia/Manila');

// Start session to persist user login state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// HANDLE SIGN UP (Customer Registration)
// ==========================================
if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($contact) || empty($password) || empty($confirmPassword)) {
        $_SESSION['auth_error'] = "Please fill in all required fields.";
        header("Location: ../auth.php");
        exit();
    }

    if ($password !== $confirmPassword) {
        $_SESSION['auth_error'] = "Passwords do not match.";
        header("Location: ../auth.php");
        exit();
    }

    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[A-Za-z]/', $password)) {
        $_SESSION['auth_error'] = "Password must be at least 8 characters and include letters and numbers.";
        header("Location: ../auth.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['auth_error'] = "An account with this email address already exists.";
            header("Location: ../auth.php");
            exit();        
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate a unique token and set expiration (24 hours from now)
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Insert user with verification token and is_verified = 0
        $insertStmt = $pdo->prepare("INSERT INTO users (name, email, contact, password, role, verification_token, token_expires_at, is_verified) VALUES (?, ?, ?, ?, 'customer', ?, ?, 0)");
        $insertStmt->execute([$name, $email, $contact, $hashedPassword, $token, $expiresAt]);

        $userId = $pdo->lastInsertId();
        $passengerStmt = $pdo->prepare("INSERT INTO saved_passengers (user_id, name, contact, passenger_type) VALUES (?, ?, ?, 'Regular')");
        $passengerStmt->execute([$userId, $name, $contact]);

        // --- SEND EMAIL VIA PHPMailer ---
        require __DIR__ . '/../includes/PHPMailer/PHPMailer.php';
        require __DIR__ . '/../includes/PHPMailer/SMTP.php';
        require __DIR__ . '/../includes/PHPMailer/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        // Detect if running locally or on Render to set the correct domain URL automatically
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $hostName = $_SERVER['HTTP_HOST'];
        $projectFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $projectFolder = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
        $verificationLink = "{$protocol}://{$hostName}{$projectFolder}/actions/verify.php?token={$token}";

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_EMAIL');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipient & Content
        $mail->setFrom(getenv('SMTP_EMAIL'), 'Veloxity System');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Veloxity Account';
        $mail->Body    = "<h3>Hello {$name},</h3>
                          <p>Thank you for registering with Veloxity. Please click the link below to verify your email address:</p>
                          <p><a href='{$verificationLink}' style='background:#f97316;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Verify Account</a></p>
                          <p>This link will expire in 24 hours.</p>";

        $mail->send();

        $_SESSION['auth_error'] = "Account registered successfully! Please check your email to verify your account.";
        header("Location: ../auth.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['auth_error'] = "Account registered, but we couldn't send the verification email. <a href='actions/resend_verification.php?email=" . urlencode($email) . "' style='color: var(--velox-primary); text-decoration: underline;'>Click here to try sending again</a>.";
        header("Location: ../auth.php");
        exit();
    } catch (PDOException $e) {
        die("Registration Error: " . $e->getMessage());
    }
}

// ==========================================
// HANDLE SIGN IN (Unified Login for all Roles)
// ==========================================
if (isset($_POST['signin'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['auth_error'] = "Please fill in all required fields.";
        header("Location: ../auth.php");
        exit();
    }

    try {
        // Fetch user record by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 0) {
                $_SESSION['auth_error'] = "Please verify your email address. Didn't receive the email? <a href='actions/resend_verification.php?email=" . urlencode($email) . "' style='color: var(--velox-primary); text-decoration: underline; font-weight: bold;'>Resend verification link</a>";
                header("Location: ../auth.php");
                exit();
            }

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Role-based redirection
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
        } else {
            // Invalid credentials
            $_SESSION['auth_error'] = "Invalid email address or password.";
            header("Location: ../auth.php");
            exit();
        }

    } catch (PDOException $e) {
        die("Login Error: " . $e->getMessage());
    }
}
?>