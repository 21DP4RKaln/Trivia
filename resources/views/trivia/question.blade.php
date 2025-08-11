@extends('trivia.layout')

@section('title', 'Question')

@section('content')
    <h1>Trivia Game</h1>
    
    <div class="game-stats">
        <div class="stat">
            <div class="stat-label">Question</div>
            <div class="stat-value">{{ $current_question }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Correct answers</div>
            <div class="stat-value">{{ $correct_answers }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Objective</div>
            <div class="stat-value">20</div>
        </div>
    </div>
    
    <div class="question">
        <h2>{{ $question }}</h2>
    </div>
    
    @if(isset($is_admin) && $is_admin)
        <div class="admin-testing-panel" style="background: #ffe6e6; border: 2px solid #ff9999; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <h3 style="color: #cc0000; margin: 0 0 10px 0;">Admin Testing Panel</h3>
            <div style="margin-bottom: 10px;">
                <strong>Correct Answer:</strong> {{ $correct_answer }}
            </div>
            @if(isset($full_fact) && $full_fact)
                <div style="margin-bottom: 10px;">
                    <strong>Source Fact:</strong> {{ $full_fact }}
                </div>
            @endif
            <div style="font-size: 12px; color: #666;">
                This panel is only visible to administrators for testing purposes.
            </div>
        </div>
    @endif
    
    <form method="POST" action="{{ route('trivia.answer') }}" id="answer-form">
        @csrf
        <div class="options">
            @foreach($options as $option)
                <label class="option">
                    <input type="radio" name="answer" value="{{ $option }}" required>
                    <span class="option-text">{{ $option }}</span>
                </label>
            @endforeach
        </div>
        
        <button type="submit" class="btn" id="submit-btn" disabled>
            Submit a reply
        </button>
    </form>
    
    @if($errors->any())
        <div class="result-box error">
            <strong>Error!</strong> Please select an answer.
        </div>
    @endif
@endsection
