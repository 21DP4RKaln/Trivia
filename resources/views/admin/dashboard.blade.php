@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="admin-card">
        <h1>Admin Dashboard</h1>
        <p>Welcome to the trivia game admin panel. Here you can manage users, view questions, and monitor game statistics.</p>
    </div>

    <div class="admin-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $adminUsers }}</div>
            <div class="stat-label">Admin Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalGames }}</div>
            <div class="stat-label">Games Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $recentGames->count() }}</div>
            <div class="stat-label">Recent Games</div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Recent Game Sessions</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Score</th>
                    <th>Duration</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentGames as $game)
                    <tr>
                        <td>{{ $game->user ? $game->user->name : 'Guest' }}</td>
                        <td>{{ $game->correct_answers }}/{{ $game->total_questions }}</td>
                        <td>{{ gmdate('i:s', $game->duration_seconds) }}</td>
                        <td>{{ $game->created_at->format('M j, Y H:i') }}</td>
                        <td>
                            @if($game->correct_answers == 20)
                                <span class="badge badge-success">Won</span>
                            @else
                                <span class="badge badge-secondary">Lost</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No games found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-grid">
        <div class="admin-card">
            <h3>Quick Actions</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('admin.users') }}" class="btn">Manage Users</a>
                <a href="{{ route('admin.statistics') }}" class="btn">View Statistics</a>
                <a href="{{ route('trivia.index') }}" class="btn btn-secondary">Play Game</a>
            </div>
        </div>
    </div>
@endsection
