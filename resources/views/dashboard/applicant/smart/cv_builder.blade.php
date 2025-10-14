<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CV Builder</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Quill Rich Text Editor -->
{{--    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">--}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link href="{{ asset('styles/css/cv_builder.css') }}" rel="stylesheet">
</head>
<body>

<!-- Template Selection Page -->
<div id="templateSelection" class="templates-container">
    <div class="container">
        <h1 class="text-center mb-5">Choose Your CV Template</h1>
        <div class="row g-4" id="templatesGrid">
            <!-- Templates will be loaded dynamically from database -->
        </div>
        <div class="text-center mt-5">
            <button class="btn btn-primary btn-lg px-5" onclick="confirmTemplate()">
                Continue with Selected Template
            </button>
        </div>
    </div>
</div>

<!-- CV Builder Page -->
<div id="cvBuilder" class="builder-container" style="display: none;">
    <!-- Left Side - Form -->
    <div class="builder-left">
        <!-- Progress Bar -->
        <div class="overall-progress">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">CV Completion</span>
                <span class="text-primary fw-bold" id="progressPercentage">0%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progressBar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Steps Navigation -->
        <div class="progress-steps">
            <div class="progress-line">
                <div class="progress-line-fill" id="stepProgress" style="width: 0%"></div>
            </div>
            <div class="step-circle active" onclick="goToStep(1)" data-step="1">1</div>
            <div class="step-circle" onclick="goToStep(2)" data-step="2">2</div>
            <div class="step-circle" onclick="goToStep(3)" data-step="3">3</div>
            <div class="step-circle" onclick="goToStep(4)" data-step="4">4</div>
            <div class="step-circle" onclick="goToStep(5)" data-step="5">5</div>
            <div class="step-circle" onclick="goToStep(6)" data-step="6">6</div>
            <div class="step-circle" onclick="goToStep(7)" data-step="7">7</div>
        </div>

        <!-- Form Sections -->
        <!-- Personal Details -->
        <div class="form-section active" id="section1">
            <h2 class="section-title">Personal Details</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" placeholder="John">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" placeholder="Doe">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Job Title</label>
                <input type="text" class="form-control" id="jobTitle" placeholder="Senior Software Engineer">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="john.doe@example.com">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" placeholder="+1 234 567 890">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" id="address" placeholder="123 Main Street">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">City/State</label>
                        <input type="text" class="form-control" id="cityState" placeholder="New York, NY">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <input type="text" class="form-control" id="country" placeholder="United States">
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div id="additionalDetails" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Zip Code</label>
                            <input type="text" class="form-control" id="zipCode" placeholder="10001">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Driving License</label>
                            <input type="text" class="form-control" id="drivingLicense" placeholder="Class B">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Place of Birth</label>
                            <input type="text" class="form-control" id="placeOfBirth" placeholder="Boston">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dateOfBirth">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nationality</label>
                    <input type="text" class="form-control" id="nationality" placeholder="American">
                </div>
            </div>

            <button class="btn btn-link p-0" onclick="toggleAdditionalDetails()">
                <i class="fas fa-plus-circle"></i> Add more details
            </button>
        </div>

        <!-- Professional Summary -->
        <div class="form-section" id="section2">
            <h2 class="section-title">Professional Summary</h2>
            <div class="form-group position-relative">
                <label class="form-label">Write a brief summary about yourself</label>
                <button class="ai-improve-btn" onclick="improveWithAI('summary')">
                    <i class="fas fa-magic"></i> Improve with AI
                </button>
                <div class="editor-container">
                    <div id="summaryEditor" class="quill-editor"></div>
                </div>
            </div>
        </div>

        <!-- Employment History -->
        <div class="form-section" id="section3">
            <h2 class="section-title">Employment History</h2>
            <div id="employmentList">
                <div class="employment-item border rounded p-3 mb-3" data-index="0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Job Title</label>
                                <input type="text" class="form-control emp-job-title" placeholder="Software Engineer">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Company</label>
                                <input type="text" class="form-control emp-company" placeholder="Tech Corp">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="month" class="form-control emp-start">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <input type="month" class="form-control emp-end">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control emp-city" placeholder="San Francisco">
                            </div>
                        </div>
                    </div>
                    <div class="form-group position-relative">
                        <label class="form-label">Description</label>
                        <button class="ai-improve-btn" onclick="improveWithAI('employment', 0)">
                            <i class="fas fa-magic"></i> Improve with AI
                        </button>
                        <div class="editor-container">
                            <div class="employment-editor quill-editor" data-editor-index="0"></div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="add-more-btn" onclick="addEmployment()">
                <i class="fas fa-plus"></i> Add one more employment
            </button>
        </div>

        <!-- Education -->
        <div class="form-section" id="section4">
            <h2 class="section-title">Education</h2>
            <div id="educationList">
                <div class="education-item border rounded p-3 mb-3" data-index="0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">School/University</label>
                                <input type="text" class="form-control edu-school" placeholder="MIT">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Degree</label>
                                <input type="text" class="form-control edu-degree" placeholder="Bachelor of Computer Science">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="month" class="form-control edu-start">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <input type="month" class="form-control edu-end">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control edu-city" placeholder="Cambridge">
                            </div>
                        </div>
                    </div>
                    <div class="form-group position-relative">
                        <label class="form-label">Description</label>
                        <button class="ai-improve-btn" onclick="improveWithAI('education', 0)">
                            <i class="fas fa-magic"></i> Improve with AI
                        </button>
                        <div class="editor-container">
                            <div class="education-editor quill-editor" data-editor-index="0"></div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="add-more-btn" onclick="addEducation()">
                <i class="fas fa-plus"></i> Add one more education
            </button>
        </div>

        <!-- Skills -->
        <div class="form-section" id="section5">
            <h2 class="section-title">Skills</h2>
            <div id="skillsList">
                <div class="skill-item border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Skill</label>
                                <input type="text" class="form-control skill-name" placeholder="JavaScript">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Level</label>
                                <select class="form-select skill-level">
                                    <option>Novice</option>
                                    <option>Beginner</option>
                                    <option>Skillful</option>
                                    <option selected>Experienced</option>
                                    <option>Expert</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="add-more-btn" onclick="addSkill()">
                <i class="fas fa-plus"></i> Add one more skill
            </button>
        </div>

        <!-- Additional Sections -->
        <div class="form-section" id="section6">
            <h2 class="section-title">Additional Sections</h2>

            <!-- Courses -->
            <div class="mb-4">
                <h4 class="mb-3">Courses</h4>
                <div id="coursesList">
                    <div class="course-item border rounded p-3 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Course</label>
                                    <input type="text" class="form-control course-name" placeholder="Advanced Machine Learning">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Institution</label>
                                    <input type="text" class="form-control course-institution" placeholder="Coursera">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Start Date</label>
                                    <input type="month" class="form-control course-start">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">End Date</label>
                                    <input type="month" class="form-control course-end">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="add-more-btn" onclick="addCourse()">
                    <i class="fas fa-plus"></i> Add one more course
                </button>
            </div>

            <!-- Languages -->
            <div class="mb-4">
                <h4 class="mb-3">Languages</h4>
                <div id="languagesList">
                    <div class="language-item border rounded p-3 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Language</label>
                                    <input type="text" class="form-control lang-name" placeholder="English">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Level</label>
                                    <select class="form-select lang-level">
                                        <option>Select level</option>
                                        <option selected>Native speaker</option>
                                        <option>Highly proficient</option>
                                        <option>Very good command</option>
                                        <option>Good working knowledge</option>
                                        <option>Working knowledge</option>
                                        <option>C2</option>
                                        <option>C1</option>
                                        <option>B2</option>
                                        <option>B1</option>
                                        <option>A2</option>
                                        <option>A1</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="add-more-btn" onclick="addLanguage()">
                    <i class="fas fa-plus"></i> Add one more language
                </button>
            </div>

            <!-- Hobbies -->
            <div class="mb-4">
                <h4 class="mb-3">Hobbies</h4>
                <div class="form-group">
                    <label class="form-label">What do you like?</label>
                    <textarea class="form-control" id="hobbies" rows="3" placeholder="Reading, Hiking, Photography, Cooking..."></textarea>
                </div>
            </div>
        </div>

        <!-- Customize -->
        <div class="form-section" id="section7">
            <h2 class="section-title">Customize Your CV</h2>
            <p class="text-muted">You can customize colors and fonts from the palette icon on the preview.</p>
        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <button class="btn btn-secondary" onclick="previousStep()" id="prevBtn" style="display: none;">
                <i class="fas fa-arrow-left"></i> Previous
            </button>
            <button class="btn btn-primary ms-auto" onclick="nextStep()" id="nextBtn">
                Next <i class="fas fa-arrow-right"></i>
            </button>
            <button class="btn btn-success ms-auto" onclick="finishCV()" id="finishBtn" style="display: none;">
                <i class="fas fa-check"></i> Finish & Generate CV
            </button>
        </div>
    </div>

    <!-- Right Side - Preview -->
    <div class="builder-right">
        <div class="cv-preview-wrapper">
            <!-- Customize Button (Top Left) -->
            <button class="customize-icon-btn" onclick="openCustomizeModal()" title="Customize Design">
                <i class="fas fa-palette"></i>
            </button>

            <!-- A4 Preview Container -->
            <div class="cv-preview-container" id="cvPreviewContainer">
                <!-- Pages will be dynamically generated -->
            </div>
        </div>
    </div>
</div>

<!-- Actions Bar -->
<div class="actions-bar" id="actionsBar" style="display: none;">
    <button class="btn-action btn-primary" onclick="saveToDatabase()">
        <i class="fas fa-save"></i> Save
    </button>
</div>

<!-- Customize Modal -->
<div class="customize-modal" id="customizeModal">
    <div class="customize-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Customize Your CV</h3>
            <button class="btn-close" onclick="closeCustomizeModal()"></button>
        </div>

        <div class="mb-4">
            <h5>Color Theme</h5>
            <div class="color-picker" id="colorPicker">
                <!-- Colors will be populated from template data -->
            </div>
        </div>

        <div class="mb-4">
            <h5>Font Family</h5>
            <select class="form-select" id="fontSelect" onchange="changeFont(this.value)">
                <option value="'Inter', sans-serif">Inter (Default)</option>
                <option value="'Arial', sans-serif">Arial</option>
                <option value="'Times New Roman', serif">Times New Roman</option>
                <option value="'Georgia', serif">Georgia</option>
                <option value="'Courier New', monospace">Courier New</option>
            </select>
        </div>

        <div class="mb-4">
            <h5>Font Size</h5>
            <input type="range" class="form-range" id="fontSizeRange" min="12" max="18" value="14" onchange="changeFontSize(this.value)">
            <div class="text-center"><span id="fontSizeValue">14px</span></div>
        </div>

        <div class="mb-4">
            <h5>Line Spacing</h5>
            <input type="range" class="form-range" id="spacingRange" min="1" max="2" step="0.1" value="1.5" onchange="changeSpacing(this.value)">
            <div class="text-center"><span id="spacingValue">1.5</span></div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-secondary" onclick="closeCustomizeModal()">Cancel</button>
            <button class="btn btn-primary" onclick="applyCustomization()">Apply Changes</button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-emoji">📄</div>
    <div class="spinner"></div>
    <div class="loading-text">Building your amazing CV...</div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{--<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    window.authToken = @json(auth()->check() ? auth()->user()->createToken('cv-builder')->plainTextToken : '');
    window.userId = @json(auth()->id());
</script>
<x-smart.cv_builder.app_script></x-smart.cv_builder.app_script>
</body>
</html>
