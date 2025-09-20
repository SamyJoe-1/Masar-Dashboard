// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Show corresponding form
            forms.forEach(form => {
                form.classList.remove('active');
                if (form.id === targetTab + 'Form') {
                    form.classList.add('active');
                }
            });
        });
    });

    // Password strength checker
    const registerPassword = document.getElementById('register_password');
    if (registerPassword) {
        registerPassword.addEventListener('input', checkPasswordStrength);
    }

    // Form submission with loading state
    const authForms = document.querySelectorAll('.auth-form');
    authForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.auth-btn');
            submitBtn.classList.add('loading');
        });
    });
});

// Password visibility toggle
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);

    const toggle = field.nextElementSibling;
    toggle.textContent = type === 'password' ? '👁️' : '🙈';
}

// Password strength checker
function checkPasswordStrength() {
    const password = document.getElementById('register_password').value;
    const strengthIndicator = document.getElementById('passwordStrength');

    let strength = 0;

    // Check password criteria
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/\d/)) strength++;
    if (password.match(/[^a-zA-Z\d]/)) strength++;

    // Update strength indicator
    strengthIndicator.className = 'password-strength';

    if (strength === 0) {
        strengthIndicator.style.width = '0%';
    } else if (strength <= 2) {
        strengthIndicator.classList.add('weak');
    } else if (strength === 3) {
        strengthIndicator.classList.add('medium');
    } else {
        strengthIndicator.classList.add('strong');
    }
}

// Show forgot password form
function showForgotPassword() {
    document.getElementById('authTabs').style.display = 'none';
    document.querySelectorAll('.auth-form').forEach(form => {
        form.classList.remove('active');
    });
    document.getElementById('forgotPasswordForm').classList.add('active');
}

// Show login form (back from forgot password)
function showLogin() {
    document.getElementById('authTabs').style.display = 'flex';
    document.querySelectorAll('.auth-form').forEach(form => {
        form.classList.remove('active');
    });
    document.getElementById('loginForm').classList.add('active');

    // Reset tabs
    document.querySelectorAll('.auth-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelector('.auth-tab[data-tab="login"]').classList.add('active');
}
