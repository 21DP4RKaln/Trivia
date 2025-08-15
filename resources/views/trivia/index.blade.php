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
    
    <form method="POST" action="{{ route('trivia.start') }}" class="animate-fadeInUp">
        @csrf
        <button type="submit" class="btn btn-primary transform hover:scale-105 transition-all duration-300 animate-pulse" style="font-size: 1.25rem; padding: 1rem 2rem;" onclick="initializeGameTimer();">
            Start Game
        </button>
    </form>
    
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
    </script>
@endsection
