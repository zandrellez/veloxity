<?php
// auth_process.php - Handles Sign Up and Sign In backend logic securely via PDO
require_once '../includes/supabase.php';

date_default_timezone_set('Asia/Manila');

// Start session to persist user login state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// 1. HANDLE OPERATOR ONBOARDING
// ==========================================
if (isset($_POST['operator_onboarding'])) {
    $operatorName = trim($_POST['operator_name']);
    $headquarters = trim($_POST['headquarters_address']);
    $repName = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($operatorName) || empty($headquarters) || empty($repName) || empty($email) || empty($contact) || empty($password) || empty($confirmPassword)) {
        $_SESSION['auth_error'] = "Please fill in all required fields.";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    if ($password !== $confirmPassword) {
        $_SESSION['auth_error'] = "Passwords do not match.";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[A-Za-z]/', $password)) {
        $_SESSION['auth_error'] = "Password must be at least 8 characters and include letters and numbers.";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    // Handle File Upload (Business Permit) to Supabase Storage
    if (!isset($_FILES['permit_number']) || $_FILES['permit_number']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['auth_error'] = "Business Permit / Franchise file is required.";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    $file = $_FILES['permit_number'];
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
    $maxSizeMB = 5;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['auth_error'] = "Error uploading file. Please try again.";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['auth_error'] = "Invalid file type. Only PDF and Images (JPG/PNG) are accepted.";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    if ($file['size'] > ($maxSizeMB * 1024 * 1024)) {
        $_SESSION['auth_error'] = "File size exceeds the 5MB limit.";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    // Prepare Supabase Storage Upload via cURL
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'permit_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $storagePath = 'permits/' . $fileName; // Path inside the bucket

    // Retrieve Supabase URL and Service Key/Anon Key from environment variables
    $supabaseUrl = getenv('SUPABASE_URL'); // e.g., https://your-project.supabase.co
    $supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY') ?: getenv('SUPABASE_ANON_KEY'); 

    if (!$supabaseUrl || !$supabaseKey) {
        $_SESSION['auth_error'] = "Supabase storage configuration keys are missing.";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    $uploadUrl = rtrim($supabaseUrl, '/') . '/storage/v1/object/' . $storagePath;

    // Read temporary file contents
    $fileData = file_get_contents($file['tmp_name']);

    $ch = curl_init($uploadUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $supabaseKey,
        'Content-Type: ' . $file['type'],
        'x-upsert: true'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 && $httpCode !== 201) {
        $_SESSION['auth_error'] = "Failed to upload file to Supabase Storage (Code: {$httpCode}).";
        header("Location: ../auth.php#onboarding");
        exit();
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['auth_error'] = "An account with this email address already exists.";
            header("Location: ../auth.php#onboarding");
            exit();        
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user account with role 'operator_admin'
        $userStmt = $pdo->prepare("INSERT INTO users (name, email, password, contact, role, is_verified) VALUES (?, ?, ?, ?, 'operator_admin', 1)");
        $userStmt->execute([$repName, $email, $hashedPassword, $contact]);
        $userId = $pdo->lastInsertId();

        // Insert operator application record with 'Pending' status, saving the Supabase storage path
        $opStmt = $pdo->prepare("INSERT INTO operators (operator_name, permit_number, headquarters_address, user_id, contact_email, contact_phone, acc_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        $opStmt->execute([$operatorName, $storagePath, $headquarters, $userId, $email, $contact]);

        $_SESSION['auth_error'] = "Operator application submitted successfully! Your account is currently pending admin approval.";
        header("Location: ../auth.php#signin");
        exit();

    } catch (PDOException $e) {
        $_SESSION['auth_error'] = "Database error: " . $e->getMessage();
        header("Location: ../auth.php#onboarding");
        exit();
    }
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