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
            Submit Answer
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
            const serverStartTime = new Date('{{ $gameplay_start_time->toISOString() }}').getTime();
            localStorage.setItem('gameplayStartTime', serverStartTime);
        }
    </script>
    @endif
@endsection
