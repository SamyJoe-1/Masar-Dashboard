// Dummy ATS Results Data
// This will be replaced with actual API responses in production

window.dummyATSResults = {
    ats_score: 68,
    content_score: 64,
    formatting_score: 83,
    skills_score: 75,

    feedback: {
        content: {
            title: 'Content Quality Analysis',
            icon: 'fas fa-align-left',
            type: 'points',
            items: [
                {
                    title: 'Quantifiable Achievements',
                    description: 'Your CV lacks measurable results. Add specific numbers, percentages, or metrics to demonstrate impact (e.g., "Increased sales by 30%" instead of "Improved sales").',
                    passed: false
                },
                {
                    title: 'Action Verbs Usage',
                    description: 'Good use of strong action verbs like "Developed", "Implemented", "Led". This makes your experience more impactful.',
                    passed: true
                },
                {
                    title: 'Professional Summary',
                    description: 'Your summary is present but too generic. Tailor it to include your unique value proposition and key achievements with numbers.',
                    passed: false
                },
                {
                    title: 'Experience Descriptions',
                    description: 'Experience section is well-structured with clear job titles and company names. Consider adding more details about team size and project scope.',
                    passed: true
                },
                {
                    title: 'Grammar & Spelling',
                    description: 'Found 4 potential grammar or spelling issues. Review dates formatting, verb tenses consistency, and punctuation.',
                    passed: false
                }
            ]
        },

        formatting: {
            title: 'Formatting & ATS Compatibility',
            icon: 'fas fa-paint-brush',
            type: 'points',
            items: [
                {
                    title: 'ATS Parsing Rate: 78%',
                    description: 'Your CV is moderately readable by ATS systems. Some elements may be causing parsing issues - possibly tables, columns, or text boxes.',
                    passed: false
                },
                {
                    title: 'File Format',
                    description: 'PDF format is correct and file size is under 2MB. This ensures compatibility with most ATS systems.',
                    passed: true
                },
                {
                    title: 'Font & Readability',
                    description: 'Using standard, ATS-friendly fonts. Text is clear and readable without images or graphics interfering with parsing.',
                    passed: true
                },
                {
                    title: 'Section Headers',
                    description: 'Clear section headers present (Experience, Education, Skills). ATS can easily identify different sections of your CV.',
                    passed: true
                },
                {
                    title: 'Layout Structure',
                    description: 'Layout is simple and clean, but consider using a single-column format for better ATS compatibility instead of multi-column layouts.',
                    passed: true
                }
            ]
        },

        sections: {
            title: 'Required Sections Check',
            icon: 'fas fa-list-check',
            type: 'points',
            items: [
                {
                    title: 'Contact Information',
                    description: 'Phone and email are present. However, LinkedIn profile URL is missing - this is expected by most recruiters and ATS systems.',
                    passed: false
                },
                {
                    title: 'Professional Experience',
                    description: 'Experience section is complete with job titles, companies, dates, and descriptions. Well organized chronologically.',
                    passed: true
                },
                {
                    title: 'Education',
                    description: 'Education section present with degree, institution, and dates. Consider adding relevant coursework or GPA if strong.',
                    passed: true
                },
                {
                    title: 'Skills Section',
                    description: 'Skills are listed but need better organization. Separate into categories (Technical, Soft Skills, Tools) for better readability.',
                    passed: true
                },
                {
                    title: 'Projects/Portfolio',
                    description: 'Projects section is included, which is excellent for technical roles. Consider adding live links or GitHub repositories.',
                    passed: true
                }
            ]
        },

        skills: {
            title: 'Skills Analysis',
            icon: 'fas fa-code',
            type: 'badges',
            items: [
                { name: 'PHP', relevant: true },
                { name: 'Laravel', relevant: true },
                { name: 'Vue.js', relevant: true },
                { name: 'JavaScript', relevant: true },
                { name: 'MySQL', relevant: true },
                { name: 'Docker', relevant: true },
                { name: 'AWS', relevant: true },
                { name: 'Git', relevant: true },
                { name: 'REST APIs', relevant: true },
                { name: 'Figma', relevant: true },
                { name: 'Photoshop', relevant: false },
                { name: 'MS Office', relevant: false },
                { name: 'Social Media Marketing', relevant: false }
            ]
        },

        skillsOrganization: {
            title: 'Skills Organization Recommendation',
            icon: 'fas fa-layer-group',
            type: 'paragraph',
            content: 'Your skills are listed but lack clear categorization. Organize them into distinct groups for better readability and ATS parsing: <br><br><strong>Backend:</strong> PHP, Laravel, MySQL, REST APIs<br><strong>Frontend:</strong> Vue.js, JavaScript, TypeScript, HTML/CSS<br><strong>DevOps/Cloud:</strong> Docker, AWS, DigitalOcean, CI/CD<br><strong>Tools & Other:</strong> Git, Figma, Stripe, Paymob<br><br>Remove irrelevant skills like "MS Office" and "Social Media Marketing" unless directly related to the target position.'
        },

        keywords: {
            title: 'Keyword Optimization',
            icon: 'fas fa-key',
            type: 'paragraph',
            content: 'Your CV matches approximately 65% of common keywords for full-stack developer positions. To improve, include more industry-specific terms like "Agile", "Scrum", "CI/CD", "Microservices", "TDD", "API Integration". If applying for specific roles, mirror the exact terminology used in the job description (e.g., if they say "RESTful APIs", use that exact phrase instead of just "APIs").'
        },

        improvements: {
            title: 'Priority Improvements',
            icon: 'fas fa-star',
            type: 'points',
            items: [
                {
                    title: 'Add Quantifiable Metrics (Critical)',
                    description: 'Transform every achievement into a measurable result. Example: "Built loan tracking system using Laravel & MySQL, reducing processing time by 30% and improving accuracy to 99.5%"',
                    passed: false
                },
                {
                    title: 'Add LinkedIn Profile (High Priority)',
                    description: 'Include your LinkedIn URL in the contact section. Format: linkedin.com/in/yourprofile',
                    passed: false
                },
                {
                    title: 'Fix Grammar Issues (High Priority)',
                    description: 'Review your CV for the 4 detected spelling/grammar errors. Pay attention to date formatting, verb tense consistency, and punctuation.',
                    passed: false
                },
                {
                    title: 'Reorganize Skills Section (Medium Priority)',
                    description: 'Group skills into clear categories (Backend, Frontend, DevOps, Tools) and remove unrelated skills.',
                    passed: false
                },
                {
                    title: 'Optimize for Single Column (Low Priority)',
                    description: 'Consider switching to a single-column layout to improve ATS parsing rate from 78% to 90%+.',
                    passed: false
                }
            ]
        },

        overall: {
            title: 'Overall Assessment',
            icon: 'fas fa-clipboard-check',
            type: 'paragraph',
            content: 'Your CV shows solid technical experience and is generally well-structured. With a score of 68/100, you\'re in the "decent but needs improvement" range. The main weaknesses are: lack of quantifiable achievements, missing LinkedIn profile, and some formatting issues affecting ATS parsing. Focus on adding specific numbers and metrics to your achievements - this single change could boost your score to 75-80. Your technical skills are strong, but better organization would help both human reviewers and ATS systems. Consider tailoring your CV for each application by including exact keywords from the job description.'
        }
    },

    suggested_roles: [
        'Full Stack Developer',
        'Laravel Developer',
        'PHP Backend Developer',
        'Vue.js Frontend Developer',
        'Web Application Developer',
        'Software Engineer',
        'API Developer',
        'Cloud Solutions Developer'
    ]
};

// Alternative dummy responses for different scenarios
window.dummyATSResults_Excellent = {
    ats_score: 87,
    content_score: 89,
    formatting_score: 91,
    skills_score: 85,
    feedback: {
        // Similar structure but with mostly "passed: true" items
    },
    suggested_roles: [
        'Senior Full Stack Developer',
        'Technical Lead',
        'Software Architect',
        'Engineering Manager'
    ]
};

window.dummyATSResults_Poor = {
    ats_score: 42,
    content_score: 38,
    formatting_score: 45,
    skills_score: 43,
    feedback: {
        // Similar structure but with mostly "passed: false" items
    },
    suggested_roles: [
        'Junior Developer',
        'Intern - Web Development',
        'Entry Level PHP Developer'
    ]
};

// Function to simulate API delay
async function simulateAPICall(endpoint, duration = 1000) {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve({
                success: true,
                data: window.dummyATSResults
            });
        }, duration);
    });
}

// Mock API functions for different analysis steps
window.mockAPICalls = {
    uploadCV: async (formData) => {
        await simulateAPICall('/api/cv/upload', 800);
        return { file_id: 'dummy_file_123', success: true };
    },

    extractContent: async (fileId) => {
        await simulateAPICall('/api/cv/extract', 1200);
        return {
            raw_text: 'Extracted CV content...',
            sections: ['experience', 'education', 'skills'],
            success: true
        };
    },

    calculateATSScore: async (data) => {
        await simulateAPICall('/api/cv/ats-score', 1500);
        return {
            score: window.dummyATSResults.ats_score,
            success: true
        };
    },

    analyzeContent: async (data) => {
        await simulateAPICall('/api/cv/content-analysis', 1400);
        return {
            score: window.dummyATSResults.content_score,
            feedback: window.dummyATSResults.feedback.content,
            success: true
        };
    },

    analyzeFormatting: async (data) => {
        await simulateAPICall('/api/cv/formatting-analysis', 1100);
        return {
            score: window.dummyATSResults.formatting_score,
            feedback: window.dummyATSResults.feedback.formatting,
            success: true
        };
    },

    analyzeSkills: async (data) => {
        await simulateAPICall('/api/cv/skills-analysis', 1300);
        return {
            score: window.dummyATSResults.skills_score,
            feedback: window.dummyATSResults.feedback.skills,
            success: true
        };
    }
};
