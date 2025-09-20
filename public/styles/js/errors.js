function showPage(pageNumber) {
    // Update navigation
    document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(`nav-${pageNumber}`).classList.add('active');

    // Hide current page
    document.getElementById(`page-${currentPage}`).classList.remove('active');

    // Show new page with smooth transition
    setTimeout(() => {
        document.getElementById(`page-${pageNumber}`).classList.add('active');
        currentPage = pageNumber;
    }, 300);
}

function goHome() {
    // Create a cool transition effect before redirect
    document.body.style.transform = 'scale(1.1)';
    document.body.style.opacity = '0';

    setTimeout(() => {
        location.href = '/';
        // alert('Redirecting to home page...');
        // window.location.href = '/';

        // Reset for demo
        document.body.style.transform = 'scale(1)';
        document.body.style.opacity = '1';
    }, 500);
}

// Create floating particles
function createParticles(containerId, count = 15) {
    const container = document.getElementById(containerId);

    for (let i = 0; i < count; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';

        // Random size and position
        const size = Math.random() * 4 + 2;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 8 + 's';
        particle.style.animationDuration = (Math.random() * 5 + 5) + 's';

        container.appendChild(particle);
    }
}

// Initialize particles for each page
createParticles('particles-404');
createParticles('particles-500');
createParticles('particles-403');

// Keyboard navigation
document.addEventListener('keydown', (e) => {
    switch(e.key) {
        case '1':
            showPage('404');
            break;
        case '2':
            showPage('500');
            break;
        case '3':
            showPage('403');
            break;
        case 'Escape':
            goHome();
            break;
        case 'ArrowLeft':
            const pages = ['404', '500', '403'];
            const currentIndex = pages.indexOf(currentPage);
            const prevPage = pages[(currentIndex - 1 + pages.length) % pages.length];
            showPage(prevPage);
            break;
        case 'ArrowRight':
            const pagesRight = ['404', '500', '403'];
            const currentIndexRight = pagesRight.indexOf(currentPage);
            const nextPage = pagesRight[(currentIndexRight + 1) % pagesRight.length];
            showPage(nextPage);
            break;
    }
});

// Mouse interaction effects
document.addEventListener('mousemove', (e) => {
    const mouseX = e.clientX / window.innerWidth;
    const mouseY = e.clientY / window.innerHeight;

    // Subtle parallax effect on icons
    const icons = document.querySelectorAll('.astronaut, .server-icon, .lock-icon');
    icons.forEach(icon => {
        const speed = 0.02;
        const x = (mouseX - 0.5) * speed * 100;
        const y = (mouseY - 0.5) * speed * 100;
        icon.style.transform = `translate(${x}px, ${y}px)`;
    });
});

// Add click effects to error numbers
document.querySelectorAll('[class*="error-"]').forEach(errorCode => {
    errorCode.addEventListener('click', function() {
        this.style.animation = 'none';
        this.style.transform = 'scale(1.1)';

        setTimeout(() => {
            this.style.animation = '';
            this.style.transform = 'scale(1)';
        }, 200);
    });
});

// Auto-rotate demo (optional)
let autoRotate = false;
function startAutoRotate() {
    if (autoRotate) return;
    autoRotate = true;

    const pages = ['404', '500', '403'];
    let index = 0;

    const rotateInterval = setInterval(() => {
        showPage(pages[index]);
        index = (index + 1) % pages.length;
    }, 5000);

    // Stop auto-rotate on user interaction
    document.addEventListener('click', () => {
        clearInterval(rotateInterval);
        autoRotate = false;
    }, { once: true });
}

// Uncomment to enable auto-rotation
// setTimeout(startAutoRotate, 3000);
