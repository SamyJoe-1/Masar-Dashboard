<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص المرشحين الذكي - ATS Scanner</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: linear-gradient(135deg, #667eea 0%, #3a7a9d 100%);
            --secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark: #1a1a2e;
            --light: #ffffff;
            --gray-light: #f8fafc;
            --gray-medium: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        body {
            font-family: 'Tajawal', 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .lang-switch {
            background: var(--accent);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        .lang-switch:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #3a7a9d 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="300" cy="700" r="120" fill="url(%23a)"/><circle cx="900" cy="800" r="80" fill="url(%23a)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-text {
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: white;
            color: #667eea;
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
        }

        /* Hero Dashboard */
        .hero-dashboard {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            animation: slideUp 1s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            color: white;
        }

        .upload-zone {
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            color: white;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-zone:hover {
            border-color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .upload-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Features Section */
        .features {
            padding: 8rem 0;
            background: var(--gray-light);
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: var(--primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: var(--gray-medium);
            margin-bottom: 4rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 3rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            background: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: var(--gray-medium);
            line-height: 1.8;
        }

        /* Process Section */
        .process {
            padding: 8rem 0;
            background: white;
        }

        .process-steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4rem;
            margin-top: 4rem;
        }

        .process-step {
            text-align: center;
            position: relative;
        }

        .process-step::after {
            content: '';
            position: absolute;
            top: 40px;
            right: -2rem;
            width: 4rem;
            height: 2px;
            background: linear-gradient(90deg, #667eea, transparent);
        }

        .process-step:last-child::after {
            display: none;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            color: white;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
        }

        .step-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .step-description {
            color: var(--gray-medium);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .nav-links {
                display: none;
            }

            .features-grid,
            .process-steps {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
            }
        }

        /* Arabic RTL specific styles */
        [dir="rtl"] .process-step::after {
            right: auto;
            left: -2rem;
            background: linear-gradient(-90deg, #667eea, transparent);
        }

        [dir="rtl"] .nav-links a::after {
            right: auto;
            left: 0;
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<header>
    <nav class="container">
        <a href="/" class="logo">
            <img src="{{ asset('assets/images/logo2.png') }}" width="100">
        </a>
        <ul class="nav-links">
            <li><a href="#features">المميزات</a></li>
            <li><a href="#process">كيف يعمل</a></li>
            <li><a href="#pricing">الأسعار</a></li>
            <li><a href="#contact">تواصل معنا</a></li>
        </ul>
        <button class="lang-switch" onclick="toggleLanguage()">English</button>
    </nav>
</header>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">فحص ذكي للسير الذاتية بتقنية الذكاء الاصطناعي</h1>
                <p class="hero-subtitle">ارفع مئات السير الذاتية واحصل على تقرير شامل بالمرشحين المقبولين والمرفوضين مع تحليل مفصل لكل ملف</p>
                <div class="cta-buttons">
                    <a href="#" class="btn btn-primary">ابدأ المسح المجاني</a>
                    <a href="#" class="btn btn-secondary">شاهد العرض التوضيحي</a>
                </div>
            </div>
            <div class="hero-dashboard">
                <div class="dashboard-header">
                    <h3>لوحة المسح الذكي</h3>
                    <span>🚀</span>
                </div>
                <div class="upload-zone">
                    <div class="upload-icon">📄</div>
                    <h4>اسحب وأفلت السير الذاتية هنا</h4>
                    <p>أو انقر للتصفح (PDF, DOC, DOCX)</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-number">156</span>
                        <span class="stat-label">تم المسح</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">89</span>
                        <span class="stat-label">مقبول</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">67</span>
                        <span class="stat-label">مرفوض</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="features" id="features">
    <div class="container">
        <h2 class="section-title">مميزات استثنائية</h2>
        <p class="section-subtitle">تقنيات متطورة لفحص وتحليل السير الذاتية بدقة عالية</p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <h3 class="feature-title">ذكاء اصطناعي متقدم</h3>
                <p class="feature-description">خوارزميات تعلم آلة متطورة تحلل السير الذاتية بدقة تصل إلى 95% مع فهم السياق والمهارات المطلوبة</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3 class="feature-title">معالجة سريعة</h3>
                <p class="feature-description">فحص مئات السير الذاتية في دقائق معدودة مع تقارير مفصلة فورية وترتيب تلقائي للمرشحين</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">تقارير شاملة</h3>
                <p class="feature-description">تحليلات عميقة تشمل نقاط القوة والضعف، مطابقة المهارات، والتوصيات المخصصة لكل مرشح</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3 class="feature-title">دعم متعدد اللغات</h3>
                <p class="feature-description">يتعامل مع السير الذاتية باللغة العربية والإنجليزية بنفس الدقة مع فهم المصطلحات المحلية</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3 class="feature-title">أمان وخصوصية</h3>
                <p class="feature-description">تشفير متقدم لحماية بيانات المرشحين مع ضمان الخصوصية التامة وعدم تخزين المعلومات الحساسة</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3 class="feature-title">مطابقة دقيقة</h3>
                <p class="feature-description">مطابقة ذكية للمهارات والخبرات مع متطلبات الوظيفة وترشيح أفضل المرشحين تلقائياً</p>
            </div>
        </div>
    </div>
</section>

<section class="process" id="process">
    <div class="container">
        <h2 class="section-title">كيف يعمل النظام؟</h2>
        <p class="section-subtitle">ثلاث خطوات بسيطة للحصول على أفضل المرشحين</p>

        <div class="process-steps">
            <div class="process-step">
                <div class="step-number">1</div>
                <h3 class="step-title">ارفع السير الذاتية</h3>
                <p class="step-description">قم برفع مجموعة السير الذاتية بصيغ PDF أو Word، يمكن رفع مئات الملفات دفعة واحدة</p>
            </div>
            <div class="process-step">
                <div class="step-number">2</div>
                <h3 class="step-title">المعالجة الذكية</h3>
                <p class="step-description">النظام يحلل كل سيرة ذاتية باستخدام الذكاء الاصطناعي ويقارنها بمعايير الوظيفة المحددة</p>
            </div>
            <div class="process-step">
                <div class="step-number">3</div>
                <h3 class="step-title">احصل على التقرير</h3>
                <p class="step-description">تقرير شامل بالمرشحين مرتبين حسب الأولوية مع تحليل مفصل لكل مرشح ونقاط القوة والضعف</p>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>ATS Scanner</h3>
                <p>منصة ذكية لفحص وتحليل السير الذاتية باستخدام أحدث تقنيات الذكاء الاصطناعي</p>
            </div>
            <div class="footer-section">
                <h3>روابط سريعة</h3>
                <a href="#">المميزات</a>
                <a href="#">الأسعار</a>
                <a href="#">المساعدة</a>
            </div>
            <div class="footer-section">
                <h3>تواصل معنا</h3>
                <a href="#">info@atsscanner.com</a>
                <a href="#">+966 50 123 4567</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 ATS Scanner. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</footer>

<script>
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
    const uploadZone = document.querySelector('.upload-zone');
    uploadZone.addEventListener('click', function() {
        // Simulate file upload
        this.style.background = 'rgba(255, 255, 255, 0.1)';
        this.innerHTML = '<div class="upload-icon">⏳</div><h4>جاري المعالجة...</h4>';

        setTimeout(() => {
            this.innerHTML = '<div class="upload-icon">✅</div><h4>تم الرفع بنجاح!</h4><p>156 ملف تم تحليله</p>';
            this.style.background = 'rgba(16, 185, 129, 0.1)';
        }, 2000);
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
</script>
</body>
</html>
