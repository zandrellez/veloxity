<?php
// auth.php - Veloxity Sliding Transition Authentication Portal
require_once 'includes/supabase.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloxity - Transit & Cargo Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/auth.js" defer></script>
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="velox-auth-container" id="authContainer">
        <?php
        if (isset($_SESSION['auth_error'])) {
            $msg = $_SESSION['auth_error'];
            echo '<div id="authAlertBanner" class="velox-alert-banner show">
                    <i class="fa-solid fa-circle-exclamation" style="color: var(--velox-primary);"></i> 
                    <span>' . $msg . '</span>
                </div>';
            unset($_SESSION['auth_error']);
        }
        ?>

        <div class="forms-container">
            <div class="signin-signup">
            
                <!-- Sign In Form -->
                <form action="actions/auth_process.php" method="POST" class="sign-in-form">
                    <h2 class="title">Welcome Back</h2>
                    <p class="subtitle">Sign in to manage trips and track waybills.</p>
                    <div class="velox-input-field">
                        <input type="email" name="email" placeholder="Email Address" required />
                    </div>
                    <div class="velox-password-wrapper">
                        <div class="velox-input-field">
                            <input type="password" name="password" placeholder="Password" required />
                        </div>
                        <button type="button" class="toggle-password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <input type="submit" name="signin" value="Sign In" class="velox-btn" />
                    
                    <p class="social-text">Or continue with</p>
                    <div class="social-buttons-grid">
                        <a href="actions/google_login.php" class="velox-social-rect">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </a>
                        <a href="actions/facebook_login.php" class="velox-social-rect">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#1877F2">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>
                    </div>
                </form>

                <!-- Sign Up Form -->
                <form action="actions/auth_process.php" method="POST" class="sign-up-form" id="signUpForm">
                    <h2 class="title">Create Account</h2>
                    <p class="subtitle">Get started with multi-operator booking.</p>
                    
                    <div class="velox-input-field" style="margin: 4px 0;">
                        <input type="text" name="name" placeholder="Full Name" required />
                    </div>
                    
                    <div class="velox-input-field" style="margin: 4px 0;">
                        <input type="email" name="email" placeholder="Email Address" required />
                    </div>
                    
                    <!-- Dynamic +63 Phone Input -->
                    <div class="velox-phone-wrapper" style="position: relative; width: 100%; max-width: 360px; margin: 4px 0;">
                        <div class="velox-input-field" id="phoneContainer" style="max-width: 100%; margin: 0; display: flex; align-items: center;">
                            <span id="countryPrefix" style="display: none; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; border-right: 1px solid #CBD5E1; padding-right: 6px; margin-right: 6px;">+63</span>
                            <input type="text" name="contact" id="phoneNumberInput" placeholder="Mobile Number" maxlength="10" pattern="[0-9]{10}" required style="border: none; outline: none; width: 100%; background: none;" />
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="velox-password-wrapper">
                        <div class="velox-input-field">
                            <input type="password" name="password" id="signupPassword" placeholder="Create Password" required />
                        </div>
                        <button type="button" class="toggle-password" id="togglePasswordBtn">
                            <i class="fa-solid fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    
                    <!-- Segmented Strength Meter -->
                    <div class="password-strength-container">
                        <div class="strength-bars">
                            <div class="strength-bar" id="bar1"></div>
                            <div class="strength-bar" id="bar2"></div>
                            <div class="strength-bar" id="bar3"></div>
                            <div class="strength-bar" id="bar4"></div>
                            <div class="strength-bar" id="bar5"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Strength: Too short</div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="velox-password-wrapper">
                        <div class="velox-input-field">
                            <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required />
                        </div>
                        <button type="button" class="toggle-password" id="togglePasswordBtn">
                            <i class="fa-solid fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>

                    <input type="submit" name="signup" value="Create Account" class="velox-btn" />
                    
                    <p class="social-text">Or register with</p>
                    <div class="social-buttons-grid">
                        <a href="actions/google_login.php" class="velox-social-rect">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </a>
                        <a href="actions/facebook_login.php" class="velox-social-rect">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#1877F2">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="panels-container">
            <!-- Left Panel (Visible in Sign-In Mode) -->
            <div class="panel left-panel">
                <div class="content">
                    <a href="index.php" class="tracking-widest text-xs">&larr; Back to Home</a>
                    <h3 class="text-orange-400 font-bold tracking-widest uppercase text-xs">New to Veloxity?</h3>
                    <p>"Connecting hubs, securing terminal schedules, and delivering cargo transparency with high-performance operational architecture."</p>
                    <button class="velox-btn transparent" id="sign-up-btn">Sign Up</button>
                </div>
            </div>

            <!-- Right Panel (Visible in Sign-Up Mode) -->
            <div class="panel right-panel">
                <div class="content">
                    <a href="index.php" class="tracking-widest text-xs">&larr; Back to Home</a>
                    <h3 class="text-orange-400 font-bold tracking-widest uppercase text-xs">One of us?</h3>
                    <p>"Empowering transit operators and streamlining passenger journeys with precision network management."</p>
                    <button class="velox-btn transparent" id="sign-in-btn">Sign In</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>