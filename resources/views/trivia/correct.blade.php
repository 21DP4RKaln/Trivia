@extends('trivia.layout')

@section('title', 'Correct!')

@section('content')
    <h1 class="main-title glass-text animate-bounceIn">Brilliant!</h1>
    
    <div class="result-card success animate-bounceIn" style="animation-delay: 0.2s;">
        <div class="text-center mb-4">
            <h2 class="text-3xl font-bold mb-4" style="color: var(--text-primary);">Outstanding Answer!</h2>
            <p class="text-xl" style="color: var(--text-primary);">
                You're crushing it with <span class="glass-text font-bold">{{ $correct_answers }}</span> correct answers!
            </p>
        </div>
    </div>
    
    <div class="stats-card animate-fadeInUp" style="animation-delay: 0.4s;">
        <div class="stat">
            <div class="stat-label">Streak</div>
            <div class="stat-value">{{ $correct_answers }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Next Question</div>
            <div class="stat-value">{{ $current_question }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total Time</div>
            <div class="stat-value" id="timer">0:00</div>
        </div>
        <div class="stat">
            <div class="stat-label">Goal</div>
            <div class="stat-value">20</div>
        </div>
    </div>
    
    <!-- Progress visualization -->
    <div class="question-card animate-fadeInUp" style="animation-delay: 0.6s;">
        <h3 class="section-title glass-text">Progress to Victory</h3>
        <div style="background: var(--glass-bg); border-radius: 20px; height: 12px; margin: 1.5rem 0; overflow: hidden; border: 1px solid var(--glass-border);">
            <div style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end)); height: 100%; width: {{ ($correct_answers / 20) * 100 }}%; border-radius: 20px; transition: width 0.8s ease; box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);"></div>
        </div>
        <p style="color: var(--text-secondary);">{{ 20 - $correct_answers }} questions remaining to victory!</p>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8 animate-fadeInUp" style="animation-delay: 0.8s;">
        <a href="{{ route('trivia.question') }}" class="btn btn-primary transform hover:scale-110 transition-all duration-300">
            Continue Quest
        </a>
        
        <a href="{{ route('trivia.index') }}" class="btn btn-outline">
            Home Base
        </a>
    </div>
    
    @if(isset($gameplay_start_time))
    <script>
        // Ensure timer start time is set from server when on correct answer page
        if (!localStorage.getItem('gameplayStartTime')) {
            const serverStartTime = new Date('{{ $gameplay_start_time->toISOString() }}').getTime();
            console.log('Setting timer start time from correct page:', new Date(serverStartTime));
            localStorage.setItem('gameplayStartTime', serverStartTime);
        }
        
        // Timer continues running and displays the same total time
    </script>
    @endif
@endsection
