// Language toggle functionality
function toggleLanguage() {
    const currentLang = document.documentElement.lang;
    const newLang = currentLang === 'ar' ? 'en' : 'ar';
    const newDir = newLang === 'ar' ? 'rtl' : 'ltr';

    document.documentElement.lang = newLang;
    document.documentElement.dir = newDir;

    // Update button text
    const button = document.querySelector('.lang-switch');
    button.textContent = newLang === 'ar' ? 'English' : 'العربية';

    // Here you would typically load different content or redirect
    // For demo purposes, we'll just toggle direction
}

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Upload zone drag and drop simulation
document.addEventListener('DOMContentLoaded', () => {
    const uploadZone = document.querySelector('.upload-zone');
    if (uploadZone) {
        uploadZone.addEventListener('click', function() {
            this.style.background = 'rgba(255, 255, 255, 0.1)';
            this.innerHTML = '<div class="upload-icon">⏳</div><h4>جاري المعالجة...</h4>';

            setTimeout(() => {
                this.innerHTML = '<div class="upload-icon">✅</div><h4>تم الرفع بنجاح!</h4><p>156 ملف تم تحليله</p>';
                this.style.background = 'rgba(16, 185, 129, 0.1)';
            }, 2000);
        });
    }
});


// Scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
        }
    });
}, observerOptions);

// Observe all feature cards and process steps
document.querySelectorAll('.feature-card, .process-step').forEach(el => {
    observer.observe(el);
});

// Stats counter animation
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        const target = parseInt(counter.textContent);
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 30);
    });
}

// Trigger counter animation when hero is visible
const heroObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounters();
            heroObserver.unobserve(entry.target);
        }
    });
});

heroObserver.observe(document.querySelector('.hero'));
function toggleMobileMenu() {
    console.log('12')
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('mobileOverlay');

    sidebar.classList.add('active');
    overlay.classList.add('active');
    overlay.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('mobileOverlay');

    sidebar.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';

    setTimeout(() => {
        overlay.style.display = 'none';
    }, 300);
}

// Close mobile menu when clicking on links with anchors
document.addEventListener('DOMContentLoaded', function() {
    const anchorLinks = document.querySelectorAll('a[href*="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1050) {
                closeMobileMenu();
            }
        });
    });
});
