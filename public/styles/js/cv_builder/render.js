async function downloadPDF() {
    const container = document.getElementById('cvPreviewContainer');
    const { jsPDF } = window.jspdf;

    document.getElementById('loadingOverlay').classList.add('show');

    try {
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true
        });

        const sidebarColor = cvData.customize.color;
        const rgb = hexToRgb(sidebarColor);
        const fontFamily = 'helvetica'; // Closest to Inter
        const baseFontSize = cvData.customize.font_size || 14;
        const lineSpacing = cvData.customize.spacing || 1.5;

        // Sidebar background (30% width = 63mm)
        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
        pdf.rect(0, 0, 63, 297, 'F');

        // ========== SIDEBAR ==========
        pdf.setTextColor(255, 255, 255);
        let sidebarY = 40;

        // Avatar
// Avatar
        if (cvData.personal_details.avatar) {
            try {
                const x = 16.5;
                const y = 13.5;
                const size = 30;

                const img = cvData.personal_details.avatar;

                const image = new Image();
                image.crossOrigin = 'Anonymous';
                image.src = img;
                await new Promise((resolve, reject) => {
                    image.onload = resolve;
                    image.onerror = reject;
                });

                const canvas = document.createElement('canvas');
                canvas.width = size * 4;
                canvas.height = size * 4;
                const ctx = canvas.getContext('2d');

                // clear the canvas (ensures transparency)
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // draw circular clip
                ctx.beginPath();
                ctx.arc(canvas.width / 2, canvas.height / 2, canvas.width / 2, 0, Math.PI * 2);
                ctx.closePath();
                ctx.clip();

                ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

                // ✅ Export as PNG (preserves transparency)
                const roundedImg = canvas.toDataURL('image/png');

                pdf.addImage(roundedImg, 'PNG', x, y, size, size, undefined, 'FAST');
                sidebarY = 55;
            } catch (e) {
                console.log('Could not add avatar');
            }
        } else {
            sidebarY = 30;
        }



        // Name - font-size: 1.5rem (24px preview) = 18pt PDF
        pdf.setFont(fontFamily, 'bold');
        pdf.setFontSize(18);
        const nameText = `${cvData.personal_details.first_name || ''} ${cvData.personal_details.last_name || ''}`.trim();
        if (nameText) {
            const nameLines = pdf.splitTextToSize(nameText, 55);
            nameLines.forEach(line => {
                pdf.text(line, 31.5, sidebarY, { align: 'center' });
                sidebarY += 6;
            });
        }

        // Job Title - font-size: 0.95rem (15px preview) = 11pt PDF
        if (cvData.personal_details.job_title) {
            pdf.setFont(fontFamily, 'normal');
            pdf.setFontSize(11);
            const jobLines = pdf.splitTextToSize(cvData.personal_details.job_title, 55);
            jobLines.forEach(line => {
                pdf.text(line, 31.5, sidebarY, { align: 'center' });
                sidebarY += 5;
            });
        }

        sidebarY += 10;

        // Contact Section
        if (cvData.personal_details.email || cvData.personal_details.phone || cvData.personal_details.city_state) {
            // Section title - font-size: 1rem (16px) = 12pt, uppercase, letter-spacing: 1px
            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(12);
            pdf.text('CONTACT', 10, sidebarY);

            // Border line - border-bottom: 2px
            pdf.setDrawColor(255, 255, 255);
            pdf.setLineWidth(0.5);
            pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
            sidebarY += 10;

            pdf.setFont(fontFamily, 'normal');
            pdf.setFontSize(9);

            // Email with icon
            if (cvData.personal_details.email) {
                try {
                    const emailIcon = getIconAsBase64('email');
                    pdf.addImage(emailIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                } catch(e) {}

                const emailLines = pdf.splitTextToSize(cvData.personal_details.email, 40);
                pdf.text(emailLines, 14, sidebarY);
                sidebarY += (emailLines.length * 4) + 2;
            }

            // Phone with icon
            if (cvData.personal_details.phone) {
                try {
                    const phoneIcon = getIconAsBase64('phone');
                    pdf.addImage(phoneIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                } catch(e) {}

                pdf.text(cvData.personal_details.phone, 14, sidebarY);
                sidebarY += 6;
            }

            // Address with icon
            if (cvData.personal_details.city_state && cvData.personal_details.country) {
                try {
                    const locationIcon = getIconAsBase64('location');
                    pdf.addImage(locationIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                } catch(e) {}

                const addressText = `${cvData.personal_details.city_state}, ${cvData.personal_details.country}`;
                const addressLines = pdf.splitTextToSize(addressText, 40);
                pdf.text(addressLines, 14, sidebarY);
                sidebarY += (addressLines.length * 4) + 2;
            }

            sidebarY += 8;
        }

        // Skills Section
        if (cvData.skills && cvData.skills.length > 0 && cvData.skills.some(s => s.skill)) {
            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(12);
            pdf.text('SKILLS', 10, sidebarY);
            pdf.setLineWidth(0.5);
            pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
            sidebarY += 10;

            cvData.skills.forEach(skill => {
                if (skill.skill && sidebarY < 275) {
                    // Skill name - font-size: 0.9rem = 10pt, bold
                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(10);
                    const skillLines = pdf.splitTextToSize(skill.skill, 43);
                    pdf.text(skillLines, 10, sidebarY);
                    sidebarY += (skillLines.length * 4.5);

                    // Skill level - font-size: 0.75rem = 8pt, normal
                    if (skill.level) {
                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(8);
                        pdf.text(skill.level, 10, sidebarY);
                        sidebarY += 5;
                    }
                    sidebarY += 2;
                }
            });

            sidebarY += 5;
        }

        // Languages Section
        if (cvData.additional_sections.languages && cvData.additional_sections.languages.length > 0 &&
            cvData.additional_sections.languages.some(l => l.language && l.level && l.level !== 'Select level')) {

            if (sidebarY < 270) {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.text('LANGUAGES', 10, sidebarY);
                pdf.setLineWidth(0.5);
                pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
                sidebarY += 10;

                cvData.additional_sections.languages.forEach(lang => {
                    if (lang.language && lang.level && lang.level !== 'Select level' && sidebarY < 275) {
                        pdf.setFont(fontFamily, 'bold');
                        pdf.setFontSize(10);
                        const langLines = pdf.splitTextToSize(lang.language, 43);
                        pdf.text(langLines, 10, sidebarY);
                        sidebarY += (langLines.length * 4.5);

                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(8);
                        pdf.text(lang.level, 10, sidebarY);
                        sidebarY += 6;
                    }
                });
            }
        }

        // ========== MAIN CONTENT (70% = 147mm width, starts at 63mm) ==========
        pdf.setTextColor(0, 0, 0);
        let mainY = 20;
        const mainX = 70;
        const mainWidth = 130;

        // Professional Summary
        if (cvData.summary && cvData.summary.trim()) {
            // Section title - font-size: 1rem = 12pt, uppercase, bold
            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(12);
            pdf.setTextColor(rgb.r, rgb.g, rgb.b);
            pdf.text('PROFESSIONAL SUMMARY', mainX, mainY);

            pdf.setDrawColor(rgb.r, rgb.g, rgb.b);
            pdf.setLineWidth(0.5);
            pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
            mainY += 9;

            // Content - font-size: 0.9rem = 10pt, line-height: 1.6
            pdf.setFont(fontFamily, 'normal');
            pdf.setFontSize(10);
            pdf.setTextColor(51, 51, 51);

            const summaryText = cvData.summary.replace(/<[^>]*>/g, '\n').trim();
            const summaryLines = pdf.splitTextToSize(summaryText, mainWidth);
            summaryLines.forEach(line => {
                pdf.text(line, mainX, mainY);
                mainY += 5;
            });
            mainY += 10;
        }

        // Employment History
        if (cvData.employment_history && cvData.employment_history.length > 0 &&
            cvData.employment_history.some(e => e.job_title || e.company)) {

            // Helper function to add section title
            const addExperienceTitle = () => {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                pdf.text('EXPERIENCE', mainX, mainY);
                pdf.setLineWidth(0.5);
                pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                mainY += 9;
            };

            addExperienceTitle(); // First time

            cvData.employment_history.forEach(emp => {
                if ((emp.job_title || emp.company)) {
                    // Job Title
                    if (mainY > 270) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addExperienceTitle();
                    }

                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(12);
                    pdf.setTextColor(34, 34, 34);
                    pdf.text(emp.job_title || 'Position', mainX, mainY);
                    mainY += 5;

                    // Company & City
                    if (mainY > 280) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addExperienceTitle();
                    }

                    pdf.setFont(fontFamily, 'normal');
                    pdf.setFontSize(10);
                    pdf.setTextColor(102, 102, 102);
                    let companyText = emp.company || 'Company';
                    if (emp.city) companyText += ` • ${emp.city}`;
                    pdf.text(companyText, mainX, mainY);
                    mainY += 5;

                    // Dates
                    if (mainY > 280) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addExperienceTitle();
                    }

                    pdf.setFontSize(10);
                    pdf.setTextColor(136, 136, 136);
                    const startDate = emp.start_date ? formatDateForPDF(emp.start_date) : '';
                    const endDate = emp.end_date ? formatDateForPDF(emp.end_date) : 'Present';
                    if (startDate || endDate) {
                        pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                        mainY += 5;
                    }

                    // Description - THIS IS THE KEY PART
                    if (emp.description) {
                        if (mainY > 280) {
                            pdf.addPage();
                            pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                            pdf.rect(0, 0, 63, 297, 'F');
                            mainY = 20;
                            addExperienceTitle();
                        }

                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(10);
                        pdf.setTextColor(68, 68, 68);

                        const descText = emp.description
                            .replace(/<\/p>/g, '\n')
                            .replace(/<br\s*\/?>/g, '\n')
                            .replace(/<\/li>/g, '\n')
                            .replace(/<[^>]*>/g, '')
                            .replace(/&nbsp;/g, ' ')
                            .trim();

                        const descLines = pdf.splitTextToSize(descText, mainWidth);
                        descLines.forEach(line => {
                            if (mainY > 280) {
                                pdf.addPage();
                                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                                pdf.rect(0, 0, 63, 297, 'F');
                                mainY = 20;
                                addExperienceTitle();

                                // Keep description formatting after page break
                                pdf.setFont(fontFamily, 'normal');
                                pdf.setFontSize(9);
                                pdf.setTextColor(68, 68, 68);
                            }
                            pdf.text(line, mainX, mainY);
                            mainY += 5;
                        });
                    }

                    mainY += 10;
                }
            });

            mainY += 3;
        }

        // Education
        if (cvData.education && cvData.education.length > 0 &&
            cvData.education.some(e => e.school || e.degree)) {

            // Check if need new page before starting
            if (mainY > 250) {
                pdf.addPage();
                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                pdf.rect(0, 0, 63, 297, 'F');
                mainY = 20;
            }

            // Helper function to add section title
            const addEducationTitle = () => {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                pdf.text('EDUCATION', mainX, mainY);
                pdf.setLineWidth(0.5);
                pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                mainY += 9;
            };

            addEducationTitle(); // First time

            cvData.education.forEach(edu => {
                if ((edu.school || edu.degree) && mainY < 270) {
                    // Check if need new page BEFORE adding content
                    if (mainY > 250) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addEducationTitle(); // Add title again
                    }

                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(11);
                    pdf.setTextColor(34, 34, 34);
                    pdf.text(edu.degree || 'Degree', mainX, mainY);
                    mainY += 5;

                    pdf.setFont(fontFamily, 'normal');
                    pdf.setFontSize(10);
                    pdf.setTextColor(102, 102, 102);
                    let schoolText = edu.school || 'School';
                    if (edu.city) schoolText += ` • ${edu.city}`;
                    pdf.text(schoolText, mainX, mainY);
                    mainY += 5;

                    pdf.setFontSize(9);
                    pdf.setTextColor(136, 136, 136);
                    const startDate = edu.start_date ? formatDateForPDF(edu.start_date) : '';
                    const endDate = edu.end_date ? formatDateForPDF(edu.end_date) : '';
                    if (startDate || endDate) {
                        pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                        mainY += 5;
                    }

                    if (edu.description) {
                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(10);
                        pdf.setTextColor(68, 68, 68);

                        const descText = edu.description
                            .replace(/<\/p>/g, '\n')
                            .replace(/<br\s*\/?>/g, '\n')
                            .replace(/<\/li>/g, '\n')
                            .replace(/<[^>]*>/g, '')
                            .replace(/&nbsp;/g, ' ')
                            .trim();

                        const descLines = pdf.splitTextToSize(descText, mainWidth);
                        descLines.forEach(line => {
                            if (mainY > 280) {
                                pdf.addPage();
                                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                                pdf.rect(0, 0, 63, 297, 'F');
                                mainY = 20;
                                addEducationTitle(); // Add title on overflow
                            }
                            pdf.text(line, mainX, mainY);
                            mainY += 5;
                        });
                    }

                    mainY += 10;
                }
            });

            mainY += 3;
        }

        // Courses
        if (cvData.additional_sections.courses && cvData.additional_sections.courses.length > 0 &&
            cvData.additional_sections.courses.some(c => c.course || c.institution)) {

            if (mainY > 250) {
                pdf.addPage();
                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                pdf.rect(0, 0, 63, 297, 'F');
                mainY = 20;
            }

            const addCoursesTitle = () => {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                pdf.text('COURSES', mainX, mainY);
                pdf.setLineWidth(0.5);
                pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                mainY += 9;
            };

            addCoursesTitle();

            cvData.additional_sections.courses.forEach(course => {
                if ((course.course || course.institution) && mainY < 270) {
                    if (mainY > 260) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addCoursesTitle();
                    }

                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(10);
                    pdf.setTextColor(34, 34, 34);
                    pdf.text(course.course || 'Course', mainX, mainY);
                    mainY += 5;

                    if (course.institution) {
                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(9);
                        pdf.setTextColor(102, 102, 102);
                        pdf.text(course.institution, mainX, mainY);
                        mainY += 4;
                    }

                    const startDate = course.start_date ? formatDateForPDF(course.start_date) : '';
                    const endDate = course.end_date ? formatDateForPDF(course.end_date) : '';
                    if (startDate || endDate) {
                        pdf.setFontSize(9);
                        pdf.setTextColor(136, 136, 136);
                        pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                        mainY += 5;
                    }

                    mainY += 3;
                }
            });

            mainY += 3;
        }

        // Hobbies
        if (cvData.additional_sections.hobbies && cvData.additional_sections.hobbies.trim()) {
            if (mainY > 260) {
                pdf.addPage();
                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                pdf.rect(0, 0, 63, 297, 'F');
                mainY = 20;
            }

            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(12);
            pdf.setTextColor(rgb.r, rgb.g, rgb.b);
            pdf.text('HOBBIES', mainX, mainY);
            pdf.setLineWidth(0.5);
            pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
            mainY += 9;

            pdf.setFont(fontFamily, 'normal');
            pdf.setFontSize(9);
            pdf.setTextColor(68, 68, 68);
            const hobbiesLines = pdf.splitTextToSize(cvData.additional_sections.hobbies, mainWidth);
            hobbiesLines.forEach(line => {
                pdf.text(line, mainX, mainY);
                mainY += 5;
            });
        }

        const fileName = `CV_${cvData.personal_details.first_name}_${cvData.personal_details.last_name}.pdf`;
        pdf.save(fileName);

        document.getElementById('loadingOverlay').classList.remove('show');
        showNotification(__('pdf_downloaded_successfully'), 'success');

    } catch (error) {
        console.error('Error generating PDF:', error);
        document.getElementById('loadingOverlay').classList.remove('show');
        showNotification(__('failed_to_generate_pdf'), 'error');
    }
}

async function downloadPDF(file=true) {
    const container = document.getElementById('cvPreviewContainer');
    const { jsPDF } = window.jspdf;

    document.getElementById('loadingOverlay').classList.add('show');

    try {
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true
        });

        const sidebarColor = cvData.customize.color;
        const rgb = hexToRgb(sidebarColor);
        const fontFamily = 'helvetica'; // Closest to Inter
        const baseFontSize = cvData.customize.font_size || 14;
        const lineSpacing = cvData.customize.spacing || 1.5;

        // Sidebar background (30% width = 63mm)
        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
        pdf.rect(0, 0, 63, 297, 'F');

        // ========== SIDEBAR ==========
        pdf.setTextColor(255, 255, 255);
        let sidebarY = 40;

        // Avatar
// Avatar
        if (cvData.personal_details.avatar) {
            try {
                const x = 16.5;
                const y = 13.5;
                const size = 30;

                const img = cvData.personal_details.avatar;

                const image = new Image();
                image.crossOrigin = 'Anonymous';
                image.src = img;
                await new Promise((resolve, reject) => {
                    image.onload = resolve;
                    image.onerror = reject;
                });

                const canvas = document.createElement('canvas');
                canvas.width = size * 4;
                canvas.height = size * 4;
                const ctx = canvas.getContext('2d');

                // clear the canvas (ensures transparency)
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // draw circular clip
                ctx.beginPath();
                ctx.arc(canvas.width / 2, canvas.height / 2, canvas.width / 2, 0, Math.PI * 2);
                ctx.closePath();
                ctx.clip();

                ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

                // ✅ Export as PNG (preserves transparency)
                const roundedImg = canvas.toDataURL('image/png');

                pdf.addImage(roundedImg, 'PNG', x, y, size, size, undefined, 'FAST');
                sidebarY = 55;
            } catch (e) {
                console.log('Could not add avatar');
            }
        } else {
            sidebarY = 30;
        }



        // Name - font-size: 1.5rem (24px preview) = 18pt PDF
        pdf.setFont(fontFamily, 'bold');
        pdf.setFontSize(18);
        const nameText = `${cvData.personal_details.first_name || ''} ${cvData.personal_details.last_name || ''}`.trim();
        if (nameText) {
            const nameLines = pdf.splitTextToSize(nameText, 55);
            nameLines.forEach(line => {
                pdf.text(line, 31.5, sidebarY, { align: 'center' });
                sidebarY += 6;
            });
        }

        // Job Title - font-size: 0.95rem (15px preview) = 11pt PDF
        if (cvData.personal_details.job_title) {
            pdf.setFont(fontFamily, 'normal');
            pdf.setFontSize(11);
            const jobLines = pdf.splitTextToSize(cvData.personal_details.job_title, 55);
            jobLines.forEach(line => {
                pdf.text(line, 31.5, sidebarY, { align: 'center' });
                sidebarY += 5;
            });
        }

        sidebarY += 10;

        // Contact Section
        if (cvData.personal_details.email || cvData.personal_details.phone || cvData.personal_details.city_state) {
            // Section title - font-size: 1rem (16px) = 12pt, uppercase, letter-spacing: 1px
            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(12);
            pdf.text('CONTACT', 10, sidebarY);

            // Border line - border-bottom: 2px
            pdf.setDrawColor(255, 255, 255);
            pdf.setLineWidth(0.5);
            pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
            sidebarY += 10;

            pdf.setFont(fontFamily, 'normal');
            pdf.setFontSize(9);

            // Email with icon
            if (cvData.personal_details.email) {
                try {
                    const emailIcon = getIconAsBase64('email');
                    pdf.addImage(emailIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                } catch(e) {}

                const emailLines = pdf.splitTextToSize(cvData.personal_details.email, 40);
                pdf.text(emailLines, 14, sidebarY);
                sidebarY += (emailLines.length * 4) + 2;
            }

            // Phone with icon
            if (cvData.personal_details.phone) {
                try {
                    const phoneIcon = getIconAsBase64('phone');
                    pdf.addImage(phoneIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                } catch(e) {}

                pdf.text(cvData.personal_details.phone, 14, sidebarY);
                sidebarY += 6;
            }

            // Address with icon
            if (cvData.personal_details.city_state && cvData.personal_details.country) {
                try {
                    const locationIcon = getIconAsBase64('location');
                    pdf.addImage(locationIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                } catch(e) {}

                const addressText = `${cvData.personal_details.city_state}, ${cvData.personal_details.country}`;
                const addressLines = pdf.splitTextToSize(addressText, 40);
                pdf.text(addressLines, 14, sidebarY);
                sidebarY += (addressLines.length * 4) + 2;
            }

            sidebarY += 8;
        }

        // Skills Section
        if (cvData.skills && cvData.skills.length > 0 && cvData.skills.some(s => s.skill)) {
            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(12);
            pdf.text('SKILLS', 10, sidebarY);
            pdf.setLineWidth(0.5);
            pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
            sidebarY += 10;

            cvData.skills.forEach(skill => {
                if (skill.skill && sidebarY < 275) {
                    // Skill name - font-size: 0.9rem = 10pt, bold
                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(10);
                    const skillLines = pdf.splitTextToSize(skill.skill, 43);
                    pdf.text(skillLines, 10, sidebarY);
                    sidebarY += (skillLines.length * 4.5);

                    // Skill level - font-size: 0.75rem = 8pt, normal
                    if (skill.level) {
                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(8);
                        pdf.text(skill.level, 10, sidebarY);
                        sidebarY += 5;
                    }
                    sidebarY += 2;
                }
            });

            sidebarY += 5;
        }

        // Languages Section
        if (cvData.additional_sections.languages && cvData.additional_sections.languages.length > 0 &&
            cvData.additional_sections.languages.some(l => l.language && l.level && l.level !== 'Select level')) {

            if (sidebarY < 270) {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.text('LANGUAGES', 10, sidebarY);
                pdf.setLineWidth(0.5);
                pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
                sidebarY += 10;

                cvData.additional_sections.languages.forEach(lang => {
                    if (lang.language && lang.level && lang.level !== 'Select level' && sidebarY < 275) {
                        pdf.setFont(fontFamily, 'bold');
                        pdf.setFontSize(10);
                        const langLines = pdf.splitTextToSize(lang.language, 43);
                        pdf.text(langLines, 10, sidebarY);
                        sidebarY += (langLines.length * 4.5);

                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(8);
                        pdf.text(lang.level, 10, sidebarY);
                        sidebarY += 6;
                    }
                });
            }
        }

        // ========== MAIN CONTENT (70% = 147mm width, starts at 63mm) ==========
        pdf.setTextColor(0, 0, 0);
        let mainY = 20;
        const mainX = 70;
        const mainWidth = 130;

        // Professional Summary
        if (cvData.summary && cvData.summary.trim()) {
            // Section title - font-size: 1rem = 12pt, uppercase, bold
            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(12);
            pdf.setTextColor(rgb.r, rgb.g, rgb.b);
            pdf.text('PROFESSIONAL SUMMARY', mainX, mainY);

            pdf.setDrawColor(rgb.r, rgb.g, rgb.b);
            pdf.setLineWidth(0.5);
            pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
            mainY += 9;

            // Content - font-size: 0.9rem = 10pt, line-height: 1.6
            pdf.setFont(fontFamily, 'normal');
            pdf.setFontSize(10);
            pdf.setTextColor(51, 51, 51);

            const summaryText = cvData.summary.replace(/<[^>]*>/g, '\n').trim();
            const summaryLines = pdf.splitTextToSize(summaryText, mainWidth);
            summaryLines.forEach(line => {
                pdf.text(line, mainX, mainY);
                mainY += 5;
            });
            mainY += 10;
        }

        // Employment History
        if (cvData.employment_history && cvData.employment_history.length > 0 &&
            cvData.employment_history.some(e => e.job_title || e.company)) {

            // Helper function to add section title
            const addExperienceTitle = () => {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                pdf.text('EXPERIENCE', mainX, mainY);
                pdf.setLineWidth(0.5);
                pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                mainY += 9;
            };

            addExperienceTitle(); // First time

            cvData.employment_history.forEach(emp => {
                if ((emp.job_title || emp.company)) {
                    // Job Title
                    if (mainY > 270) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addExperienceTitle();
                    }

                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(12);
                    pdf.setTextColor(34, 34, 34);
                    pdf.text(emp.job_title || 'Position', mainX, mainY);
                    mainY += 5;

                    // Company & City
                    if (mainY > 280) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addExperienceTitle();
                    }

                    pdf.setFont(fontFamily, 'normal');
                    pdf.setFontSize(10);
                    pdf.setTextColor(102, 102, 102);
                    let companyText = emp.company || 'Company';
                    if (emp.city) companyText += ` • ${emp.city}`;
                    pdf.text(companyText, mainX, mainY);
                    mainY += 5;

                    // Dates
                    if (mainY > 280) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addExperienceTitle();
                    }

                    pdf.setFontSize(10);
                    pdf.setTextColor(136, 136, 136);
                    const startDate = emp.start_date ? formatDateForPDF(emp.start_date) : '';
                    const endDate = emp.end_date ? formatDateForPDF(emp.end_date) : 'Present';
                    if (startDate || endDate) {
                        pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                        mainY += 5;
                    }

                    // Description - THIS IS THE KEY PART
                    if (emp.description) {
                        if (mainY > 280) {
                            pdf.addPage();
                            pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                            pdf.rect(0, 0, 63, 297, 'F');
                            mainY = 20;
                            addExperienceTitle();
                        }

                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(10);
                        pdf.setTextColor(68, 68, 68);

                        const descText = emp.description
                            .replace(/<\/p>/g, '\n')
                            .replace(/<br\s*\/?>/g, '\n')
                            .replace(/<\/li>/g, '\n')
                            .replace(/<[^>]*>/g, '')
                            .replace(/&nbsp;/g, ' ')
                            .trim();

                        const descLines = pdf.splitTextToSize(descText, mainWidth);
                        descLines.forEach(line => {
                            if (mainY > 280) {
                                pdf.addPage();
                                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                                pdf.rect(0, 0, 63, 297, 'F');
                                mainY = 20;
                                addExperienceTitle();

                                // Keep description formatting after page break
                                pdf.setFont(fontFamily, 'normal');
                                pdf.setFontSize(9);
                                pdf.setTextColor(68, 68, 68);
                            }
                            pdf.text(line, mainX, mainY);
                            mainY += 5;
                        });
                    }

                    mainY += 10;
                }
            });

            mainY += 3;
        }

        // Education
        if (cvData.education && cvData.education.length > 0 &&
            cvData.education.some(e => e.school || e.degree)) {

            // Check if need new page before starting
            if (mainY > 250) {
                pdf.addPage();
                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                pdf.rect(0, 0, 63, 297, 'F');
                mainY = 20;
            }

            // Helper function to add section title
            const addEducationTitle = () => {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                pdf.text('EDUCATION', mainX, mainY);
                pdf.setLineWidth(0.5);
                pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                mainY += 9;
            };

            addEducationTitle(); // First time

            cvData.education.forEach(edu => {
                if ((edu.school || edu.degree) && mainY < 270) {
                    // Check if need new page BEFORE adding content
                    if (mainY > 250) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addEducationTitle(); // Add title again
                    }

                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(11);
                    pdf.setTextColor(34, 34, 34);
                    pdf.text(edu.degree || 'Degree', mainX, mainY);
                    mainY += 5;

                    pdf.setFont(fontFamily, 'normal');
                    pdf.setFontSize(10);
                    pdf.setTextColor(102, 102, 102);
                    let schoolText = edu.school || 'School';
                    if (edu.city) schoolText += ` • ${edu.city}`;
                    pdf.text(schoolText, mainX, mainY);
                    mainY += 5;

                    pdf.setFontSize(9);
                    pdf.setTextColor(136, 136, 136);
                    const startDate = edu.start_date ? formatDateForPDF(edu.start_date) : '';
                    const endDate = edu.end_date ? formatDateForPDF(edu.end_date) : '';
                    if (startDate || endDate) {
                        pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                        mainY += 5;
                    }

                    if (edu.description) {
                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(10);
                        pdf.setTextColor(68, 68, 68);

                        const descText = edu.description
                            .replace(/<\/p>/g, '\n')
                            .replace(/<br\s*\/?>/g, '\n')
                            .replace(/<\/li>/g, '\n')
                            .replace(/<[^>]*>/g, '')
                            .replace(/&nbsp;/g, ' ')
                            .trim();

                        const descLines = pdf.splitTextToSize(descText, mainWidth);
                        descLines.forEach(line => {
                            if (mainY > 280) {
                                pdf.addPage();
                                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                                pdf.rect(0, 0, 63, 297, 'F');
                                mainY = 20;
                                addEducationTitle(); // Add title on overflow
                            }
                            pdf.text(line, mainX, mainY);
                            mainY += 5;
                        });
                    }

                    mainY += 10;
                }
            });

            mainY += 3;
        }

        // Courses
        if (cvData.additional_sections.courses && cvData.additional_sections.courses.length > 0 &&
            cvData.additional_sections.courses.some(c => c.course || c.institution)) {

            if (mainY > 250) {
                pdf.addPage();
                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                pdf.rect(0, 0, 63, 297, 'F');
                mainY = 20;
            }

            const addCoursesTitle = () => {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                pdf.text('COURSES', mainX, mainY);
                pdf.setLineWidth(0.5);
                pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                mainY += 9;
            };

            addCoursesTitle();

            cvData.additional_sections.courses.forEach(course => {
                if ((course.course || course.institution) && mainY < 270) {
                    if (mainY > 260) {
                        pdf.addPage();
                        pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                        pdf.rect(0, 0, 63, 297, 'F');
                        mainY = 20;
                        addCoursesTitle();
                    }

                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(10);
                    pdf.setTextColor(34, 34, 34);
                    pdf.text(course.course || 'Course', mainX, mainY);
                    mainY += 5;

                    if (course.institution) {
                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(9);
                        pdf.setTextColor(102, 102, 102);
                        pdf.text(course.institution, mainX, mainY);
                        mainY += 4;
                    }

                    const startDate = course.start_date ? formatDateForPDF(course.start_date) : '';
                    const endDate = course.end_date ? formatDateForPDF(course.end_date) : '';
                    if (startDate || endDate) {
                        pdf.setFontSize(9);
                        pdf.setTextColor(136, 136, 136);
                        pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                        mainY += 5;
                    }

                    mainY += 3;
                }
            });

            mainY += 3;
        }

        // Hobbies
        if (cvData.additional_sections.hobbies && cvData.additional_sections.hobbies.trim()) {
            if (mainY > 260) {
                pdf.addPage();
                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                pdf.rect(0, 0, 63, 297, 'F');
                mainY = 20;
            }

            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(12);
            pdf.setTextColor(rgb.r, rgb.g, rgb.b);
            pdf.text('HOBBIES', mainX, mainY);
            pdf.setLineWidth(0.5);
            pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
            mainY += 9;

            pdf.setFont(fontFamily, 'normal');
            pdf.setFontSize(9);
            pdf.setTextColor(68, 68, 68);
            const hobbiesLines = pdf.splitTextToSize(cvData.additional_sections.hobbies, mainWidth);
            hobbiesLines.forEach(line => {
                pdf.text(line, mainX, mainY);
                mainY += 5;
            });
        }

        const fileName = `CV_${cvData.personal_details.first_name}_${cvData.personal_details.last_name}.pdf`;
        if (file){
            pdf.save(fileName);
        }else{
            return pdf.output('blob');
        }

        document.getElementById('loadingOverlay').classList.remove('show');
        showNotification(__('pdf_downloaded_successfully'), 'success');

    } catch (error) {
        console.error('Error generating PDF:', error);
        document.getElementById('loadingOverlay').classList.remove('show');
        showNotification(__('failed_to_generate_pdf'), 'error');
    }
}

async function downloadImage() {
    const container = document.getElementById('cvPreviewContainer');

    document.getElementById('loadingOverlay').classList.add('show');

    try {
        const page = container.querySelector('.cv-page');

        const canvas = await html2canvas(page, {
            scale: 2,
            useCORS: true,
            logging: false,
            width: A4_WIDTH,
            height: A4_HEIGHT
        });

        canvas.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `CV_${cvData.personal_details.first_name}_${cvData.personal_details.last_name}.png`;
            link.click();
            URL.revokeObjectURL(url);

            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification(__('image_downloaded_successfully'), 'success');
        });

    } catch (error) {
        console.error(__('failed_to_generate_image'), error);
        document.getElementById('loadingOverlay').classList.remove('show');
        showNotification(__('failed_to_generate_image'), 'error');
    }
}

function getIconAsBase64(iconType) {
    const canvas = document.createElement('canvas');
    canvas.width = 40;
    canvas.height = 40;
    const ctx = canvas.getContext('2d');

    ctx.fillStyle = '#ffffff';
    ctx.font = '30px "Font Awesome 6 Free"';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    let iconCode = '';
    switch(iconType) {
        case 'email': iconCode = '\uf0e0'; break;
        case 'phone': iconCode = '\uf879'; break;
        case 'location': iconCode = '\uf041'; break;
    }

    ctx.fillText(iconCode, 20, 20);
    return canvas.toDataURL('image/png');
}

function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : { r: 44, g: 62, b: 80 };
}

function formatDateForPDF(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString + '-01');
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
}
