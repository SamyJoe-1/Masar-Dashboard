@extends('layouts.app_dashboard')

@section('content')
    <div class="page-header">
        <h1 class="page-title">مرحباً بك في لوحة التحكم</h1>
        <p class="page-subtitle">إليك نظرة عامة على النظام والإحصائيات الحديثة لهذا الشهر</p>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="card-title">إجمالي المستخدمين</div>
            <div class="card-value">1,247</div>
            <div class="card-trend">+12% من الشهر الماضي</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
            <div class="card-title">الوظائف النشطة</div>
            <div class="card-value">328</div>
            <div class="card-trend">+8% من الشهر الماضي</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="card-title">نمو هذا الشهر</div>
            <div class="card-value">+15.3%</div>
            <div class="card-trend">+2.1% من الشهر الماضي</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="card-title">التقييم العام</div>
            <div class="card-value">4.8/5</div>
            <div class="card-trend down">-0.2 من الشهر الماضي</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="card-title">إجمالي الإيرادات</div>
            <div class="card-value">$24,500</div>
            <div class="card-trend">+18% من الشهر الماضي</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="card-title">زيارات الموقع</div>
            <div class="card-value">15,642</div>
            <div class="card-trend">+25% من الشهر الماضي</div>
        </div>
    </div>
@endsection
