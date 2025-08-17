@extends('trivia.layout')

@section('title', 'Question')

@section('content')
    <h1 class="main-title animate-fadeInUp">Trivia Game</h1>
    
    <div class="stats-card animate-fadeInUp">
        <div class="stat">
            <div class="stat-label">Question</div>
            <div class="stat-value">{{ $current_question }}/20</div>
        </div>
        <div class="stat">
            <div class="stat-label">Correct Answers</div>
            <div class="stat-value">{{ $correct_answers }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total Time</div>
            <div class="stat-value" id="timer">0:00</div>
        </div>
        <div class="stat">
            <div class="stat-label">Progress</div>
            <div class="stat-value">{{ round(($current_question / 20) * 100) }}%</div>
        </div>
    </div>
    
    <div class="question-card animate-bounceIn">
        <h2 class="section-title">{{ $question }}</h2>
        
        <!-- Progress Bar -->
        <div style="background: var(--glass-bg); border-radius: 20px; height: 8px; margin: 1.5rem 0; overflow: hidden; border: 1px solid var(--glass-border);">
            <div style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end)); height: 100%; width: {{ ($current_question / 20) * 100 }}%; border-radius: 20px; transition: width 0.5s ease;"></div>
        </div>
    </div>
    
    @if(isset($is_admin) && $is_admin)
        <div class="result-card warning animate-fadeInUp">
            <h3 class="text-xl font-bold mb-3">Admin Testing Panel</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div style="background: var(--glass-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                    <strong style="color: var(--gradient-start);">Correct Answer:</strong><br>
                    <span style="color: var(--text-primary);">{{ $correct_answer }}</span>
                </div>
                @if(isset($full_fact) && $full_fact)
                    <div style="background: var(--glass-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                        <strong style="color: var(--gradient-start);">Source Fact:</strong><br>
                        <span style="color: var(--text-primary); font-size: 0.9rem;">{{ $full_fact }}</span>
                    </div>
                @endif
            </div>
            <div class="text-xs mt-3" style="color: var(--text-secondary);">
                This panel is only visible to administrators for testing purposes.
            </div>
        </div>
    @endif
    
    <form method="POST" action="{{ route('trivia.answer') }}" id="answer-form" class="animate-fadeInUp">
        @csrf
        
        <!-- Keyboard shortcuts hint -->
        <div class="keyboard-hints animate-fadeInUp" style="animation-delay: 0.2s;">
            <div class="hint-text">
                <i class="fas fa-keyboard"></i>
                Press <kbd>A</kbd>, <kbd>B</kbd>, <kbd>C</kbd>, <kbd>D</kbd> or <kbd>1</kbd>-<kbd>4</kbd> to select • <kbd>Enter</kbd> to submit
            </div>
        </div>
        
        <div class="options-grid">
            @foreach($options as $index => $option)
                <label class="option animate-fadeInUp" style="animation-delay: {{ $index * 0.1 }}s;">
                    <input type="radio" name="answer" value="{{ $option }}" required>
                    <span class="option-text">
                        <span class="text-2xl mr-2">{{ ['A', 'B', 'C', 'D'][$index] }}</span>
                        {{ $option }}
                    </span>
                </label>
            @endforeach
        </div>
        
        <button type="submit" class="btn btn-primary animate-fadeInUp" id="submit-btn" disabled style="animation-delay: 0.5s;">
            <span id="submit-text">Submit Answer</span>
            <span id="submit-loader" style="display: none;">
                <x-loader size="small" class="loader-inline" style="margin-left: 8px;" />
            </span>
        </button>
    </form>

    @if($errors->any())
        <div class="result-card error animate-bounceIn">
            <strong>Error!</strong> Please select an answer before submitting.
        </div>
    @endif

    @if(isset($gameplay_start_time))
    <script>
        if (!localStorage.getItem('gameplayStartTime')) {
            @if(is_string($gameplay_start_time))
                const serverStartTime = new Date('{{ $gameplay_start_time }}').getTime();
            @else
                const serverStartTime = new Date('{{ $gameplay_start_time->toISOString() }}').getTime();
            @endif
            localStorage.setItem('gameplayStartTime', serverStartTime);
        }
    </script>
    @endif

    <script>
        // Enable option selection when clicked
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[name="answer"]');
            const submitBtn = document.getElementById('submit-btn');
            
            // Enable submit button when an option is selected
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    submitBtn.disabled = false;
                });
            });
        });

        // Keyboard shortcuts for answer selection
        document.addEventListener('keydown', function(e) {
            const key = e.key.toLowerCase();
            const radioButtons = document.querySelectorAll('input[name="answer"]');
            const submitBtn = document.getElementById('submit-btn');
            
            // Handle A, B, C, D keys or 1, 2, 3, 4 keys
            let optionIndex = -1;
            if (key === 'a' || key === '1') optionIndex = 0;
            else if (key === 'b' || key === '2') optionIndex = 1;
            else if (key === 'c' || key === '3') optionIndex = 2;
            else if (key === 'd' || key === '4') optionIndex = 3;
            
            if (optionIndex >= 0 && optionIndex < radioButtons.length) {
                radioButtons[optionIndex].checked = true;
                submitBtn.disabled = false;
                
                // Add visual feedback
                const selectedOption = radioButtons[optionIndex].closest('.option');
                document.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
                selectedOption.classList.add('selected');
                
                e.preventDefault();
            }
            
            // Handle Enter key to submit
            if (key === 'enter' && !submitBtn.disabled) {
                e.preventDefault();
                submitBtn.click();
            }
        });

        // Enhanced form submission with loader
        document.getElementById('answer-form').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitLoader = document.getElementById('submit-loader');
            
            // Show inline loader in button
            submitText.style.display = 'none';
            submitLoader.style.display = 'inline-flex';
            submitBtn.disabled = true;
            
            // Also show overlay loader for dramatic effect
            showLoader();
            
            // Add a small delay to show the loader effect
            setTimeout(() => {
                // Let the form submit naturally
                this.submit();
            }, 800);
            
            // Prevent default to control timing
            e.preventDefault();
        });

        // Preload next page with loader
        window.addEventListener('beforeunload', function() {
            showLoader();
        });
    </script>
@endsection
