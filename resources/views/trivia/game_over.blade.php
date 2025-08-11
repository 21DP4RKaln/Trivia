@extends('trivia.layout')

@section('title', 'The game is over')

@section('content')
    <h1>The game is over!</h1>
    
    <div class="result-box error">
        <h2>Unfortunately, your answer is incorrect!</h2>
        <p>You answered correctly to <strong>{{ $correct_answers }}</strong> questions.</p>
    </div>
    
    @if($last_question)
        <div class="last-question">
            <h3>Last question:</h3>
            <p><strong>{{ $last_question['question'] }}</strong></p>
            
            <div style="margin: 1rem 0;">
                <p>Your answer: <span style="color: #dc3545; font-weight: bold;">{{ $user_answer }}</span></p>
                <p>The correct answer: <span style="color: #28a745; font-weight: bold;">{{ $correct_answer }}</span></p>
            </div>
            
            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                <p><strong>Fact:</strong> {{ $last_question['full_fact'] }}</p>
            </div>
        </div>
    @endif
    
    <div class="game-stats">
        <div class="stat">
            <div class="stat-label">Correct answers</div>
            <div class="stat-value">{{ $correct_answers }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">From total</div>
            <div class="stat-value">20</div>
        </div>
        <div class="stat">
            <div class="stat-label">Percentages</div>
            <div class="stat-value">{{ round(($correct_answers / 20) * 100) }}%</div>
        </div>
    </div>
    
    <form method="POST" action="{{ route('trivia.start') }}">
        @csrf
        <button type="submit" class="btn btn-success">
            Play again
        </button>
    </form>
    
    <a href="{{ route('trivia.index') }}" class="btn">
        Home
    </a>
@endsection
