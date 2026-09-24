// 1. Dynamic +63 Prefix on Focus / Click
const setupPhoneInput = (inputId, prefixId) => {
    const pInput = document.getElementById(inputId);
    const cPrefix = document.getElementById(prefixId);

    if (pInput && cPrefix) {
        pInput.addEventListener('focus', () => { cPrefix.style.display = 'inline-block'; });
        pInput.addEventListener('blur', () => { if (pInput.value === '') cPrefix.style.display = 'none'; });
        pInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
        });
        // Keep prefix open if there's already an error-retained value
        if (pInput.value !== '') cPrefix.style.display = 'inline-block';
    }
};
setupPhoneInput('phoneNumberInput', 'countryPrefix');
setupPhoneInput('operatorPhoneNumberInput', 'operatorCountryPrefix');

// 2. Client-Side Validation (Password Match & Strength)
const signUpForm = document.getElementById('signUpForm');
const passwordInput = document.getElementById('signupPassword');
const confirmPasswordInput = document.getElementById('confirmPassword');

// 2. Alert Banner Auto-Dismiss & Helper
const alertBanner = document.getElementById('authAlertBanner');
if (alertBanner) {
    setTimeout(() => {
        alertBanner.classList.remove('show');
        setTimeout(() => alertBanner.remove(), 400);
    }, 3000);
}

function showVeloxAlert(message) {
    const existing = document.getElementById('dynamicAlertBanner');
    if (existing) existing.remove();

    const banner = document.createElement('div');
    banner.id = 'dynamicAlertBanner';
    banner.className = 'velox-alert-banner';
    banner.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="color: var(--velox-primary);"></i> <span>${message}</span>`;

    document.getElementById('authContainer').appendChild(banner);
    setTimeout(() => banner.classList.add('show'), 10);
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

// 3. Hash Routing & Context Switching
const container = document.getElementById('authContainer');
const sign_in_btn = document.querySelector("#sign-in-btn");
const secondaryActionBtn = document.querySelector("#secondaryActionBtn");
const leftPanelTitle = document.getElementById("leftPanelTitle");
const leftPanelDesc = document.getElementById("leftPanelDesc");

function handleRouting() {
    const hash = window.location.hash;
    container.classList.remove("sign-up-mode", "onboarding-mode");
    
    if (hash === "#signup") {
        container.classList.add("sign-up-mode");
        setCustomerContext();
    } else if (hash === "#onboarding") {
        container.classList.add("onboarding-mode");
        setOnboardingContext();
    } else {
        // Default to Sign In / Customer Mode context on left panel
        setCustomerContext();
    }
}

function setCustomerContext() {
    if (leftPanelTitle) leftPanelTitle.textContent = "New to Veloxity?";
    if (leftPanelDesc) leftPanelDesc.textContent = "Connecting hubs, securing terminal schedules, and delivering cargo transparency with high-performance operational architecture.";
    if (secondaryActionBtn) {
        secondaryActionBtn.textContent = "Sign Up";
        secondaryActionBtn.onclick = () => { window.location.hash = "#signup"; };
    }
}

function setOnboardingContext() {
    if (leftPanelTitle) leftPanelTitle.textContent = "New Fleet Partner?";
    if (leftPanelDesc) leftPanelDesc.textContent = "Register your terminal operations and manage booking allotments transparently.";
    if (secondaryActionBtn) {
        secondaryActionBtn.textContent = "Operator Apply";
        secondaryActionBtn.onclick = () => { window.location.hash = "#onboarding"; };
    }
}

window.addEventListener('DOMContentLoaded', handleRouting);
window.addEventListener('hashchange', handleRouting);

if (sign_in_btn) {
    sign_in_btn.addEventListener('click', () => {
        window.location.hash = "#signin";
    });
}

// Multi-Step Wizard with State Retention, File Validation & Submit Interception
const onboardingForm = document.getElementById('onboardingForm');
if (onboardingForm) {
    const steps = onboardingForm.querySelectorAll('.onboarding-step');
    const nextBtns = onboardingForm.querySelectorAll('.step-next-btn');
    const prevBtns = onboardingForm.querySelectorAll('.step-prev-btn');
    let currentStep = 0;

    function updateWizardSteps() {
        steps.forEach((step, index) => {
            step.classList.toggle('active', index === currentStep);
        });
    }

    // Auto-jump to error step if PHP returned an error
    const hasError = document.getElementById('authAlertBanner') !== null;
    if (hasError) {
        const repName = onboardingForm.querySelector('input[name="name"]');
        const pass = onboardingForm.querySelector('input[name="password"]');
        
        if (repName && repName.value && pass && pass.value) {
            currentStep = 2;
        } else if (repName && repName.value) {
            currentStep = 1;
        } else {
            currentStep = 0;
        }
        updateWizardSteps();
    }

    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const activeStepEl = steps[currentStep];
            const inputs = activeStepEl.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value) {
                    isValid = false;
                    input.reportValidity();
                }
            });

            if (currentStep === 0) {
                const permitInput = document.getElementById('businessPermitInput');
                if (!permitInput.files || permitInput.files.length === 0) {
                    isValid = false;
                    showVeloxAlert('Please upload your Business Permit or Franchise document.');
                }
            }

            if (isValid && currentStep < steps.length - 1) {
                currentStep++;
                updateWizardSteps();
            }
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateWizardSteps();
            }
        });
    });

    // Intercept final submission to validate passwords and bypass hidden validation blocks
    onboardingForm.addEventListener('submit', (e) => {
        const pass = document.getElementById('operatorPassword').value;
        const confirmPass = document.getElementById('operatorConfirmPassword').value;

        let score = 0;
        if (pass.length >= 8) score++;
        if (/[A-Z]/.test(pass)) score++;
        if (/[a-z]/.test(pass)) score++;
        if (/[0-9]/.test(pass)) score++;

        if (pass !== confirmPass) {
            e.preventDefault();
            currentStep = 2;
            updateWizardSteps();
            showVeloxAlert('Passwords do not match. Please check and try again.');
            return;
        }

        if (score < 3) {
            e.preventDefault();
            currentStep = 2;
            updateWizardSteps();
            showVeloxAlert('Please choose a stronger password (minimum 8 characters with letters and numbers).');
            return;
        }
    });
}

// File Upload Validation (PDF, Images, Max 5MB)
const permitInput = document.getElementById('businessPermitInput');
const dropzoneText = document.getElementById('fileDropzoneText');

if (permitInput) {
    permitInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        const maxSizeMB = 5;
        const maxSizeBytes = maxSizeMB * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
            showVeloxAlert('Invalid file format. Please upload a PDF or an Image (JPG/PNG).');
            permitInput.value = '';
            dropzoneText.innerHTML = `Drag & drop file or <span style="color: var(--velox-primary); text-decoration: underline;">browse</span>`;
            return;
        }

        if (file.size > maxSizeBytes) {
            showVeloxAlert(`File size exceeds ${maxSizeMB}MB limit. Please choose a smaller file.`);
            permitInput.value = '';
            dropzoneText.innerHTML = `Drag & drop file or <span style="color: var(--velox-primary); text-decoration: underline;">browse</span>`;
            return;
        }

        // Show selected file name inside dropzone text
        dropzoneText.innerHTML = `<i class="fa-solid fa-file-circle-check" style="color: var(--color-success-default);"></i> ${file.name}`;
    });
}

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

// Password Strength Calculators (Customer & Operator)
const setupPasswordStrength = (passwordInputId, barPrefix, textId) => {
    const passInput = document.getElementById(passwordInputId);
    const bars = [
        document.getElementById(`${barPrefix}1`),
        document.getElementById(`${barPrefix}2`),
        document.getElementById(`${barPrefix}3`),
        document.getElementById(`${barPrefix}4`),
        document.getElementById(`${barPrefix}5`)
    ];
    const strengthText = document.getElementById(textId);

    if (passInput) {
        passInput.addEventListener('input', () => {
            const val = passInput.value;
            let score = 0;

            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            if (val.length === 0) score = 0;

            bars.forEach(bar => { if (bar) bar.style.backgroundColor = '#E2E8F0'; });

            const colors = ['#EF4444', '#F97316', '#F59E0B', '#10B981', '#059669'];
            const labels = ['Too short', 'Very Weak', 'Weak', 'Fair', 'Strong', 'Secure'];

            for (let i = 0; i < score; i++) {
                if (bars[i]) bars[i].style.backgroundColor = colors[Math.min(score - 1, colors.length - 1)];
            }

            if (strengthText) {
                strengthText.textContent = `Strength: ${labels[score]}`;
            }
        });
    }
};

setupPasswordStrength('signupPassword', 'bar', 'strengthText');
setupPasswordStrength('operatorPassword', 'opBar', 'opStrengthText');