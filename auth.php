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
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #FFFFFF;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: hidden;
        }

        .velox-auth-container {
            position: relative;
            width: 100%;
            max-width: 950px;
            height: 580px;
            background: var(--surface-light);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(11, 15, 25, 0.15);
            overflow: hidden;
            border: 1px solid #E2E8F0;
        }

        .forms-container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .signin-signup {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            left: 75%;
            width: 50%;
            transition: 1s 0.7s ease-in-out;
            display: grid;
            grid-template-columns: 1fr;
            z-index: 5;
        }

        form {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 3rem;
            transition: all 0.2s 0.7s;
            overflow: hidden;
            grid-column: 1 / 2;
            grid-row: 1 / 2;
        }

        form.sign-up-form {
            opacity: 0;
            z-index: 1;
        }

        form.sign-in-form {
            z-index: 2;
        }

        .title {
            font-size: 1.8rem;
            color: var(--text-main);
            margin-bottom: 4px;
            font-weight: 800;
        }

        .subtitle {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .panels-container {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        /* Top-anchored panel layouts for spacious image view */
        .panel {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            text-align: left;
            z-index: 6;
            padding: 3.5rem 3rem;
        }

        .left-panel {
            pointer-events: all;
        }

        .right-panel {
            pointer-events: none;
            align-items: flex-end;
            text-align: right;
        }

        .panel .content {
            color: #fff;
            transition: transform 0.9s ease-in-out;
            transition-delay: 0.6s;
            max-width: 360px;
        }

        .panel h3 {
            font-weight: 800;
            line-height: 1.2;
            font-size: 1.6rem;
            margin-bottom: 8px;
        }

        .panel p {
            font-size: 0.85rem;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.85);
            font-style: italic;
            margin-bottom: 1rem;
        }

        .velox-btn.transparent {
            background: transparent;
            border: 2px solid #fff;
            width: 120px;
            height: 36px;
            font-weight: 700;
            font-size: 0.75rem;
            border-radius: var(--radius-sm);
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .velox-btn.transparent:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--velox-primary);
        }

        .right-panel .content {
            transform: translateX(800px);
        }

        /* Deep Charcoal overlay on warehouse/transit photo */
        .velox-auth-container:before {
            content: "";
            position: absolute;
            height: 2000px;
            width: 2000px;
            top: -10%;
            right: 48%;
            transform: translateY(-50%);
            background: linear-gradient(rgba(11, 15, 25, 0.82), rgba(11, 15, 25, 0.88)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            transition: 1.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-radius: 50%;
            z-index: 6;
        }

        /* Animation States */
        .velox-auth-container.sign-up-mode:before {
            transform: translate(100%, -50%);
            right: 52%;
        }

        .velox-auth-container.sign-up-mode .left-panel .content {
            transform: translateX(-800px);
        }

        .velox-auth-container.sign-up-mode .signin-signup {
            left: 25%;
        }

        .velox-auth-container.sign-up-mode form.sign-up-form {
            opacity: 1;
            z-index: 2;
        }

        .velox-auth-container.sign-up-mode form.sign-in-form {
            opacity: 0;
            z-index: 1;
        }

        .velox-auth-container.sign-up-mode .right-panel .content {
            transform: translateX(0%);
        }

        .velox-auth-container.sign-up-mode .left-panel {
            pointer-events: none;
        }

        .velox-auth-container.sign-up-mode .right-panel {
            pointer-events: all;
        }

        .social-text {
            padding: 0.4rem 0;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* Password Input Wrapper & Toggle Icon */
        .velox-password-wrapper {
            position: relative;
            width: 100%;
            max-width: 360px;
            margin: 6px 0;
        }

        .velox-password-wrapper .velox-input-field {
            max-width: 100%;
            margin: 0;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            background: none;
            border: none;
            font-size: 0.95rem;
            color: var(--text-muted);
            z-index: 10;
        }

        /* 5-Segment Strength Meter Container */
        .password-strength-container {
            width: 100%;
            max-width: 360px;
            margin-bottom: 6px;
        }

        .strength-bars {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 4px;
            margin-bottom: 3px;
        }

        .strength-bar {
            height: 4px;
            background-color: #E2E8F0;
            border-radius: 2px;
            transition: background-color 0.3s ease;
        }

        .strength-text {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Alerts */
        .velox-alert-banner {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background-color: var(--surface-light);
            border: 1px solid var(--velox-primary);
            color: var(--text-main);
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 600;
            z-index: 1000;
            box-shadow: 0 10px 25px -5px rgba(11, 15, 25, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .velox-alert-banner.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    </style>
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
                <form action="auth_process.php" method="POST" class="sign-in-form">
                    <h2 class="title">Welcome Back</h2>
                    <p class="subtitle">Sign in to manage trips and track waybills.</p>
                    <div class="velox-input-field">
                        <input type="email" name="email" placeholder="Email Address" required />
                    </div>
                    <div class="velox-input-field">
                        <input type="password" name="password" placeholder="Password" required />
                    </div>
                    <input type="submit" name="signin" value="Sign In" class="velox-btn" />
                    
                    <p class="social-text">Or continue with</p>
                    <div class="social-buttons-grid">
                        <a href="google_login.php" class="velox-social-rect">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </a>
                        <a href="#" class="velox-social-rect">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#1877F2">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>
                    </div>
                </form>

                <!-- Sign Up Form -->
                <form action="auth_process.php" method="POST" class="sign-up-form" id="signUpForm">
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
                    </div>

                    <input type="submit" name="signup" value="Create Account" class="velox-btn" />
                    
                    <p class="social-text">Or register with</p>
                    <div class="social-buttons-grid">
                        <a href="google_login.php" class="velox-social-rect">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </a>
                        <a href="#" class="velox-social-rect">
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

    <script>
        // 1. Dynamic +63 Prefix on Focus / Click
        const phoneInput = document.getElementById('phoneNumberInput');
        const countryPrefix = document.getElementById('countryPrefix');

        if (phoneInput) {
            phoneInput.addEventListener('focus', () => {
                countryPrefix.style.display = 'inline-block';
            });
            phoneInput.addEventListener('blur', () => {
                if (phoneInput.value === '') {
                    countryPrefix.style.display = 'none';
                }
            });
            phoneInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
            });
        }

        // 2. Client-Side Validation (Password Match & Strength)
        const signUpForm = document.getElementById('signUpForm');
        const passwordInput = document.getElementById('signupPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        // Auto-dismiss the PHP session alert banner after 3 seconds with a smooth fade
        const alertBanner = document.getElementById('authAlertBanner');
        if (alertBanner) {
            setTimeout(() => {
                alertBanner.classList.remove('show');
                setTimeout(() => alertBanner.remove(), 400); // Remove from DOM after transition
            }, 3000);
        }

        // Helper replacement for window.alert using the custom banner
        function showVeloxAlert(message) {
            // Remove existing if any
            const existing = document.getElementById('dynamicAlertBanner');
            if (existing) existing.remove();

            const banner = document.createElement('div');
            banner.id = 'dynamicAlertBanner';
            banner.className = 'velox-alert-banner';
            banner.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="color: var(--velox-primary);"></i> <span>${message}</span>`;
            
            document.getElementById('authContainer').appendChild(banner);
            
            // Trigger smooth entrance
            setTimeout(() => banner.classList.add('show'), 10);

            // Auto dismiss after 3s
            setTimeout(() => {
                banner.classList.remove('show');
                setTimeout(() => banner.remove(), 400);
            }, 3000);
        }

        if (signUpForm) {
            signUpForm.addEventListener('submit', (e) => {
                const pass = passwordInput.value;
                const confirmPass = confirmPasswordInput.value;

                let score = 0;
                if (pass.length >= 8) score++;
                if (/[A-Z]/.test(pass)) score++;
                if (/[a-z]/.test(pass)) score++;
                if (/[0-9]/.test(pass)) score++;

                // Replace alert(...) in your form submit listener with:
                if (pass !== confirmPass) {
                    e.preventDefault();
                    showVeloxAlert('Passwords do not match. Please check and try again.');
                    confirmPasswordInput.focus();
                    return;
                }

                if (score < 3) {
                    e.preventDefault();
                    showVeloxAlert('Please choose a stronger password (minimum 8 characters with letters and numbers).');
                    passwordInput.focus();
                }
            });
        }
        
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const container = document.querySelector("#authContainer");

        sign_up_btn.addEventListener('click', () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener('click', () => {
            container.classList.remove("sign-up-mode");
        });

        // 3. Password Visibility Toggle
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const toggleIcon = document.getElementById('toggleIcon');

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
            });
        }

        // 4. 5-Segment Password Strength Calculator
        const bars = [
            document.getElementById('bar1'),
            document.getElementById('bar2'),
            document.getElementById('bar3'),
            document.getElementById('bar4'),
            document.getElementById('bar5')
        ];
        const strengthText = document.getElementById('strengthText');

        if (passwordInput) {
            passwordInput.addEventListener('input', () => {
                const val = passwordInput.value;
                let score = 0;

                if (val.length >= 8) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[a-z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;

                if (val.length === 0) score = 0;

                bars.forEach(bar => bar.style.backgroundColor = '#E2E8F0');

                const colors = ['#EF4444', '#F97316', '#F59E0B', '#10B981', '#059669'];
                const labels = ['Too short', 'Very Weak', 'Weak', 'Fair', 'Strong', 'Secure'];

                for (let i = 0; i < score; i++) {
                    bars[i].style.backgroundColor = colors[Math.min(score - 1, colors.length - 1)];
                }
                
                strengthText.textContent = `Strength: ${labels[score]}`;
            });
        }
    </script>
</body>
</html>