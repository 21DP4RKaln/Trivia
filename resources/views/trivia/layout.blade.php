<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trivia Game - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .option.selected {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end)) !important;
            color: var(--text-accent) !important;
            border-color: var(--gradient-start) !important;
            animation: pulse 0.5s ease-in-out;
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
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
    </style>
</head>
<body>
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
