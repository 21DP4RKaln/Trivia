@extends('trivia.layout')

@section('title', 'Question')

@section('content')
    <h1>🧠 Trivia Game</h1>
    
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
