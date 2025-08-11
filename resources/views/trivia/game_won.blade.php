@extends('trivia.layout')

@section('title', 'Victory!')

@section('content')
    <h1>Congratulations! You won!</h1>
    
    <div class="result-box success">
        <h2>Fantastic performance!</h2>
        <p>You answered all  <strong>{{ $correct_answers }}</strong> questions correctly!</p>
        <p>You are a true trivia expert!</p>
        
        @auth
            <p><em>This perfect game has been saved to your account history!</em></p>
        @else
            <p><em><a href="{{ route('register') }}">Create an account</a> to track your perfect games and compete with yourself!</em></p>
        @endauth
    </div>
    
    <div style="font-size: 4rem; margin: 2rem 0;">
        🎉🎊🏆🎊🎉
    </div>
    
    <div class="game-stats">
        <div class="stat">
            <div class="stat-label">Correct answers</div>
            <div class="stat-value">{{ $correct_answers }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Accuracy</div>
            <div class="stat-value">100%</div>
        </div>
        <div class="stat">
            <div class="stat-label">Result</div>
            <div class="stat-value">PERFECT!</div>
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
    
    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            View Dashboard
        </a>
    @endauth
@endsection
