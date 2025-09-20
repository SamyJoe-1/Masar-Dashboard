// Static Pages JavaScript - For FAQ toggles, contact form, and search functionality

document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle Functionality
    initFAQToggles();

    // FAQ Category Filtering
    initFAQCategories();

    // FAQ Search
    initFAQSearch();

    // Contact Form Handling
    initContactForm();

    // Smooth Scrolling for anchor links
    initSmoothScrolling();
});

// FAQ Toggle Functionality
function initFAQToggles() {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', function() {
                console.log(123);
                // Toggle active state
                item.classList.toggle('active');

                // Update toggle icon
                const toggle = question.querySelector('.faq-toggle');
                if (toggle) {
                    toggle.textContent = item.classList.contains('active') ? '−' : '+';
                }

                // Animate answer visibility
                const answer = item.querySelector('.faq-answer');
                if (answer) {
                    if (item.classList.contains('active')) {
                        answer.style.display = 'block';
                        // Small delay for smooth animation
                        setTimeout(() => {
                            answer.style.opacity = '1';
                        }, 10);
                    } else {
                        answer.style.opacity = '0';
                        setTimeout(() => {
                            answer.style.display = 'none';
                        }, 300);
                    }
                }
            });
        }
    });
}

// FAQ Category Filtering
function initFAQCategories() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const faqSections = document.querySelectorAll('.faq-section');

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;

            // Update active button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Show/hide sections
            faqSections.forEach(section => {
                if (category === 'all' || section.dataset.category === category) {
                    section.style.display = 'block';
                    // Fade in animation
                    setTimeout(() => {
                        section.style.opacity = '1';
                    }, 10);
                } else {
                    section.style.opacity = '0';
                    setTimeout(() => {
                        section.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
}

// FAQ Search Functionality
function initFAQSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');
    const faqItems = document.querySelectorAll('.faq-item');

    if (searchInput && faqItems.length > 0) {
        // Search on input
        searchInput.addEventListener('input', performSearch);

        // Search on button click
        if (searchBtn) {
            searchBtn.addEventListener('click', performSearch);
        }

        // Search on enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let hasResults = false;

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question h3');
            const answer = item.querySelector('.faq-answer');

            if (question && answer) {
                const questionText = question.textContent.toLowerCase();
                const answerText = answer.textContent.toLowerCase();

                if (searchTerm === '' ||
                    questionText.includes(searchTerm) ||
                    answerText.includes(searchTerm)) {
                    item.style.display = 'block';
                    hasResults = true;

                    // Highlight search terms if search is active
                    if (searchTerm !== '') {
                        highlightText(question, searchTerm);
                        highlightText(answer, searchTerm);
                    } else {
                        removeHighlight(question);
                        removeHighlight(answer);
                    }
                } else {
                    item.style.display = 'none';
                }
            }
        });

        // Show/hide no results message
        toggleNoResultsMessage(!hasResults && searchTerm !== '');
    }

    function highlightText(element, searchTerm) {
        if (!element || !searchTerm) return;

        const originalText = element.getAttribute('data-original') || element.innerHTML;
        if (!element.getAttribute('data-original')) {
            element.setAttribute('data-original', originalText);
        }

        const regex = new RegExp(`(${searchTerm})`, 'gi');
        const highlightedText = originalText.replace(regex, '<mark>$1</mark>');
        element.innerHTML = highlightedText;
    }

    function removeHighlight(element) {
        if (!element) return;

        const originalText = element.getAttribute('data-original');
        if (originalText) {
            element.innerHTML = originalText;
        }
    }

    function toggleNoResultsMessage(show) {
        let noResultsMsg = document.querySelector('.no-results-message');

        if (show && !noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.className = 'no-results-message';
            noResultsMsg.innerHTML = `
                <div style="text-align: center; padding: 3rem; color: var(--gray-medium);">
                    <h3>لم يتم العثور على نتائج</h3>
                    <p>جرب البحث بكلمات أخرى أو تصفح الأقسام المختلفة</p>
                </div>
            `;
            document.querySelector('.faq-sections').appendChild(noResultsMsg);
        } else if (!show && noResultsMsg) {
            noResultsMsg.remove();
        }
    }
}

// Contact Form Handling
function initContactForm() {
    const contactForm = document.querySelector('.contact-form');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(contactForm);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }

            // Validate form
            if (validateContactForm(data)) {
                submitContactForm(data);
            }
        });
    }
}

function validateContactForm(data) {
    const errors = [];

    // Required fields validation
    if (!data.name || data.name.trim().length < 2) {
        errors.push('الاسم الكامل مطلوب ويجب أن يكون أكثر من حرفين');
    }

    if (!data.email || !isValidEmail(data.email)) {
        errors.push('البريد الإلكتروني غير صحيح');
    }

    if (!data.subject) {
        errors.push('يرجى اختيار موضوع الرسالة');
    }

    if (!data.message || data.message.trim().length < 10) {
        errors.push('الرسالة مطلوبة ويجب أن تكون أكثر من 10 أحرف');
    }

    // Phone validation (optional but if provided, should be valid)
    if (data.phone && !isValidPhone(data.phone)) {
        errors.push('رقم الهاتف غير صحيح');
    }

    if (errors.length > 0) {
        showFormErrors(errors);
        return false;
    }

    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    // Saudi phone number validation
    const phoneRegex = /^(\+966|0)?[5-9][0-9]{8}$/;
    return phoneRegex.test(phone.replace(/[\s-]/g, ''));
}

function showFormErrors(errors) {
    // Remove existing error messages
    const existingErrors = document.querySelectorAll('.form-error');
    existingErrors.forEach(error => error.remove());

    // Create error container
    const errorContainer = document.createElement('div');
    errorContainer.className = 'form-error';
    errorContainer.style.cssText = `
        background: #fee;
        border: 2px solid #f66;
        color: #d00;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
    `;

    const errorList = document.createElement('ul');
    errorList.style.cssText = 'margin: 0; padding-right: 1rem;';

    errors.forEach(error => {
        const errorItem = document.createElement('li');
        errorItem.textContent = error;
        errorList.appendChild(errorItem);
    });

    errorContainer.appendChild(errorList);

    // Insert error container before form
    const form = document.querySelector('.contact-form');
    form.parentNode.insertBefore(errorContainer, form);

    // Scroll to error
    errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function submitContactForm(data) {
    // Show loading state
    const submitBtn = document.querySelector('.contact-form button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'جارٍ الإرسال...';
    submitBtn.disabled = true;

    // Simulate form submission (replace with actual API call)
    setTimeout(() => {
        // Show success message
        showFormSuccess();

        // Reset form
        document.querySelector('.contact-form').reset();

        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;

        // In a real application, you would send data to your backend:
        // fetch('/api/contact', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(data)
        // });
    }, 2000);
}

function showFormSuccess() {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.form-success, .form-error');
    existingMessages.forEach(msg => msg.remove());

    // Create success message
    const successContainer = document.createElement('div');
    successContainer.className = 'form-success';
    successContainer.style.cssText = `
        background: #efe;
        border: 2px solid #6c6;
        color: #060;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        text-align: center;
    `;

    successContainer.innerHTML = `
        <h4 style="margin: 0 0 0.5rem 0;">تم إرسال الرسالة بنجاح!</h4>
        <p style="margin: 0;">سنتواصل معك في أقرب وقت ممكن</p>
    `;

    // Insert success message before form
    const form = document.querySelector('.contact-form');
    form.parentNode.insertBefore(successContainer, form);

    // Scroll to success message
    successContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Auto-remove success message after 5 seconds
    setTimeout(() => {
        if (successContainer.parentNode) {
            successContainer.remove();
        }
    }, 5000);
}

// Smooth Scrolling for Anchor Links
function initSmoothScrolling() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');

    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Utility function to add loading states to buttons
function addLoadingState(button, loadingText = 'جارٍ التحميل...') {
    const originalText = button.textContent;
    const originalDisabled = button.disabled;

    button.textContent = loadingText;
    button.disabled = true;
    button.classList.add('loading');

    return function removeLoadingState() {
        button.textContent = originalText;
        button.disabled = originalDisabled;
        button.classList.remove('loading');
    };
}

// Initialize animations on scroll (optional enhancement)
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe elements that should animate
    const animateElements = document.querySelectorAll(
        '.value-item, .service-card, .benefit-item, .pricing-card, .achievement-item'
    );

    animateElements.forEach(el => {
        el.classList.add('animate-on-scroll');
        observer.observe(el);
    });
}

// Call scroll animations if supported
if ('IntersectionObserver' in window) {
    document.addEventListener('DOMContentLoaded', initScrollAnimations);
}
