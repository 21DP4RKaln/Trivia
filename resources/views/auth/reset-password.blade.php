<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Number Trivia Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/css/auth/reset-password.css', 'resources/js/app.js'])
    @vite('resources/css/mobile-responsive.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="global-page auth-page">
    <!-- Global Background -->
    <div class="global-background"></div>
    
    <!-- Global Particles -->
    <div class="global-particles">
        <div class="global-particle" style="left: 16%; animation-delay: 0.8s;"></div>
        <div class="global-particle" style="left: 28%; animation-delay: 2.8s;"></div>
        <div class="global-particle" style="left: 40%; animation-delay: 4.8s;"></div>
        <div class="global-particle" style="left: 52%; animation-delay: 6.8s;"></div>
        <div class="global-particle" style="left: 64%; animation-delay: 8.8s;"></div>
        <div class="global-particle" style="left: 76%; animation-delay: 10.8s;"></div>
        <div class="global-particle" style="left: 88%; animation-delay: 12.8s;"></div>
    </div>
    
    <div class="reset-password-page">
        <!-- Back Button -->
        <a href="{{ route('login') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="reset-password-container">
            <form class="reset-password-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="icon-container">
                    <i class="fas fa-key"></i>
                </div>
                
                <h1 class="reset-password-title">Reset Password</h1>
                
                <p class="reset-password-subtitle">
                    Enter your new password below. Make sure it's strong and secure.
                </p>

                @if($errors->any())
                    <div class="error-message">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(session('status'))
                    <div class="success-message">
                        {{ session('status') }}
                    </div>
                @endif
                
                <input 
                    type="email" 
                    class="auth-input" 
                    name="email" 
                    placeholder="Email Address" 
                    value="{{ $email ?? old('email') }}" 
                    required
                    autocomplete="email"
                    readonly
                >
                
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        class="auth-input" 
                        name="password" 
                        id="password"
                        placeholder="New Password" 
                        required
                        autocomplete="new-password"
                        onkeyup="checkPasswordStrength(this.value)"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="password-strength" id="password-strength" style="display: none;">
                    <div class="strength-bar">
                        <div class="strength-segment" id="strength-1"></div>
                        <div class="strength-segment" id="strength-2"></div>
                        <div class="strength-segment" id="strength-3"></div>
                        <div class="strength-segment" id="strength-4"></div>
                    </div>
                    <div class="strength-text" id="strength-text">Password strength</div>
                </div>
                
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        class="auth-input" 
                        name="password_confirmation" 
                        id="password_confirmation"
                        placeholder="Confirm New Password" 
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                
                <button type="submit" class="auth-button">
                    Reset Password
                </button>
                
                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Login
                </a>
            </form>
        </div>
    </div>

    <script>
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

        function checkPasswordStrength(password) {
            const strengthIndicator = document.getElementById('password-strength');
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
            
            // Character type checks
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            
            // Normalize score to 4 segments
            const normalizedScore = Math.min(4, Math.floor(score * 4 / 6));
            
            // Update visual indicators
            for (let i = 1; i <= 4; i++) {
                const segment = document.getElementById(`strength-${i}`);
                segment.style.background = i <= normalizedScore ? getStrengthColor(normalizedScore) : '#e1e5e9';
            }
            
            // Update text
            const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            strengthText.textContent = strengthTexts[normalizedScore] || 'Very Weak';
            strengthText.style.color = getStrengthColor(normalizedScore);
        }
        
        function getStrengthColor(score) {
            const colors = ['#dc2626', '#f59e0b', '#10b981', '#059669'];
            return colors[Math.max(0, score - 1)] || '#dc2626';
        }

        // Add form validation and password strength checking
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const passwordInput = document.getElementById('password');
            
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
            });
            
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('.auth-button');
                button.disabled = true;
                button.classList.add('loading');
                button.innerHTML = 'Resetting...';
                
                // Re-enable after timeout to prevent permanent disable on error
                setTimeout(() => {
                    button.disabled = false;
                    button.classList.remove('loading');
                    button.innerHTML = 'Reset Password';
                }, 10000);
            });

            // Ensure global background is visible
            const globalBg = document.querySelector('.global-background');
            if (globalBg) {
                globalBg.style.display = 'block';
            }
        });
    </script>
</body>
</html>
