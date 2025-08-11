@extends('admin.layout')

@section('title', 'Game Statistics')

@section('content')
    <div class="admin-card">
        <h1>Game Statistics</h1>
        <p>Comprehensive analytics about game performance and user engagement.</p>
    </div>

    <div class="admin-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_games'] }}</div>
            <div class="stat-label">Total Games</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['average_score'], 1) }}</div>
            <div class="stat-label">Average Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['highest_score'] }}</div>
            <div class="stat-label">Highest Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['perfect_games'] }}</div>
            <div class="stat-label">Perfect Games</div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Performance Metrics</h2>
        <div class="admin-grid">
            <div>
                <h3>Average Game Duration</h3>
                <p style="font-size: 1.5rem; color: #667eea; font-weight: bold;">
                    {{ gmdate('i:s', $stats['average_duration']) }}
                </p>
                <p style="color: #666;">Minutes:Seconds</p>
            </div>
            <div>
                <h3>Success Rate</h3>
                <p style="font-size: 1.5rem; color: #667eea; font-weight: bold;">
                    {{ $stats['total_games'] > 0 ? number_format(($stats['perfect_games'] / $stats['total_games']) * 100, 1) : 0 }}%
                </p>
                <p style="color: #666;">Players who reach 20 correct answers</p>
            </div>
        </div>
    </div>

    @if($dailyGames->count() > 0)
        <div class="admin-card">
            <h2>Daily Game Activity (Last 30 Days)</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Games Played</th>
                        <th>Activity Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyGames as $day)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($day->date)->format('M j, Y') }}</td>
                            <td>{{ $day->count }}</td>
                            <td>
                                <div style="background: #e9ecef; border-radius: 4px; height: 8px; width: 100px; position: relative;">
                                    <div style="background: #667eea; height: 100%; border-radius: 4px; width: {{ min(100, ($day->count / $dailyGames->max('count')) * 100) }}%;"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endsection
