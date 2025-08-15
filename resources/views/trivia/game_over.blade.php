@extends('trivia.layout')

@section('title', 'Game Over')

@section('content')
    <h1 class="main-title glass-text animate-bounceIn">Game Complete!</h1>
    
    <div class="result-card error animate-bounceIn" style="animation-delay: 0.2s;">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold mb-4" style="color: var(--text-primary);">Good Effort!</h2>
            <p class="text-xl mb-4" style="color: var(--text-primary);">
                You scored <span class="glass-text font-bold text-2xl">{{ $correct_answers }}/20</span> correct answers!
            </p>
            
            @auth
                <div class="result-card success mb-4">
                    <p class="font-semibold" style="color: var(--text-primary);">
                        Your game has been saved to your profile!
                    </p>
                </div>
            @else
                <div class="result-card warning mb-4">
                    <p style="color: var(--text-primary);">
                        <a href="{{ route('register') }}" class="glass-text font-bold underline hover:no-underline">Create an account</a> to track your progress and compete with friends!
                    </p>
                </div>
            @endauth
        </div>
    </div>
    
    @if(isset($last_question))
        <div class="question-card animate-fadeInUp" style="animation-delay: 0.4s;">
            <h3 class="text-xl font-bold mb-4 glass-text">Last Question Review:</h3>
            <p class="text-lg font-semibold mb-6" style="color: var(--text-primary);">{{ $last_question['question'] }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="p-4 rounded-xl border-2" style="background: rgba(239, 68, 68, 0.1); border-color: #ef4444;">
                    <p style="color: var(--text-primary);"><strong>Your Answer:</strong></p>
                    <p class="font-bold" style="color: #ef4444;">{{ $user_answer }}</p>
                </div>
                <div class="p-4 rounded-xl border-2" style="background: rgba(16, 185, 129, 0.1); border-color: var(--gradient-start);">
                    <p style="color: var(--text-primary);"><strong>Correct Answer:</strong></p>
                    <p class="font-bold glass-text">{{ $correct_answer }}</p>
                </div>
            </div>
            
            <div class="p-4 rounded-xl" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                <p style="color: var(--text-primary);"><strong>Fascinating Fact:</strong></p>
                <p style="color: var(--text-secondary);">{{ $last_question['full_fact'] }}</p>
            </div>
        </div>
    @endif
    
    <script>
        // Mark the last question as incorrect before displaying summary
        document.addEventListener('DOMContentLoaded', function() {
            // Mark the last question as incorrect since this is game over (wrong answer)
            let questionTimes = JSON.parse(localStorage.getItem('questionTimes') || '[]');
            if (questionTimes.length > 0) {
                questionTimes[questionTimes.length - 1].correct = false;
                localStorage.setItem('questionTimes', JSON.stringify(questionTimes));
            }
        });
    </script>
    
    <div class="stats-card animate-fadeInUp" style="animation-delay: 0.6s;">
        <div class="stat">
            <div class="stat-label">Final Score</div>
            <div class="stat-value">{{ $correct_answers }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total Questions</div>
            <div class="stat-value">20</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total Time</div>
            <div class="stat-value" id="final-timer">Calculating...</div>
        </div>
        <div class="stat">
            <div class="stat-label">Accuracy</div>
            <div class="stat-value">{{ round(($correct_answers / 20) * 100) }}%</div>
        </div>
    </div>
    
    <!-- Performance feedback -->
    <div class="question-card animate-fadeInUp" style="animation-delay: 0.8s;">
        @if($correct_answers >= 15)
            <h3 class="glass-text text-2xl font-bold mb-2">Outstanding Performance!</h3>
            <p style="color: var(--text-primary);">You're a trivia master! Keep up the excellent work!</p>
        @elseif($correct_answers >= 10)
            <h3 class="glass-text text-2xl font-bold mb-2">Good Job!</h3>
            <p style="color: var(--text-primary);">Solid performance! A few more correct answers and you'll be unstoppable!</p>
        @else
            <h3 class="glass-text text-2xl font-bold mb-2">Keep Learning!</h3>
            <p style="color: var(--text-primary);">Every expert was once a beginner. Practice makes perfect!</p>
        @endif
    </div>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8 animate-fadeInUp" style="animation-delay: 1s;">
        <form method="POST" action="{{ route('trivia.start') }}" class="inline">
            @csrf
            <input type="hidden" name="game_duration" id="game-duration-input">
            <button type="submit" class="btn btn-primary transform hover:scale-110 transition-all duration-300">
                Play Again
            </button>
        </form>
        
        <a href="{{ route('trivia.index') }}" class="btn btn-secondary">
            Home
        </a>
        
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-outline">
                View Stats
            </a>
        @endauth
    </div>

    <script>
        console.log('=== GAME OVER PAGE DEBUG START ===');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Game Over - DOMContentLoaded fired');
            
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
                console.log('Game Over - Final time calculated:', formattedTime);
                
                // Store the final duration
                localStorage.setItem('finalGameDuration', calculatedDuration);
            } else if (finalTimerElement) {
                finalTimerElement.textContent = '0:00';
                console.log('Game Over - No start time found, showing 0:00');
            }
            
            // Display simplified question times breakdown
            displaySimpleQuestionBreakdown();
            
            // Send final duration to server
            setTimeout(() => {
                sendDurationToServer(calculatedDuration);
            }, 200);
        });
        
        function displaySimpleQuestionBreakdown() {
            const timesBreakdown = document.getElementById('times-breakdown');
            
            if (timesBreakdown) {
                timesBreakdown.innerHTML = '<p style="color: var(--text-secondary);">Game completed in total time shown above</p>';
            }
        }
        
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
                    console.log('Game Over - Duration saved successfully:', data);
                    clearAllTimerData();
                })
                .catch(error => {
                    console.error('Game Over - Failed to save duration:', error);
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
        
        console.log('=== GAME OVER PAGE DEBUG END ===');
    </script>
@endsection
