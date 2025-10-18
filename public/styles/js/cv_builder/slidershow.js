function updatePageSlider() {
    const container = document.getElementById('cvPreviewContainer');
    const pages = Array.from(container.querySelectorAll('.cv-page'));
    const totalPages = pages.length;

    const arrows = document.getElementById('cvPageArrows');
    const dotsContainer = document.getElementById('cvPageDots');
    const prevArrow = document.getElementById('cvPrevArrow');
    const nextArrow = document.getElementById('cvNextArrow');

    if (totalPages <= 1) {
        arrows.style.display = 'none';
        dotsContainer.style.display = 'none';
        if (pages[0]) pages[0].classList.add('active');
        return;
    }

    arrows.style.display = 'flex';
    dotsContainer.style.display = 'flex';

    prevArrow.disabled = currentPreviewPage === 0;
    nextArrow.disabled = currentPreviewPage === totalPages - 1;

    dotsContainer.innerHTML = '';
    for (let i = 0; i < totalPages; i++) {
        const dot = document.createElement('div');
        dot.className = 'cv-page-dot' + (i === currentPreviewPage ? ' active' : '');
        dot.onclick = () => goToPreviewPage(i);
        dotsContainer.appendChild(dot);
    }

    // 🔥 Re-ordering logic using flex order
    pages.forEach((page, index) => {
        if (index < currentPreviewPage) {
            page.style.order = totalPages + index;   // move previous pages to the end
        } else {
            page.style.order = index - currentPreviewPage; // keep current first
        }
        page.classList.toggle('active', index === currentPreviewPage);
    });

    console.log('currentPreviewPage:', currentPreviewPage, 'total pages:', totalPages);
}
function changePreviewPage(direction) {
    const container = document.getElementById('cvPreviewContainer');
    const totalPages = container.querySelectorAll('.cv-page').length;

    if (direction === 'prev' && currentPreviewPage > 0) {
        currentPreviewPage--;
    } else if (direction === 'next' && currentPreviewPage < totalPages - 1) {
        currentPreviewPage++;
    }

    updatePageSlider();
}
function nextPreviewPage() {
    goToPreviewPage(currentPreviewPage + 1);
}
function prevPreviewPage() {
    goToPreviewPage(currentPreviewPage - 1);
}

function goToPreviewPage(index) {
    const container = document.getElementById('cvPreviewContainer');
    const pages = container.querySelectorAll('.cv-page');
    const totalPages = pages.length;
    if (index < 0 || index >= totalPages) return;
    currentPreviewPage = index;
    updatePageSlider();
}
