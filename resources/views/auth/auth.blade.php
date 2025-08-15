<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isLogin ? 'Login' : 'Register' }} - Number Trivia Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/css/auth/auth.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="auth-page">
        <!-- Back Button -->
        <a href="{{ route('trivia.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="auth-container {{ !$isLogin ? 'right-panel-active' : '' }}" id="container">
            <!-- Sign Up Form -->
            <div class="form-container sign-up-container">
                <form class="auth-form" method="POST" action="{{ route('register') }}">
                    @csrf
                    <h1 class="auth-title">Create Account</h1>
                    
                    <!-- Social Login Buttons 
                    <div class="social-container">
                        <button type="button" class="social-button google">
                            <i class="fab fa-google"></i>
                        </button>
                        <button type="button" class="social-button apple">
                            <i class="fab fa-apple"></i>
                        </button>
                                              <button type="button" class="social-button facebook">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                    </div> -->

                    @if($errors->any() && !$isLogin)
                        <div class="error-message">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-grid">
                        <div class="input-group full-width">
                            <input type="text" class="auth-input" name="name" placeholder="Name" value="{{ old('name') }}" required>
                        </div>
                        
                        <div class="input-group full-width">
                            <input type="email" class="auth-input" name="email" placeholder="Email" value="{{ old('email') }}" required>
                        </div>
                        
                        <div class="input-group full-width">
                            <div class="input-wrapper">
                                <input type="password" class="auth-input" name="password" id="signup-password" placeholder="Password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('signup-password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="input-group full-width">
                            <div class="input-wrapper">
                                <input type="password" class="auth-input" name="password_confirmation" id="confirm-password" placeholder="Confirm Password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm-password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                      <div class="checkbox-wrapper">
                        <input type="checkbox" name="terms" class="terms" required>
                        <label>I agree to the <a href="{{ route('trivia.index') }}" class="terms-link">Terms of Service</a></label>
                    </div>
                    
                    <button type="submit" class="auth-button">Sign Up</button>
                </form>
            </div>

            <!-- Sign In Form -->
            <div class="form-container sign-in-container">
                <form class="auth-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <h1 class="auth-title">Sign In</h1>
                    
                    <!-- Social Login Buttons 
                    <div class="social-container">
                        <button type="button" class="social-button google">
                            <i class="fab fa-google"></i>
                        </button>
                        <button type="button" class="social-button apple">
                            <i class="fab fa-apple"></i>
                        </button>
                        <button type="button" class="social-button facebook">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                    </div> -->

                    @if($errors->any() && $isLogin)
                        <div class="error-message">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif                    @if(session('success'))
                        <div class="success-message">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="input-wrapper">
                        <input type="email" class="auth-input" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="input-wrapper">
                        <input type="password" class="auth-input" name="password" id="signin-password" placeholder="Password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('signin-password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot your password?</a>
                    
                    <button type="submit" class="auth-button">Sign In</button>
                </form>
            </div>

            <!-- Overlay -->
            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1 class="overlay-title">Welcome Back!</h1>
                        <p class="overlay-text">To keep connected with us please login with your personal info</p>
                        <button class="ghost-button" onclick="toggleForm()">Sign In</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1 class="overlay-title">Hello, Friend!</h1>
                        <p class="overlay-text">Enter your personal details and start your trivia journey with us</p>
                        <button class="ghost-button" onclick="toggleForm()">Sign Up</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>        function toggleForm() {
            const container = document.getElementById('container');
            const signInContainer = document.querySelector('.sign-in-container');
            
            container.classList.toggle('right-panel-active');
            
            // Hide/show sign-in form based on register form state
            const isSignUp = container.classList.contains('right-panel-active');
            if (isSignUp) {
                signInContainer.style.visibility = 'hidden';
            } else {
                signInContainer.style.visibility = 'visible';
            }
            
            // Update URL without page reload
            const newUrl = isSignUp ? '{{ route("register") }}' : '{{ route("login") }}';
            window.history.pushState({}, '', newUrl);
        }

        function togglePassword(inputId, button) {
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
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('container');
            const signInContainer = document.querySelector('.sign-in-container');
            const isRegisterPage = window.location.pathname.includes('register');
            
            if (isRegisterPage) {
                container.classList.add('right-panel-active');
                signInContainer.style.visibility = 'hidden';
            } else {
                signInContainer.style.visibility = 'visible';
            }
        });     
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('.auth-button');
                    
                    // Check if it's register or login form
                    const isRegister = this.closest('.sign-up-container');
                    
                    button.disabled = true;
                    button.innerHTML = isRegister ? 'Creating Account...' : 'Signing In...';
                    
                    // Re-enable button after 10 seconds to prevent permanent disable on error
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = isRegister ? 'Sign Up' : 'Sign In';
                    }, 10000);
                });
            });

            // Real-time form validation
            const emailInputs = document.querySelectorAll('input[name="email"]');
            const passwordInputs = document.querySelectorAll('input[name="password"]');
            
            emailInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateEmail(this);
                });
            });
            
            passwordInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.closest('.sign-up-container')) {
                        validatePasswordStrength(this);
                    }
                });
            });
        });

        function validateEmail(input) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValid = emailPattern.test(input.value);
            
            if (input.value && !isValid) {
                input.style.borderColor = '#dc2626';
                input.style.backgroundColor = '#fee2e2';
            } else {
                input.style.borderColor = '#e1e5e9';
                input.style.backgroundColor = '#f8f9fa';
            }
        }

        function validatePasswordStrength(input) {
            // This function is called for registration password validation
            // The visual feedback is already handled by the existing password strength indicator
        }
    </script>
</body>
</html>
