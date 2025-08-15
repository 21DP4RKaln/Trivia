@extends('trivia.layout')

@section('title', 'Victory!')

@section('content')
    <h1 class="main-title glass-text animate-bounceIn">VICTORY!</h1>
    
    <div class="result-card success animate-bounceIn" style="animation-delay: 0.2s;">
        <div class="text-center mb-6">
            <h2 class="text-4xl font-bold mb-4" style="color: var(--text-primary);">PERFECT SCORE!</h2>
            <p class="text-2xl mb-6" style="color: var(--text-primary);">
                You answered all <span class="glass-text font-bold text-3xl">{{ $correct_answers }}/20</span> questions correctly!
            </p>
            <p class="text-xl glass-text font-bold">You are a MASTER!</p>
            
            @auth
                <div class="mt-6 p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(139, 92, 246, 0.2)); border: 1px solid var(--gradient-start);">
                    <p class="font-semibold" style="color: var(--text-primary);">
                        This perfect game is now immortalized in your profile!
                    </p>
                </div>
            @else
                <div class="mt-6 p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(16, 185, 129, 0.2)); border: 1px solid #f59e0b;">
                    <p style="color: var(--text-primary);">
                        <a href="{{ route('register') }}" class="glass-text font-bold underline hover:no-underline">Create an account</a> to save game and show off to friends!
                    </p>
                </div>
            @endauth
        </div>
    </div>
    
    <div class="stats-card animate-fadeInUp" style="animation-delay: 0.8s;">
        <div class="stat">
            <div class="stat-label">Final Score</div>
            <div class="stat-value">{{ $correct_answers }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total Time</div>
            <div class="stat-value" id="final-timer">Calculating...</div>
        </div>
        <div class="stat">
            <div class="stat-label">Accuracy</div>
            <div class="stat-value">100%</div>
        </div>
    </div>
    
    <!-- Motivational Message -->
    <div class="question-card animate-fadeInUp" style="animation-delay: 1s;">
        <h3 class="glass-text text-2xl font-bold mb-4">You've Reached Trivia Mastery!</h3>
        <p class="text-lg mb-4" style="color: var(--text-primary);">
            Your perfect score demonstrates exceptional knowledge and quick thinking. You've joined the elite ranks of trivia champions!
        </p>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8 animate-fadeInUp" style="animation-delay: 1.2s;">
        <form method="POST" action="{{ route('trivia.start') }}" class="inline">
            @csrf
            <button type="submit" class="btn btn-primary transform hover:scale-110 transition-all duration-300 animate-pulse">
                Challenge Yourself Again
            </button>
        </form>
        
        <a href="{{ route('trivia.index') }}" class="btn btn-secondary">
            Return Home
        </a>
        
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-outline">
                View Your Status
            </a>
        @endauth
    </div>

    <script>
        console.log('=== GAME WON PAGE DEBUG START ===');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Game Won - DOMContentLoaded fired');
            
            // Calculate final total time
            const finalTimerElement = document.getElementById('final-timer');
            const startTime = localStorage.getItem('gameplayStartTime');
            
            let calculatedDuration = 0;
            
            if (startTime && finalTimerElement) {
                const endTime = Date.now();
                calculatedDuration = Math.floor((endTime - parseInt(startTime)) / 1000);
                const minutes = Math.floor(calculatedDuration / 60);
                const seconds = calculatedDuration % 60;
                const formattedTime = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                finalTimerElement.textContent = formattedTime;
                console.log('Game Won - Final time calculated:', formattedTime);
                
                // Store the final duration
                localStorage.setItem('finalGameDuration', calculatedDuration);
            } else if (finalTimerElement) {
                finalTimerElement.textContent = '0:00';
                console.log('Game Won - No start time found, showing 0:00');
            }
            
            // Send final duration to server
            setTimeout(() => {
                sendDurationToServer(calculatedDuration);
            }, 200);
        });
        
        function sendDurationToServer(durationToSend) {
            if (durationToSend > 0) {
                fetch('{{ route("trivia.update-duration") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        duration: durationToSend
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Game Won - Duration saved successfully:', data);
                    clearAllTimerData();
                })
                .catch(error => {
                    console.error('Game Won - Failed to save duration:', error);
                    clearAllTimerData();
                });
            } else {
                clearAllTimerData();
            }
        }
        
        function clearAllTimerData() {
            localStorage.removeItem('gameplayStartTime');
            localStorage.removeItem('finalGameDuration');
        }
        
        function displayQuestionTimesSummary() {
            const questionTimes = JSON.parse(localStorage.getItem('questionTimes') || '[]');
            const timesBreakdown = document.getElementById('times-breakdown');
            const fastCount = document.getElementById('fast-count');
            const mediumCount = document.getElementById('medium-count');
            const slowCount = document.getElementById('slow-count');
            
            if (questionTimes.length === 0) {
                timesBreakdown.innerHTML = '<p style="color: var(--text-secondary);">No timing data available</p>';
                return;
            }
            
            let fast = 0, medium = 0, slow = 0;
            let html = '';
            
            questionTimes.forEach(qt => {
                const minutes = Math.floor(qt.duration / 60);
                const seconds = qt.duration % 60;
                const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                let timeColor = '#ef4444'; 
                if (qt.duration <= 5) {
                    timeColor = '#10b981';
                    fast++;
                } else if (qt.duration <= 10) {
                    timeColor = '#f59e0b';
                    medium++;
                } else {
                    slow++;
                }
                
                html += `
                    <div class="flex justify-between items-center p-2 rounded" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                        <span style="color: var(--text-primary);">Question ${qt.question} ${statusIcon}</span>
                        <span style="color: ${timeColor}; font-weight: bold;">${timeString}</span>
                    </div>
                `;
            });
            
            timesBreakdown.innerHTML = html;
            fastCount.textContent = fast;
            mediumCount.textContent = medium;
            slowCount.textContent = slow;
        }
        
        console.log('=== GAME WON PAGE DEBUG END ===');
    </script>
@endsection
