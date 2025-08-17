<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Number Trivia Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/css/auth/auth.css', 'resources/js/app.js'])
    @vite('resources/css/mobile-responsive.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="global-page auth-page">
    <!-- Global Background -->
    <div class="global-background"></div>
    
    <!-- Global Particles -->
    <div class="global-particles">
        <div class="global-particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="global-particle" style="left: 22%; animation-delay: 2s;"></div>
        <div class="global-particle" style="left: 34%; animation-delay: 4s;"></div>
        <div class="global-particle" style="left: 46%; animation-delay: 6s;"></div>
        <div class="global-particle" style="left: 58%; animation-delay: 8s;"></div>
        <div class="global-particle" style="left: 70%; animation-delay: 10s;"></div>
        <div class="global-particle" style="left: 82%; animation-delay: 12s;"></div>
        <div class="global-particle" style="left: 94%; animation-delay: 14s;"></div>
        
        <!-- Additional particles for better coverage -->
        <div class="global-particle" style="left: 16%; animation-delay: 0.8s;"></div>
        <div class="global-particle" style="left: 28%; animation-delay: 2.8s;"></div>
        <div class="global-particle" style="left: 40%; animation-delay: 4.8s;"></div>
        <div class="global-particle" style="left: 52%; animation-delay: 6.8s;"></div>
        <div class="global-particle" style="left: 64%; animation-delay: 8.8s;"></div>
        <div class="global-particle" style="left: 76%; animation-delay: 10.8s;"></div>
        <div class="global-particle" style="left: 88%; animation-delay: 12.8s;"></div>
    </div>

    <!-- Back Button -->
    <a href="{{ route('dashboard') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="verify-email-page">
        <div class="verify-email-container">
        
        <h1 class="verify-email-title">Verify Your Email Address</h1>
        
        <div class="verify-email-content">
            @if(session('status'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-message">
                    @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <p class="verify-email-subtitle">
                We've sent a verification email to <strong>{{ Auth::user()->email }}</strong>. Please check your inbox and click the verification link to activate your account.
            </p>

            <div class="verification-benefits">
                <h3><i class="fas fa-shield-check"></i> Why verify your email?</h3>
                <ul>
                    <li><i class="fas fa-check"></i> Secure your account</li>
                    <li><i class="fas fa-check"></i> Receive game updates</li>
                    <li><i class="fas fa-check"></i> Account recovery options</li>
                    <li><i class="fas fa-check"></i> Access all features</li>
                </ul>
            </div>

            <div class="verification-actions">
                <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
                    @csrf
                    <button type="submit" class="auth-button resend-button">
                        <i class="fas fa-paper-plane"></i>
                        Resend Verification Email
                    </button>
                </form>

                <div class="alternative-actions">
                    
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="alt-link logout-link">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="help-section">
                <h4><i class="fas fa-question-circle"></i> Need Help?</h4>
                <p>Check your spam folder if you don't see the email. The verification link expires in 60 minutes.</p>
                <div class="contact-info">
                    <p>Still having trouble? Contact us at: <a href="mailto:support@trivia.com">support@trivia.com</a></p>
                </div>            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resendForm = document.querySelector('.resend-form');
    const resendButton = document.querySelector('.resend-button');
    
    if (resendForm && resendButton) {
        resendForm.addEventListener('submit', function(e) {
            resendButton.disabled = true;
            resendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            // Re-enable button after 10 seconds
            setTimeout(() => {
                resendButton.disabled = false;
                resendButton.innerHTML = '<i class="fas fa-paper-plane"></i> Resend Verification Email';
            }, 10000);
        });
    }

    // Auto-refresh to check for verification status every 30 seconds
    let refreshInterval = setInterval(() => {
        fetch('{{ route("dashboard") }}', {
            method: 'HEAD',
            credentials: 'same-origin'
        }).then(response => {
            if (response.ok) {
                // Check if user is verified by making a quick request
                fetch('/email/verify/check-status')
                    .then(r => r.json())
                    .then(data => {
                        if (data.verified) {
                            clearInterval(refreshInterval);
                            window.location.href = '{{ route("dashboard") }}';
                        }
                    })
                    .catch(() => {
                    });
            }
        }).catch(() => {
        });
    }, 30000);
    
    // Clear interval after 10 minutes
    setTimeout(() => {
        clearInterval(refreshInterval);
    }, 600000);
});
</script>

<style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    overflow-y: auto;
}

body {
    position: relative;
}

.verify-email-page {
    min-height: 100vh;
    padding: 6rem 2rem 4rem;
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
}

.back-button {
    position: fixed;
    top: 2rem;
    left: 2rem;
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: white;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    z-index: 1000;
}

.back-button:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    color: white;
    text-decoration: none;
}

.verify-email-container {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 3rem;
    max-width: 600px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.icon-container {
    background: linear-gradient(135deg, #10b981, #8b5cf6);
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
}

.icon-container i {
    font-size: 3rem;
    color: white;
}

.verify-email-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.verify-email-subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 2rem;
    line-height: 1.6;
}

.verification-benefits {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 1.5rem;
    margin: 2rem 0;
    text-align: left;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.verification-benefits h3 {
    color: white;
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.verification-benefits ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.verification-benefits li {
    padding: 0.5rem 0;
    color: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.verification-benefits li i {
    color: #10b981;
    font-size: 0.9rem;
}

.verification-actions {
    margin: 2rem 0;
}

.resend-button {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    margin-bottom: 1.5rem;
    width: 100%;
    max-width: 300px;
}

.resend-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.resend-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.alternative-actions {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.alt-link, .logout-link {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
}

.alt-link:hover, .logout-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
}

.logout-form {
    display: inline;
}

.help-section {
    background: rgba(254, 243, 199, 0.2);
    border-radius: 15px;
    padding: 1.5rem;
    margin-top: 2rem;
    text-align: left;
    border: 1px solid rgba(254, 243, 199, 0.3);
}

.help-section h4 {
    color: #fbbf24;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.help-section p {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.contact-info a {
    color: #10b981;
    text-decoration: none;
    font-weight: 600;
}

.contact-info a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .verify-email-page {
        padding: 5rem 1rem 3rem;
    }
    
    .verify-email-container {
        padding: 2rem;
    }
    
    .verify-email-title {
        font-size: 2rem;
    }
    
    .alternative-actions {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    
    .back-button {
        top: 1rem;
        left: 1rem;
        width: 45px;
        height: 45px;
        font-size: 1rem;
    }
      .icon-container {
        width: 80px;
        height: 80px;
    }
    
    .icon-container i {
        font-size: 2rem;
    }
}

.success-message {
    background: rgba(16, 185, 129, 0.2);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
    padding: 1rem;
    border-radius: 10px;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.error-message {
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
    padding: 1rem;
    border-radius: 10px;
    margin: 1rem 0;
}

.error-message div {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0.25rem 0;
}
</style>
</body>
</html>
