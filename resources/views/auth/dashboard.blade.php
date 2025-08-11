<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Number Trivia Game</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .welcome {
            color: #333;
        }
        
        .nav-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-secondary {
            background: #e1e5e9;
            color: #333;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        
        .recent-games {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .recent-games h2 {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .games-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .games-table th,
        .games-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .games-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .games-table tr:hover {
            background: #f8f9fa;
        }
        
        .accuracy {
            font-weight: bold;
        }
        
        .accuracy.excellent { color: #28a745; }
        .accuracy.good { color: #ffc107; }
        .accuracy.poor { color: #dc3545; }
        
        .no-games {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 2rem;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .nav-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .games-table {
                font-size: 0.9rem;
            }
            
            .games-table th,
            .games-table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="welcome">
                <h1>Welcome back, {{ $user->name }}!</h1>
                <p>Here's your trivia game progress</p>
            </div>            <div class="nav-actions">
                <a href="{{ route('trivia.index') }}" class="btn btn-primary">Play Game</a>
                @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #ff6b6b; color: white;">Admin Panel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $user->total_games }}</div>
                <div class="stat-label">Games Played</div>
            </div>
              <div class="stat-card">
                <div class="stat-number">{{ $bestSession ? $bestSession->correct_answers . '/20' : '0/20' }}</div>
                <div class="stat-label">Best Score</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">{{ $fastestSession ? $fastestSession->duration : 'N/A' }}</div>
                <div class="stat-label">Fastest Time</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">{{ $user->average_accuracy }}%</div>
                <div class="stat-label">Average Accuracy</div>
            </div>
        </div>
        
        <div class="recent-games">
            <h2>Recent Games</h2>
            
            @if($recentGames->count() > 0)
                <table class="games-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Score</th>
                            <th>Duration</th>
                            <th>Accuracy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentGames as $game)
                            <tr>
                                <td>{{ $game->created_at->format('M j, Y g:i A') }}</td>
                                <td>{{ $game->correct_answers }}/{{ $game->total_questions }}</td>
                                <td>{{ $game->duration }}</td>
                                <td>
                                    <span class="accuracy {{ $game->accuracy >= 80 ? 'excellent' : ($game->accuracy >= 60 ? 'good' : 'poor') }}">
                                        {{ $game->accuracy }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-games">
                    <p>No games played yet. <a href="{{ route('trivia.index') }}" class="btn btn-primary">Start your first game!</a></p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
