@extends('trivia.layout')

@section('title', 'Sākums')

@section('content')
    <div class="auth-status animate-fadeInUp">
        @auth
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-700">Welcome back, <strong class="glass-text">{{ Auth::user()->name }}</strong>!</p>
                <div class="auth-actions flex gap-3">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-outline">Logout</button>
                    </form>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">View Dashboard</a>
                </div>
            </div>
        @else
            <div class="auth-actions flex justify-center gap-3">
                <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
            </div>
        @endauth
    </div>
    
    <h1 class="main-title animate-fadeInUp">Trivia Game</h1>
    
    <div class="question-card animate-bounceIn">
        <h2 class="section-title">Welcome to the Trivia Game!</h2>
        <p class="text-lg leading-relaxed mb-6" style="color: var(--text-primary);">
            Challenge your mind with fascinating number facts! Answer correctly to advance through 20 increasingly engaging questions.
        </p>
        
        @guest
            <div class="result-card success mb-6">
                <p class="font-semibold" style="color: var(--text-primary);">
                    Create an account to track your progress, compete with friends, and unlock achievement badges!
                </p>
            </div>
        @endguest
        
        <h3 class="text-xl font-bold mb-4 glass-text">Game Rules:</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left max-w-2xl mx-auto">
            <div class="flex items-start p-3 rounded-lg" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                <span style="color: var(--text-primary);">4 answer choices per question</span>
            </div>
            <div class="flex items-start p-3 rounded-lg" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                <span style="color: var(--text-primary);">Reach 20 correct answers to win</span>
            </div>
            <div class="flex items-start p-3 rounded-lg" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                <span style="color: var(--text-primary);">Facts from numbersapi.com</span>
            </div>
        </div>
    </div>
    
    @if(isset($savedGame) && $savedGame)
        <div class="result-card info animate-fadeInUp mb-6">
            <h3 class="text-xl font-bold mb-3 glass-text">Continue Your Game</h3>
            <p class="mb-4" style="color: var(--text-primary);">
                You have a saved game with <strong>{{ $savedGame->game_state['correct_answers'] ?? 0 }}</strong> correct answers 
                at question <strong>{{ $savedGame->game_state['current_question'] ?? 1 }}</strong>.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <form method="POST" action="{{ route('trivia.continue') }}" class="inline" id="continue-game-form">
                    @csrf
                    <button type="submit" class="btn btn-success" id="continue-game-btn">
                        <i class="fas fa-play"></i> 
                        <span id="continue-text">Continue Saved Game</span>
                        <span id="continue-loader" style="display: none;">
                            <x-loader size="small" class="loader-inline" style="margin-left: 8px;" />
                        </span>
                    </button>
                </form>
                <form method="POST" action="{{ route('trivia.start') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-outline" onclick="confirmNewGame(event)">
                        <i class="fas fa-plus"></i> Start New Game
                    </button>
                </form>
                <form method="POST" action="{{ route('trivia.abandon') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="confirmAbandonGame(event)">
                        <i class="fas fa-trash"></i> Abandon Game
                    </button>
                </form>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('trivia.start') }}" class="animate-fadeInUp" id="start-game-form">
            @csrf
            <button type="submit" class="btn btn-primary transform hover:scale-105 transition-all duration-300 animate-pulse" style="font-size: 1.25rem; padding: 1rem 2rem;" id="start-game-btn">
                <span id="start-text">Start Game</span>
                <span id="start-loader" style="display: none;">
                    <x-loader size="small" class="loader-inline" style="margin-left: 8px;" />
                </span>
            </button>
        </form>
    @endif
    
    <script>
        function initializeGameTimer() {
            localStorage.removeItem('gameplayStartTime');
            localStorage.removeItem('finalGameDuration');
            localStorage.removeItem('questionTimes');
            localStorage.removeItem('currentQuestionStartTime');
            localStorage.removeItem('currentQuestionElapsed');
            
            localStorage.setItem('gameplayStartTime', Date.now());
            console.log('Game timer initialized at:', new Date());
        }
        
        function confirmNewGame(event) {
            if (!confirm('Are you sure you want to start a new game? Your saved game will be lost.')) {
                event.preventDefault();
                return false;
            }
            initializeGameTimer();
            return true;
        }
        
        function confirmAbandonGame(event) {
            if (!confirm('Are you sure you want to abandon your saved game? This action cannot be undone.')) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Enhanced game start with loader
        document.addEventListener('DOMContentLoaded', function() {
            // Start Game Form
            const startForm = document.getElementById('start-game-form');
            if (startForm) {
                startForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const startBtn = document.getElementById('start-game-btn');
                    const startText = document.getElementById('start-text');
                    const startLoader = document.getElementById('start-loader');
                    
                    // Initialize game timer
                    initializeGameTimer();
                    
                    // Show button loader
                    startText.style.display = 'none';
                    startLoader.style.display = 'inline-flex';
                    startBtn.disabled = true;
                    
                    // Show overlay loader
                    showLoader();
                    
                    // Submit after showing loader effect
                    setTimeout(() => {
                        this.submit();
                    }, 1000);
                });
            }

            // Continue Game Form
            const continueForm = document.getElementById('continue-game-form');
            if (continueForm) {
                continueForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const continueBtn = document.getElementById('continue-game-btn');
                    const continueText = document.getElementById('continue-text');
                    const continueLoader = document.getElementById('continue-loader');
                    
                    // Show button loader
                    continueText.style.display = 'none';
                    continueLoader.style.display = 'inline-flex';
                    continueBtn.disabled = true;
                    
                    // Show overlay loader
                    showLoader();
                    
                    // Submit after showing loader effect
                    setTimeout(() => {
                        this.submit();
                    }, 800);
                });
            }
        });
    </script>
    
    <script>
        // Check for saved game status on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Display any flash messages
            @if(session('error'))
                alert('{{ session('error') }}');
            @endif
            
            @if(session('success'))
                alert('{{ session('success') }}');
            @endif
        });
    </script>
@endsection
