<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trivia Game - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    @stack('styles')
    <style>
        .option.selected {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end)) !important;
            color: var(--text-accent) !important;
            border-color: var(--gradient-start) !important;
            animation: pulse 0.5s ease-in-out;
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
        }
        
        .keyboard-hints {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .hint-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .hint-text i {
            color: var(--gradient-start);
            font-size: 1rem;
        }
        
        kbd {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-family: 'Courier New', monospace;
        }
        
        .btn:disabled {
            background: rgba(156, 163, 175, 0.3) !important;
            color: rgba(107, 114, 128, 0.7) !important;
            cursor: not-allowed !important;
            transform: none !important;
            opacity: 0.6 !important;
            backdrop-filter: blur(2px) !important;
        }
        
        .container {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @media (prefers-color-scheme: dark) {
            .container {
                box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.6), 
                           0 0 0 1px rgba(255, 255, 255, 0.1);
            }
        }
        
        @media (max-width: 768px) {
            .keyboard-hints {
                margin-bottom: 1.5rem;
                padding: 0.75rem 1rem;
            }
            
            .hint-text {
                font-size: 0.8rem;
                gap: 0.25rem;
            }
            
            kbd {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body class="global-page trivia-page">
    <!-- Global Background -->
    <div class="global-background"></div>
    
    <!-- Global Particles -->
    <div class="global-particles">
        <div class="global-particle" style="left: 8%; animation-delay: 1.5s;"></div>
        <div class="global-particle" style="left: 18%; animation-delay: 3.5s;"></div>
        <div class="global-particle" style="left: 28%; animation-delay: 5.5s;"></div>
        <div class="global-particle" style="left: 38%; animation-delay: 7.5s;"></div>
        <div class="global-particle" style="left: 48%; animation-delay: 9.5s;"></div>
        <div class="global-particle" style="left: 58%; animation-delay: 11.5s;"></div>
        <div class="global-particle" style="left: 68%; animation-delay: 13.5s;"></div>
        <div class="global-particle" style="left: 78%; animation-delay: 15.5s;"></div>
        <div class="global-particle" style="left: 88%; animation-delay: 17.5s;"></div>
    </div>
    
    <div class="container">
        @auth
            @if(auth()->user()->is_admin ?? false)
                <div class="text-right mb-3">
                    <a href="{{ route('admin.dashboard') }}" class="admin-link">Admin Panel</a>
                </div>
            @endif
        @endauth
        @yield('content')
    </div>
    
    <!-- Global Loader Overlay -->
    <x-loader 
        overlay="true" 
        id="app-loader-overlay" 
        size="medium"
        class="loader-hidden"
    />

    <script src="{{ asset('js/loader.js') }}"></script>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const options = document.querySelectorAll('.option');
            const submitBtn = document.getElementById('submit-btn');
            
            options.forEach(option => {
                option.addEventListener('click', function() {
                    options.forEach(opt => opt.classList.remove('selected'));
                    
                    this.classList.add('selected');
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        if (submitBtn) submitBtn.disabled = false;
                    }
                });
            });

            const timerElement = document.getElementById('timer');
            const currentPath = window.location.pathname;
            
            if (timerElement) {
                let gameStartTime = localStorage.getItem('gameplayStartTime');
                
                if (gameStartTime) {
                    gameStartTime = parseInt(gameStartTime);
                    
                    function updateTimer() {
                        const now = Date.now();
                        const elapsed = Math.floor((now - gameStartTime) / 1000);
                        const minutes = Math.floor(elapsed / 60);
                        const seconds = elapsed % 60;
                        const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                        
                        timerElement.textContent = timeString;
                    }
                    
                    updateTimer();
                    if (!currentPath.includes('/game-over') && !currentPath.includes('/game-won')) {
                        window.gameTimerInterval = setInterval(updateTimer, 1000);
                    }
                } else {
                    timerElement.textContent = '0:00';
                }
            }

            if (!currentPath.includes('/question') && 
                !currentPath.includes('/correct') &&
                !currentPath.includes('/game-over') &&
                !currentPath.includes('/game-won')) {
                localStorage.removeItem('gameplayStartTime');
                localStorage.removeItem('finalGameDuration');
            }
        });
    </script>
</body>
</html>
