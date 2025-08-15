@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/authentication.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
    @php($utm = session()->get('path') == 'register' ? 'register':'login')
    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-card">
                <!-- Header with Logo/Title -->
                <div class="auth-header">
                    <h1 class="auth-logo">مسار</h1>
                    <p class="auth-subtitle">انضم إلى منصة الذكاء الاصطناعي الرائدة</p>
                </div>

                <!-- Tab Switcher (Only show for login/register, hide for forgot password) -->
                <div class="auth-tabs" id="authTabs">
                    <button class="auth-tab {{ $utm == 'login' ? 'active':'' }}" data-tab="login">تسجيل الدخول</button>
                    <button class="auth-tab {{ $utm == 'register' ? 'active':'' }}" data-tab="register">إنشاء حساب</button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="auth-form {{ $utm == 'login' ? 'active':'' }}" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">البريد الإلكتروني أو اسم المستخدم</label>
                        <input type="text" id="email" name="email" class="form-input" placeholder="أدخل بريدك الإلكتروني" value="{{ old('email') }}" required>
                        @error('email')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" class="form-input" placeholder="أدخل كلمة المرور" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">👁️</button>
                        </div>
                        @error('password')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            تذكرني
                        </label>
                        <a href="#" class="forgot-link" onclick="showForgotPassword()">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="auth-btn">
                        <span>تسجيل الدخول</span>
                        <div class="btn-loader"></div>
                    </button>

                    <div class="social-login">
                        <div class="divider">
                            <span>أو</span>
                        </div>
                        <button type="button" class="social-btn google-btn" onclick="location.href = '{{ route('auth.google') }}'">
                            <svg width="20" height="20" viewBox="0 0 24 24">
                                <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            الدخول بحساب Google
                        </button>
                    </div>
                </form>

                <!-- Register Form -->
                <form id="registerForm" class="auth-form {{ $utm == 'register' ? 'active':'' }}" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">الإسم بالكامل</label>
                        <input type="text" id="name" name="name" class="form-input" placeholder="ادخل اسمك بالكامل" value="{{ old('name') }}" required>
                        @error('name')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="register_email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" id="register_email" name="email" class="form-input" placeholder="أدخل بريدك الإلكتروني" value="{{ old('email') }}" required>
                        @error('email')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="register_password" class="form-label">كلمة المرور</label>
                        <div class="password-wrapper">
                            <input type="password" id="register_password" name="password" class="form-input" placeholder="أدخل كلمة مرور قوية" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('register_password')">👁️</button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        @error('password')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                        <div class="password-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="أعد إدخال كلمة المرور" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">👁️</button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="terms" required>
                            <span class="checkmark"></span>
                            أوافق على <a href="#" class="link">الشروط والأحكام</a>
                        </label>
                    </div>

                    <button type="submit" class="auth-btn">
                        <span>إنشاء الحساب</span>
                        <div class="btn-loader"></div>
                    </button>

                    <div class="social-login">
                        <div class="divider">
                            <span>أو</span>
                        </div>
                        <button type="button" class="social-btn google-btn" onclick="location.href = '{{ route('auth.google') }}'">
                            <svg width="20" height="20" viewBox="0 0 24 24">
                                <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            التسجيل بحساب Google
                        </button>
                    </div>
                </form>

                <!-- Forgot Password Form -->
                <form id="forgotPasswordForm" class="auth-form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="forgot-header">
                        <button type="button" class="back-btn" onclick="showLogin()">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                        </button>
                        <h2>استعادة كلمة المرور</h2>
                    </div>

                    <p class="forgot-description">أدخل بريدك الإلكتروني وسنرسل لك رابط لإعادة تعيين كلمة المرور</p>

                    <div class="form-group">
                        <label for="forgot_email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" id="forgot_email" name="email" class="form-input" placeholder="أدخل بريدك الإلكتروني" required>
                        @error('email')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="auth-btn">
                        <span>إرسال رابط الاستعادة</span>
                        <div class="btn-loader"></div>
                    </button>

                    @if (session('status'))
                        <div class="success-message">
                            {{ session('status') }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </section>
    <x-alerts.sweetalerts></x-alerts.sweetalerts>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/authentication.js') }}"></script>
@endsection
