@extends('trivia.layout')

@section('title', 'Correct!')

@section('content')
    <h1>Correct!</h1>
    
    <div class="result-box success">
        <h2>Congratulations! Your answer is correct!</h2>
        <p>You continue the game with {{ $correct_answers }} correct answers.</p>
    </div>
    
    <div class="game-stats">
        <div class="stat">
            <div class="stat-label">Correct answers</div>
            <div class="stat-value">{{ $correct_answers }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Next question</div>
            <div class="stat-value">{{ $current_question }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Objective</div>
            <div class="stat-value">20</div>
        </div>
    </div>
    
    <a href="{{ route('trivia.question') }}" class="btn btn-success">
        Next question
    </a>
    
    <a href="{{ route('trivia.index') }}" class="btn">
        Home
    </a>
@endsection
