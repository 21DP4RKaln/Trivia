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
                    placeholder="Enter your email address" 
                    value="{{ old('email') }}" 
                    required
                    autocomplete="email"
                >
                
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
        // Add form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('.auth-button');
                button.disabled = true;
                button.innerHTML = 'Sending...';
            });
        });
    </script>
</body>
</html>
