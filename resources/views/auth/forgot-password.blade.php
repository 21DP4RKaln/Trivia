<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Forgot Password - Number Trivia Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/css/auth/forgot-password.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="global-page auth-page">
    <!-- Global Background -->
    <div class="global-background"></div>
    
    <!-- Global Particles -->
    <div class="global-particles">
        <div class="global-particle" style="left: 14%; animation-delay: 1s;"></div>
        <div class="global-particle" style="left: 26%; animation-delay: 3s;"></div>
        <div class="global-particle" style="left: 38%; animation-delay: 5s;"></div>
        <div class="global-particle" style="left: 50%; animation-delay: 7s;"></div>
        <div class="global-particle" style="left: 62%; animation-delay: 9s;"></div>
        <div class="global-particle" style="left: 74%; animation-delay: 11s;"></div>
        <div class="global-particle" style="left: 86%; animation-delay: 13s;"></div>
    </div>
    
    <div class="forgot-password-page">
        <a href="{{ route('login') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="forgot-password-container">
            <form class="forgot-password-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <div class="icon-container">
                    <i class="fas fa-lock"></i>
                </div>
                
                <h1 class="forgot-password-title">Forgot Password?</h1>
                
                <p class="forgot-password-subtitle">
                    Don't worry! It happens. Please enter the email address associated with your account.
                </p>

                @if($errors->any())
                    <div class="error-message">
                        @foreach($errors->all() as $error)
                            <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(session('status'))
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif
                
                <div class="input-wrapper">
                    <input 
                        type="email" 
                        class="auth-input" 
                        name="email" 
                        id="email"
                        placeholder="Enter your email address" 
                        value="{{ old('email') }}" 
                        required
                        autocomplete="email"
                        aria-describedby="email-help"
                    >
                    <div id="email-help" class="input-help">
                        We'll send you a link to reset your password
                    </div>
                </div>
                
                <button type="submit" class="auth-button">
                    Send Reset Link
                </button>
                
                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Login
                </a>
            </form>
        </div>
    </div>

    <script>
        // Add form validation and improved UX
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const emailInput = document.querySelector('input[name="email"]');
            const submitButton = document.querySelector('.auth-button');
            
            // Email validation
            emailInput.addEventListener('blur', function() {
                validateEmail(this);
            });
            
            emailInput.addEventListener('input', function() {
                // Remove error styling while typing
                this.style.borderColor = '#e1e5e9';
                this.style.backgroundColor = '#f8f9fa';
            });
            
            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                if (!validateEmail(emailInput)) {
                    e.preventDefault();
                    return;
                }
                
                submitButton.disabled = true;
                submitButton.classList.add('loading');
                submitButton.innerHTML = 'Sending...';
                
                // Re-enable button after 10 seconds as fallback
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.classList.remove('loading');
                    submitButton.innerHTML = 'Send Reset Link';
                }, 10000);
            });
        });
        
        function validateEmail(input) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValid = emailPattern.test(input.value);
            
            if (input.value && !isValid) {
                input.style.borderColor = '#dc2626';
                input.style.backgroundColor = '#fee2e2';
                showTooltip(input, 'Please enter a valid email address');
                return false;
            } else {
                input.style.borderColor = '#10b981';
                input.style.backgroundColor = '#f0fdf4';
                hideTooltip(input);
                return true;
            }
        }
        
        function showTooltip(element, message) {
            // Remove existing tooltip
            hideTooltip(element);
            
            const tooltip = document.createElement('div');
            tooltip.className = 'error-tooltip';
            tooltip.textContent = message;
            tooltip.style.cssText = `
                position: absolute;
                bottom: -25px;
                left: 0;
                background: #dc2626;
                color: white;
                padding: 5px 10px;
                border-radius: 5px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 1000;
                animation: fadeIn 0.3s ease;
            `;
            
            element.parentElement.style.position = 'relative';
            element.parentElement.appendChild(tooltip);
        }
        
        function hideTooltip(element) {
            const tooltip = element.parentElement.querySelector('.error-tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        }

        // Ensure global background is properly initialized
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize global background particles if needed
            const particlesContainer = document.querySelector('.global-particles');
            if (particlesContainer && particlesContainer.children.length === 0) {
                // Add some particles dynamically if none exist
                for (let i = 0; i < 8; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'global-particle';
                    particle.style.left = (10 + i * 10) + '%';
                    particle.style.animationDelay = (i * 2) + 's';
                    particlesContainer.appendChild(particle);
                }
            }
        });
    </script>
</body>
</html>
