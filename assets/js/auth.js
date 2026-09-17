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

// 3. Reusable Password Visibility Toggle
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', () => {
        // Find the closest password wrapper container
        const wrapper = button.closest('.velox-password-wrapper');
        // Find the input and icon inside that specific wrapper
        const input = wrapper.querySelector('input[type="password"], input[type="text"]');
        const icon = button.querySelector('i');

        if (input) {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            if (icon) {
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        }
    });
});

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