// Enhanced form handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.contact-form');
    const submitBtn = document.getElementById('submit-btn');
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('char-count');

    // Character counter
    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;

            if (length > 2000) {
                charCount.style.color = '#dc3545';
                charCount.parentElement.style.color = '#dc3545';
            } else if (length > 1800) {
                charCount.style.color = '#ffc107';
                charCount.parentElement.style.color = '#ffc107';
            } else {
                charCount.style.color = '#6c757d';
                charCount.parentElement.style.color = '#6c757d';
            }
        });

        // Initialize character count
        charCount.textContent = messageTextarea.value.length;
    }

    // Form submission handling
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');

            if (btnText && btnLoading) {
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-flex';
                submitBtn.disabled = true;
            }
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});
