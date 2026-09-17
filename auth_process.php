<?php
// auth_process.php - Handles Sign Up and Sign In backend logic securely via PDO
require_once 'includes/supabase.php';

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

    // Validation: Empty fields
    if (empty($name) || empty($email) || empty($contact) || empty($password) || empty($confirmPassword)) {
        $_SESSION['auth_error'] = "Please fill in all required fields.";
        header("Location: auth.php");
        exit();
    }

    // Validation: Passwords match
    if ($password !== $confirmPassword) {
        $_SESSION['auth_error'] = "Passwords do not match.";
        header("Location: auth.php");
        exit();
    }

    // Enforce minimum password strength on the backend
    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[A-Za-z]/', $password)) {
        $_SESSION['auth_error'] = "Password must be at least 8 characters and include letters and numbers.";
        header("Location: auth.php");
        exit();
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['auth_error'] = "An account with this email address already exists.";
            header("Location: auth.php");
            exit();        
        }

        // Hash password securely using bcrypt/Argon2 default
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user with default 'customer' role
        $insertStmt = $pdo->prepare("INSERT INTO users (name, email, contact, password, role) VALUES (?, ?, ?, ?, 'customer')");
        $insertStmt->execute([$name, $email, $contact, $hashedPassword]);

        // Automatically create a matching entry in saved_passengers for the account holder
        $userId = $pdo->lastInsertId();
        $passengerStmt = $pdo->prepare("INSERT INTO saved_passengers (user_id, name, contact, passenger_type) VALUES (?, ?, ?, 'Regular')");
        $passengerStmt->execute([$userId, $name, $contact]);

        header("Location: auth.php?success=accountcreated");
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
        header("Location: auth.php");
        exit();
    }

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['auth_error'] = "Invalid email address or password.";
        header("Location: auth.php");
        exit();
    }   

    try {
        // Fetch user record by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Role-based redirection
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    exit();
                case 'operator':
                    header("Location: operator/dashboard.php");
                    exit();
                case 'customer':
                default:
                    header("Location: customer/dashboard.php");
                    exit();
            }
        } else {
            // Invalid credentials
            header("Location: auth.php?error=invalidcredentials");
            exit();
        }

    } catch (PDOException $e) {
        die("Login Error: " . $e->getMessage());
    }
}
?>