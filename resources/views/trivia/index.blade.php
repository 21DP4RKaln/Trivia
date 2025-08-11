@extends('trivia.layout')

@section('title', 'Sākums')

@section('content')
    <div class="auth-status">
        @auth
            <p>Welcome back, <strong>{{ Auth::user()->name }}</strong>!</p>
            <div class="auth-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">View Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline">Logout</button>
                </form>
            </div>
        @else
            <div class="auth-actions">
                <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
            </div>
        @endauth
    </div>
    
    <h1>Trivia Game</h1>
    
    <div class="question">
        <h2>Welcome to the Trivia Game!</h2>
        <p>In this game, you will be asked questions about numbers. You must answer correctly to continue playing.</p>
        
        @guest
            <p><strong>Create an account to track your results, best times, and game history!</strong></p>
        @endguest
        
        <h3>Rules of the game:</h3>
        <ul style="text-align: left; max-width: 400px; margin: 0 auto;">
            <li>Each question has 4 possible answers</li>
            <li>The game continues until an error occurs or 20 correct answers are given.</li>
            <li>Questions are not repeated during a single game</li>
            <li>Questions are obtained from numbersapi.com</li>
        </ul>
    </div>
    
    <form method="POST" action="{{ route('trivia.start') }}">
        @csrf
        <button type="submit" class="btn btn-success">
            Start the game
        </button>
    </form>
@endsection
