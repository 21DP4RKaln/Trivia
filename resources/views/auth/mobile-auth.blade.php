<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>{{ $isLogin ? 'Login' : 'Register' }} - Trivia Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/css/auth/mobile-auth.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="mobile-auth-page">
    <!-- Background -->
    <div class="mobile-auth-background">
        <div class="mobile-particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="mobile-particle" style="left: 30%; animation-delay: 2s;"></div>
        <div class="mobile-particle" style="left: 50%; animation-delay: 4s;"></div>
        <div class="mobile-particle" style="left: 70%; animation-delay: 6s;"></div>
        <div class="mobile-particle" style="left: 90%; animation-delay: 8s;"></div>
    </div>

    <!-- Mobile Auth Container -->
    <div class="mobile-auth-container">        
        <!-- Header -->
        <div class="mobile-auth-header">
            <h1 class="mobile-auth-title">
                <a href="{{ route('trivia.index') }}" class="mobile-auth-title-link">Trivia Game</a>
            </h1>
            <div class="mobile-auth-toggle">
                <button type="button" class="toggle-btn {{ $isLogin ? 'active' : '' }}" onclick="showLogin()">Login</button>
                <button type="button" class="toggle-btn {{ !$isLogin ? 'active' : '' }}" onclick="showRegister()">Register</button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="mobile-auth-content">
            <!-- Login Form -->
            <div id="mobile-login-form" class="mobile-form-container {{ $isLogin ? 'active' : '' }}">
                <form method="POST" action="{{ route('login') }}" class="mobile-auth-form">
                    @csrf
                    <div class="mobile-form-header">
                        <h2>Welcome Back!</h2>
                        <p>Sign in to continue your trivia journey</p>
                    </div>

                    @if($errors->any() && $isLogin)
                        <div class="mobile-error-message">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mobile-success-message">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mobile-input-group">
                        <label for="login-email">Email</label>
                        <input 
                            type="email" 
                            id="login-email"
                            name="email" 
                            class="mobile-input" 
                            placeholder="Enter your email"
                            value="{{ old('email') }}" 
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="mobile-input-group">
                        <label for="login-password">Password</label>
                        <div class="mobile-password-wrapper">
                            <input 
                                type="password" 
                                id="login-password"
                                name="password" 
                                class="mobile-input" 
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="mobile-password-toggle" onclick="toggleMobilePassword('login-password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mobile-form-options">
                        <label class="mobile-checkbox">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="mobile-forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="mobile-submit-btn">
                        <span>Sign In</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div id="mobile-register-form" class="mobile-form-container {{ !$isLogin ? 'active' : '' }}">
                <form method="POST" action="{{ route('register') }}" class="mobile-auth-form">
                    @csrf
                    <div class="mobile-form-header">
                        <h2>Create Account</h2>
                        <p>Join us and start your trivia adventure</p>
                    </div>

                    @if($errors->any() && !$isLogin)
                        <div class="mobile-error-message">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mobile-input-group">
                        <label for="register-name">Full Name</label>
                        <input 
                            type="text" 
                            id="register-name"
                            name="name" 
                            class="mobile-input" 
                            placeholder="Enter your full name"
                            value="{{ old('name') }}" 
                            required
                            autocomplete="name"
                        >
                    </div>

                    <div class="mobile-input-group">
                        <label for="register-email">Email</label>
                        <input 
                            type="email" 
                            id="register-email"
                            name="email" 
                            class="mobile-input" 
                            placeholder="Enter your email"
                            value="{{ old('email') }}" 
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="mobile-input-group">
                        <label for="register-password">Password</label>
                        <div class="mobile-password-wrapper">
                            <input 
                                type="password" 
                                id="register-password"
                                name="password" 
                                class="mobile-input" 
                                placeholder="Create a password"
                                required
                                autocomplete="new-password"
                                onkeyup="checkMobilePasswordStrength(this.value)"
                            >
                            <button type="button" class="mobile-password-toggle" onclick="toggleMobilePassword('register-password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="mobile-password-strength" class="mobile-password-strength" style="display: none;">
                            <div class="strength-indicator">
                                <div class="strength-bar" id="strength-bar"></div>
                            </div>
                            <div class="strength-text" id="strength-text">Password strength</div>
                        </div>
                    </div>

                    <div class="mobile-input-group">
                        <label for="register-password-confirmation">Confirm Password</label>
                        <div class="mobile-password-wrapper">
                            <input 
                                type="password" 
                                id="register-password-confirmation"
                                name="password_confirmation" 
                                class="mobile-input" 
                                placeholder="Confirm your password"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" class="mobile-password-toggle" onclick="toggleMobilePassword('register-password-confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mobile-terms-group">
                        <label class="mobile-checkbox">
                            <input type="checkbox" name="terms" required>
                            <span class="checkmark"></span>
                            I agree to the <a href="{{ route('terms.service') }}" target="_blank">Terms of Service</a>
                        </label>
                    </div>

                    <button type="submit" class="mobile-submit-btn">
                        <span>Create Account</span>
                        <i class="fas fa-user-plus"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showLogin() {
            document.getElementById('mobile-login-form').classList.add('active');
            document.getElementById('mobile-register-form').classList.remove('active');
            document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.toggle-btn')[0].classList.add('active');
            window.history.pushState({}, '', '{{ route("login") }}');
        }

        function showRegister() {
            document.getElementById('mobile-register-form').classList.add('active');
            document.getElementById('mobile-login-form').classList.remove('active');
            document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.toggle-btn')[1].classList.add('active');
            window.history.pushState({}, '', '{{ route("register") }}');
        }

        function toggleMobilePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function checkMobilePasswordStrength(password) {
            const strengthIndicator = document.getElementById('mobile-password-strength');
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            
            if (password.length === 0) {
                strengthIndicator.style.display = 'none';
                return;
            }
            
            strengthIndicator.style.display = 'block';
            
            let score = 0;
            let feedback = '';
            
            // Length check
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            
            // Character variety
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            
            // Set strength level
            if (score < 3) {
                strengthBar.className = 'strength-bar weak';
                strengthBar.style.width = '25%';
                feedback = 'Weak password';
            } else if (score < 5) {
                strengthBar.className = 'strength-bar medium';
                strengthBar.style.width = '50%';
                feedback = 'Medium password';
            } else if (score < 6) {
                strengthBar.className = 'strength-bar strong';
                strengthBar.style.width = '75%';
                feedback = 'Strong password';
            } else {
                strengthBar.className = 'strength-bar very-strong';
                strengthBar.style.width = '100%';
                feedback = 'Very strong password';
            }
            
            strengthText.textContent = feedback;
        }

        // Form submission handling
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.mobile-auth-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('.mobile-submit-btn');
                    const span = button.querySelector('span');
                    const icon = button.querySelector('i');
                    
                    const isRegister = this.closest('#mobile-register-form');
                    
                    button.disabled = true;
                    span.textContent = isRegister ? 'Creating Account...' : 'Signing In...';
                    icon.className = 'fas fa-spinner fa-spin';
                    
                    // Re-enable after timeout to prevent permanent disable on error
                    setTimeout(() => {
                        button.disabled = false;
                        span.textContent = isRegister ? 'Create Account' : 'Sign In';
                        icon.className = isRegister ? 'fas fa-user-plus' : 'fas fa-arrow-right';
                    }, 10000);
                });
            });

            // Email validation
            const emailInputs = document.querySelectorAll('input[type="email"]');
            emailInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    const isValid = emailPattern.test(this.value);
                    
                    if (this.value && !isValid) {
                        this.classList.add('error');
                    } else {
                        this.classList.remove('error');
                    }
                });
            });

            // Password confirmation validation
            const passwordConfirmation = document.getElementById('register-password-confirmation');
            const password = document.getElementById('register-password');
            
            if (passwordConfirmation && password) {
                passwordConfirmation.addEventListener('blur', function() {
                    if (this.value && this.value !== password.value) {
                        this.classList.add('error');
                    } else {
                        this.classList.remove('error');
                    }
                });
            }
        });
    </script>
</body>
</html>
