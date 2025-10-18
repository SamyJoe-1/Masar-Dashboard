function renderPreview() {
    collectData();

    const container = document.getElementById('cvPreviewContainer');
    container.innerHTML = '';

    const primaryColor = cvData.customize.color;
    const fontFamily = cvData.customize.font_family;
    const fontSize = cvData.customize.font_size;
    const lineHeight = cvData.customize.spacing;

    const page = createA4Page(primaryColor, fontFamily, fontSize, lineHeight);
    container.appendChild(page);

    const sidebar = page.querySelector('.cv-sidebar');
    const main = page.querySelector('.cv-main');

    renderSidebar(sidebar, primaryColor);
    renderMainContent(main, primaryColor);

    handlePageOverflow(container, primaryColor, fontFamily, fontSize, lineHeight);

    setTimeout(() => {
        currentPreviewPage = 0;
        updatePageSlider();
        scaleCV();
    }, 300);
}

function createA4Page(color, font, size, spacing) {
    const page = document.createElement('div');
    page.className = 'cv-page';
    page.style.cssText = `
        width: ${A4_WIDTH}px;
        height: ${A4_HEIGHT}px;
        background: white;
        display: flex;
        overflow: hidden;
        position: relative;
        font-family: ${font};
        font-size: ${size}px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        margin-bottom: 20px;
    `;

    const sidebar = document.createElement('div');
    sidebar.className = 'cv-sidebar';
    sidebar.style.cssText = `
        width: 30%;
        background: ${color};
        color: white;
        padding: 40px 25px;
        overflow: hidden;
    `;

    const main = document.createElement('div');
    main.className = 'cv-main';
    main.style.cssText = `
        width: 70%;
        padding: 55px 30px;
        background: white;
        overflow: hidden;
        line-height: ${spacing};
    `;

    page.appendChild(sidebar);
    page.appendChild(main);

    return page;
}

function renderSidebar(sidebar, color) {
    const pd = cvData.personal_details;

    const avatar = document.createElement('div');
    avatar.className = 'cv-avatar';
    avatar.style.cssText = `
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: ${pd.avatar ? `url(${pd.avatar}) center/cover` : 'rgba(255,255,255,0.2)'};
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
`;
    avatar.innerHTML = pd.avatar ? '' : `<i class="fas fa-user"></i>`;
    sidebar.appendChild(avatar);

    const name = document.createElement('div');
    name.id = 'cvName';
    name.style.cssText = 'font-size: 1.5rem; font-weight: 700; margin-bottom: 5px; text-align: center;';
    name.textContent = `${pd.first_name || ''} ${pd.last_name || ''}`.trim() || __('your_name');
    sidebar.appendChild(name);

    const jobTitle = document.createElement('div');
    jobTitle.id = 'cvJobTitle';
    jobTitle.style.cssText = 'text-align: center; opacity: 0.9; margin-bottom: 30px; font-size: 0.95rem;';
    jobTitle.textContent = pd.job_title || __('your_job_title');
    sidebar.appendChild(jobTitle);

    if (pd.email || pd.phone || pd.city_state) {
        const contactSection = document.createElement('div');
        contactSection.className = 'cv-section';
        contactSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3);">${__('contact')}</div>
            <div id="cvEmail" style="margin-bottom: 10px; font-size: 0.85rem;">${pd.email ? `<i class="fas fa-envelope" style="width: 20px;"></i> ${pd.email}` : ''}</div>
            <div id="cvPhone" style="margin-bottom: 10px; font-size: 0.85rem;">${pd.phone ? `<i class="fas fa-phone" style="width: 20px;"></i> ${pd.phone}` : ''}</div>
            <div id="cvAddress" style="margin-bottom: 10px; font-size: 0.85rem;">${pd.city_state && pd.country ? `<i class="fas fa-map-marker-alt" style="width: 20px;"></i> ${pd.city_state}, ${pd.country}` : ''}</div>
        `;
        sidebar.appendChild(contactSection);
    }

    if (cvData.skills && cvData.skills.length > 0) {
        const skillsSection = document.createElement('div');
        skillsSection.className = 'cv-section';
        skillsSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3);">${__('skills')}</div>
            <div id="cvSkills"></div>
        `;
        sidebar.appendChild(skillsSection);

        const skillsContainer = skillsSection.querySelector('#cvSkills');
        cvData.skills.forEach(skill => {
            if (skill.skill) {
                const skillDiv = document.createElement('div');
                skillDiv.style.cssText = 'margin-bottom: 12px;';
                skillDiv.innerHTML = `
                    <div style="font-size: 0.9rem; margin-bottom: 3px;">${skill.skill}</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">${skill.level || __('experienced')}</div>
                `;
                skillsContainer.appendChild(skillDiv);
            }
        });
    }

    if (cvData.additional_sections.languages && cvData.additional_sections.languages.length > 0) {
        const langSection = document.createElement('div');
        langSection.className = 'cv-section';
        langSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3);">${__('languages')}</div>
            <div id="cvLanguages"></div>
        `;
        sidebar.appendChild(langSection);

        const langContainer = langSection.querySelector('#cvLanguages');
        cvData.additional_sections.languages.forEach(lang => {
            if (lang.language && lang.level && lang.level !== 'Select level') {
                const langDiv = document.createElement('div');
                langDiv.style.cssText = 'margin-bottom: 12px;';
                langDiv.innerHTML = `
                    <div style="font-size: 0.9rem; margin-bottom: 3px;">${lang.language}</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">${lang.level}</div>
                `;
                langContainer.appendChild(langDiv);
            }
        });
    }
}

function renderMainContent(main, primaryColor) {
    stylesSectionTitle = `font-size: 12pt; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; padding-bottom: 0.3pt; border-bottom: 3px solid ${primaryColor}; color: ${primaryColor};`;
    stylesSectionContent = `font-size: 10pt; line-height: 1.35;color: #333;font-family: 'helvetica'`;
    stylesSectionContentSm = `font-size: 10pt; color: #333;font-family: 'helvetica'`;
    if (cvData.summary && cvData.summary.trim()) {
        const summarySection = document.createElement('div');
        summarySection.className = 'cv-section';
        const processedSummary = cvData.summary
        summarySection.innerHTML = `
            <div class="cv-section-title" style="${stylesSectionTitle}">${__('professional_summary')}</div>
            <div id="cvSummary" style="${stylesSectionContent}">${processedSummary}</div>
        `;
        main.appendChild(summarySection);
    }

    if (cvData.employment_history && cvData.employment_history.length > 0) {
        const empSection = document.createElement('div');
        empSection.className = 'cv-section';
        empSection.innerHTML = `
        <div class="cv-section-title" style="${stylesSectionTitle}">${__('experience')}</div>
        <div id="cvEmployment"></div>
    `;
        main.appendChild(empSection);

        const empContainer = empSection.querySelector('#cvEmployment');
        cvData.employment_history.forEach(emp => {
            if (emp.job_title || emp.company) {
                const empDiv = document.createElement('div');
                empDiv.style.cssText = 'margin-bottom: 20px;';
                empDiv.innerHTML = `
                <div style="font-weight: 600; color: #222; font-size: 1rem;">${emp.job_title || __('position')}</div>
                <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">
                    ${emp.company || __('company')} ${emp.city ? `• ${emp.city}` : ''}
                </div>
                <div style="color: #888; font-size: 0.85rem; margin-bottom: 8px;">
                    ${formatDate(emp.start_date)} - ${emp.end_date ? formatDate(emp.end_date) : __('present')}
                </div>
                <div style="${stylesSectionContentSm}">
                    ${emp.description || ''}
                </div>
            `;
                empContainer.appendChild(empDiv);
            }
        });
    }

    if (cvData.education && cvData.education.length > 0) {
        const eduSection = document.createElement('div');
        eduSection.className = 'cv-section';
        eduSection.innerHTML = `
            <div class="cv-section-title" style="${stylesSectionTitle}">${__('education')}</div>
            <div id="cvEducation"></div>
        `;
        main.appendChild(eduSection);

        const eduContainer = eduSection.querySelector('#cvEducation');
        cvData.education.forEach(edu => {
            if (edu.school || edu.degree) {
                const eduDiv = document.createElement('div');
                eduDiv.style.cssText = 'margin-bottom: 20px;';
                eduDiv.innerHTML = `
                    <div style="font-weight: 600; color: #222; font-size: 1rem;">${edu.degree || __('degree')}</div>
                    <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">
                        ${edu.school || __('school')} ${edu.city ? `• ${edu.city}` : ''}
                    </div>
                    <div style="color: #888; font-size: 0.85rem; margin-bottom: 8px;">
                        ${formatDate(edu.start_date)} - ${formatDate(edu.end_date)}
                    </div>
                    <div style="${stylesSectionContentSm}">
                        ${edu.description || ''}
                    </div>
                `;
                eduContainer.appendChild(eduDiv);
            }
        });
    }

    if (cvData.additional_sections.courses && cvData.additional_sections.courses.length > 0) {
        const coursesSection = document.createElement('div');
        coursesSection.className = 'cv-section';
        coursesSection.innerHTML = `
            <div class="cv-section-title" style="${stylesSectionTitle}">${__('courses')}</div>
            <div id="cvCourses"></div>
        `;
        main.appendChild(coursesSection);

        const coursesContainer = coursesSection.querySelector('#cvCourses');
        cvData.additional_sections.courses.forEach(course => {
            if (course.course || course.institution) {
                const courseDiv = document.createElement('div');
                courseDiv.style.cssText = 'margin-bottom: 15px;';
                courseDiv.innerHTML = `
                    <div style="font-weight: 600; color: #222; font-size: 0.95rem;">${course.course || __('course')}</div>
                    <div style="color: #666; font-size: 0.85rem;">${course.institution || __('institution')}</div>
                    <div style="color: #888; font-size: 0.8rem;">
                        ${formatDate(course.start_date)} - ${formatDate(course.end_date)}
                    </div>
                `;
                coursesContainer.appendChild(courseDiv);
            }
        });
    }

    if (cvData.additional_sections.hobbies && cvData.additional_sections.hobbies.trim()) {
        const hobbiesSection = document.createElement('div');
        hobbiesSection.className = 'cv-section';
        hobbiesSection.innerHTML = `
            <div class="cv-section-title" style="${stylesSectionTitle}">${__('hobbies')}</div>
            <div style="${stylesSectionContent}">${cvData.additional_sections.hobbies}</div>
        `;
        main.appendChild(hobbiesSection);
    }
}

function handlePageOverflow(container, color, font, size, spacing) {
    let pageIndex = 0;
    let iterations = 0;
    const maxIterations = 100;

    while (pageIndex < 20 && iterations < maxIterations) {
        iterations++;

        const pages = container.querySelectorAll('.cv-page');
        if (pageIndex >= pages.length) break;

        const currentPage = pages[pageIndex];
        const mainContent = currentPage.querySelector('.cv-main');

        // Check if page overflows
        const overflowAmount = mainContent.scrollHeight - mainContent.clientHeight;

        // If no overflow (with 50px tolerance), move to next page
        if (overflowAmount <= 50) {
            pageIndex++;
            continue;
        }

        // Page overflows - need to move content
        const sections = Array.from(mainContent.children);
        if (sections.length === 0) {
            pageIndex++;
            continue;
        }

        // Get or create next page
        let nextPage = pages[pageIndex + 1];
        if (!nextPage) {
            nextPage = createA4Page(color, font, size, spacing);
            container.appendChild(nextPage);
            nextPage.querySelector('.cv-sidebar').innerHTML = '';
            currentPages++;
        }

        const nextMain = nextPage.querySelector('.cv-main');

        // Get the last section
        const lastSection = sections[sections.length - 1];

        // Check if this section has items (employment, education, courses)
        const itemsContainer = lastSection.querySelector('#cvEmployment, #cvEducation, #cvCourses');

        if (itemsContainer && itemsContainer.children.length > 0) {
            // Section with multiple items - move items one by one
            const items = Array.from(itemsContainer.children);

            // Get the LAST item
            const lastItem = items[items.length - 1];

            // Find or create matching section on next page
            let nextItemsContainer = nextMain.querySelector(`#${itemsContainer.id}`);

            if (!nextItemsContainer) {
                // Create new section with same structure
                const newSection = document.createElement('div');
                newSection.className = 'cv-section';

                // Clone the section title
                const title = lastSection.querySelector('.cv-section-title');
                if (title) {
                    newSection.appendChild(title.cloneNode(true));
                }

                // Create empty items container
                const newItemsContainer = document.createElement('div');
                newItemsContainer.id = itemsContainer.id;
                newSection.appendChild(newItemsContainer);

                nextMain.insertBefore(newSection, nextMain.firstChild);
                nextItemsContainer = newItemsContainer;
            }

            // Move the last item to next page (at the top)
            nextItemsContainer.insertBefore(lastItem, nextItemsContainer.firstChild);

            // If container is now empty, remove the section
            if (itemsContainer.children.length === 0) {
                lastSection.remove();
            }

            // DON'T increment pageIndex - check current page again

        } else {
            // Section without items (summary, hobbies) OR non-splittable content
            // Move entire section to next page
            nextMain.insertBefore(lastSection, nextMain.firstChild);

            // DON'T increment pageIndex - check current page again
        }
    }

    // Safety check - if we hit max iterations, log warning
    if (iterations >= maxIterations) {
        console.warn('⚠️ handlePageOverflow hit max iterations - possible infinite loop prevented');
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString + '-01');
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
}

function openCustomizeModal() {
    document.getElementById('customizeModal').classList.add('show');
}

function closeCustomizeModal() {
    document.getElementById('customizeModal').classList.remove('show');
}

function changeColor(color) {
    document.querySelectorAll('.color-option').forEach(option => {
        option.classList.remove('selected');
    });
    event.target.classList.add('selected');
    cvData.customize.color = color;
}

function changeFont(fontFamily) {
    cvData.customize.font_family = fontFamily;
    document.getElementById('fontSizeValue').parentElement.previousElementSibling.value = fontFamily;
}

function changeFontSize(size) {
    cvData.customize.font_size = parseInt(size);
    document.getElementById('fontSizeValue').textContent = size + 'px';
}

function changeSpacing(spacing) {
    cvData.customize.spacing = parseFloat(spacing);
    document.getElementById('spacingValue').textContent = spacing;
}

function applyCustomization() {
    closeCustomizeModal();
    renderPreview();
}

function initializeColorPicker(colors) {
    const picker = document.getElementById('colorPicker');
    picker.innerHTML = '';

    colors.forEach((color, index) => {
        const option = document.createElement('div');
        option.className = 'color-option' + (index === 0 ? ' selected' : '');
        option.style.background = color;
        option.onclick = () => changeColor(color);
        picker.appendChild(option);
    });
}

function changePreviewPage(direction) {
    const container = document.getElementById('cvPreviewContainer');
    const totalPages = container.querySelectorAll('.cv-page').length;

    if (direction === 'prev' && currentPreviewPage > 0) currentPreviewPage--;
    else if (direction === 'next' && currentPreviewPage < totalPages - 1) currentPreviewPage++;

    updatePageSlider();
}
