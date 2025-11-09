/**
 * CV Pagination Handler - FIXED VERSION
 * Properly splits CV content into A4 pages matching Browsershot output
 */

const CVPagination = {
    // A4 dimensions in MM - matches Browsershot exactly
    A4_WIDTH_MM: 210,
    A4_HEIGHT_MM: 297,
    PAGE_PADDING_MM: 20,
    CONTENT_HEIGHT_MM: 257, // 297 - (20 * 2)

    swiper: null,
    swiperModal: null,
    currentPages: [],

    /**
     * Initialize pagination system
     */
    init() {
        console.log('CVPagination initialized');
        console.log(`A4 Size: ${this.A4_WIDTH_MM}mm x ${this.A4_HEIGHT_MM}mm`);
        console.log(`Content Area: ${this.A4_WIDTH_MM}mm x ${this.CONTENT_HEIGHT_MM}mm`);
    },

    /**
     * Convert MM to PX for measurement (96 DPI)
     */
    mmToPx(mm) {
        return (mm * 96) / 25.4;
    },

    /**
     * Paginate CV content into multiple A4 pages
     * @param {string} htmlContent - The CV HTML content
     * @returns {Array} Array of page HTMLs
     */
    paginateContent(htmlContent) {
        console.log('Starting pagination...');

        const helper = document.getElementById('paginationHelper');
        if (!helper) {
            console.error('Pagination helper not found!');
            return [htmlContent];
        }

        // Create a temporary container with exact A4 dimensions
// Create a temporary container with exact A4 dimensions
        helper.innerHTML = `
            <div class="cv-content" style="
                width: ${this.A4_WIDTH_MM}mm !important;
                height: ${this.A4_HEIGHT_MM}mm !important;
                min-height: ${this.A4_HEIGHT_MM}mm !important;
                max-height: ${this.A4_HEIGHT_MM}mm !important;
                padding: ${this.PAGE_PADDING_MM}mm !important;
                box-sizing: border-box !important;
                overflow: visible;
                position: relative;
                margin: 0;
            ">${htmlContent}</div>
        `;

        const content = helper.querySelector('.cv-content');
        const maxContentHeight = this.mmToPx(this.CONTENT_HEIGHT_MM);

        console.log(`Max content height: ${maxContentHeight}px (${this.CONTENT_HEIGHT_MM}mm)`);

        const pages = [];
        let currentPage = [];
        let currentHeight = 0;

        const children = Array.from(content.children);
        console.log(`Total elements to paginate: ${children.length}`);

        for (let i = 0; i < children.length; i++) {
            const element = children[i].cloneNode(true);

            // Measure element height
// Measure element height
            const testDiv = document.createElement('div');
            testDiv.className = 'cv-content';
            testDiv.style.cssText = `
                width: ${this.A4_WIDTH_MM}mm !important;
                height: auto !important;
                padding: ${this.PAGE_PADDING_MM}mm !important;
                box-sizing: border-box !important;
                position: absolute;
                top: -99999px;
                left: -99999px;
                visibility: hidden;
                overflow: visible;
            `;
            testDiv.appendChild(element.cloneNode(true));
            document.body.appendChild(testDiv);

            const elementHeight = testDiv.offsetHeight;
            document.body.removeChild(testDiv);

            // Check if adding this element exceeds page height
            if (currentHeight + elementHeight > maxContentHeight && currentPage.length > 0) {
                // Save current page
                const pageHtml = currentPage.map(el => el.outerHTML).join('');
                pages.push(pageHtml);
                console.log(`Page ${pages.length} created with ${currentPage.length} elements`);

                // Start new page
                currentPage = [element];
                currentHeight = elementHeight;
            } else {
                currentPage.push(element);
                currentHeight += elementHeight;
            }
        }

        // Add remaining content as last page
        if (currentPage.length > 0) {
            const pageHtml = currentPage.map(el => el.outerHTML).join('');
            pages.push(pageHtml);
            console.log(`Page ${pages.length} created with ${currentPage.length} elements (final page)`);
        }

        // Clean up
        helper.innerHTML = '';

        console.log(`Pagination complete: ${pages.length} pages created`);
        this.currentPages = pages;

        return pages.length > 0 ? pages : [htmlContent];
    },

    /**
     * Render paginated CV with Swiper
     * @param {string} htmlContent - The CV HTML content
     */
    renderPaginatedCV(htmlContent) {
        console.log('Rendering paginated CV...');

        const pages = this.paginateContent(htmlContent);

        // Render to main preview
        this.renderToContainer('cvPreview', pages);
        this.updatePageCount(pages.length);

        // Destroy old swiper if exists
        if (this.swiper) {
            this.swiper.destroy(true, true);
            this.swiper = null;
        }

        // Wait a tick for DOM to update
        setTimeout(() => {
            this.initSwiper();
        }, 100);

        return pages;
    },

    /**
     * Render pages to a container
     * @param {string} containerId - Container element ID
     * @param {Array} pages - Array of page HTMLs
     */
    renderToContainer(containerId, pages) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container ${containerId} not found!`);
            return;
        }

        container.innerHTML = '';

        pages.forEach((pageContent, index) => {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';

            const page = document.createElement('div');
            page.className = 'a4-page';
            page.style.cssText = 'width: 210mm !important; height: 297mm !important;';
            const content = document.createElement('div');
            content.className = 'cv-content';
            content.style.cssText = 'width: 210mm !important; height: 297mm !important; padding: 20mm !important;';
            content.innerHTML = pageContent;
            page.appendChild(content);

            slide.appendChild(page);
            container.appendChild(slide);
        });

        console.log(`Rendered ${pages.length} pages to ${containerId}`);
    },

    /**
     * Initialize Swiper instances
     */
    initSwiper() {
        console.log('Initializing Swiper...');

        // Check if Swiper is available
        if (typeof Swiper === 'undefined') {
            console.error('Swiper library not loaded!');
            return;
        }

        // Main preview swiper
        try {
            this.swiper = new Swiper('.a4-swiper', {
                slidesPerView: 1,
                spaceBetween: 0,
                navigation: {
                    nextEl: '#nextPage',
                    prevEl: '#prevPage',
                },
                on: {
                    slideChange: (swiper) => {
                        const current = swiper.activeIndex + 1;
                        document.getElementById('currentPage').textContent = current;
                        console.log(`Moved to page ${current}`);
                    },
                    init: (swiper) => {
                        document.getElementById('currentPage').textContent = 1;
                        console.log('Swiper initialized');
                    }
                }
            });
        } catch (error) {
            console.error('Failed to initialize Swiper:', error);
        }
    },

    /**
     * Initialize modal swiper with current pages
     */
    initModalSwiper() {
        console.log('Initializing modal swiper...');

        if (this.currentPages.length === 0) {
            console.warn('No pages to show in modal');
            return;
        }

        // Destroy existing modal swiper
        if (this.swiperModal) {
            this.swiperModal.destroy(true, true);
            this.swiperModal = null;
        }

        // Render to modal container
        this.renderToContainer('fullscreenPreview', this.currentPages);

        // Wait for DOM update
        setTimeout(() => {
            try {
                this.swiperModal = new Swiper('.a4-swiper-modal', {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    navigation: {
                        nextEl: '#nextPageModal',
                        prevEl: '#prevPageModal',
                    },
                    on: {
                        slideChange: (swiper) => {
                            const current = swiper.activeIndex + 1;
                            document.getElementById('currentPageModal').textContent = current;
                        },
                        init: (swiper) => {
                            document.getElementById('currentPageModal').textContent = 1;
                            console.log('Modal swiper initialized');
                        }
                    }
                });
            } catch (error) {
                console.error('Failed to initialize modal swiper:', error);
            }
        }, 100);
    },

    /**
     * Update page count display
     * @param {number} totalPages - Total number of pages
     */
    updatePageCount(totalPages) {
        const elements = [
            'totalPages',
            'totalPagesModal'
        ];

        elements.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = totalPages;
            }
        });

        // Reset current page to 1
        const currentElements = ['currentPage', 'currentPageModal'];
        currentElements.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = 1;
            }
        });

        // Enable/disable navigation buttons
        const hasMultiplePages = totalPages > 1;
        const nextButtons = ['nextPage', 'nextPageModal'];
        const prevButtons = ['prevPage', 'prevPageModal'];

        nextButtons.forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.disabled = !hasMultiplePages;
            }
        });

        prevButtons.forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.disabled = true;
            }
        });

        console.log(`Page count updated: ${totalPages} pages`);
    },

    /**
     * Get all pages for download/export
     * @returns {Array} Array of page HTMLs
     */
    getAllPages() {
        return this.currentPages;
    }
};

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        CVPagination.init();
    });
} else {
    CVPagination.init();
}

// Make it globally available
window.CVPagination = CVPagination;

// INTEGRATION FUNCTIONS - Add these to your main.js or use directly

/**
 * Display improved CV - Call this when CV data is ready
 * @param {Object} cvData - CV data with html and improvements
 */
function displayImprovedCV(cvData) {
    console.log('Displaying improved CV...');

    // Hide processing, show results
    document.getElementById('processingSection').classList.add('d-none');
    document.getElementById('resultsSection').classList.remove('d-none');

    // Store the raw CV data
    window.improvedCVData = cvData;

    // Paginate and render the CV
    const pages = CVPagination.renderPaginatedCV(cvData.html);

    // Display improvements if function exists
    if (typeof displayImprovements === 'function') {
        displayImprovements(cvData.improvements);
    }

    console.log('CV display complete');
}

/**
 * Toggle fullscreen preview
 */
function togglePreview() {
    const modal = document.getElementById('fullscreenModal');

    if (!modal) {
        console.error('Fullscreen modal not found!');
        return;
    }

    if (modal.classList.contains('d-none')) {
        console.log('Opening fullscreen preview...');

        // Show modal
        modal.classList.remove('d-none');

        // Initialize modal swiper with current pages
        CVPagination.initModalSwiper();

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    } else {
        console.log('Closing fullscreen preview...');

        // Hide modal
        modal.classList.add('d-none');

        // Restore body scroll
        document.body.style.overflow = '';
    }
}

// Make functions globally available
window.displayImprovedCV = displayImprovedCV;
window.togglePreview = togglePreview;

console.log('CVPagination module loaded successfully');
