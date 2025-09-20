// Simple collapse functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle all collapse toggles
    const toggleButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const targetSelector = this.getAttribute('data-bs-target');
            const target = document.querySelector(targetSelector);

            if (target) {
                // Toggle the collapse
                if (target.classList.contains('show')) {
                    // Hide
                    target.classList.remove('show');
                } else {
                    // Show
                    target.classList.add('show');
                }
            }
        });
    });
});
