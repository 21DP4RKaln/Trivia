@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="dashboard-header">
    <div class="header-content">
        <div class="header-text">
            <h1 class="page-title">
                <i class="fas fa-chart-line"></i>
                Admin Dashboard
            </h1>
            <div class="live-indicator">
                <span class="live-dot"></span>
                <span class="live-text">Live Data</span>
            </div>
        </div>
        <div class="header-actions">
            <div class="quick-action-btn">
                <a href="{{ route('admin.users') }}" class="btn btn-outline">
                    <i class="fas fa-users"></i>
                    Manage Users
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Overview -->
<div class="stats-grid">
    <div class="stat-card primary-stat" data-animation-delay="0">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ number_format($totalUsers) }}</div>
            <div class="stat-label">Total Users</div>
            <div class="stat-change {{ $userGrowthPercentage >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $userGrowthPercentage >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ $userGrowthPercentage >= 0 ? '+' : '' }}{{ $userGrowthPercentage }}% this month</span>
            </div>
        </div>
        <div class="stat-graph">
            <div class="mini-chart users-chart"></div>
        </div>
    </div>

    <div class="stat-card info-stat" data-animation-delay="200">
        <div class="stat-icon">
            <i class="fas fa-gamepad"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ number_format($totalGames) }}</div>
            <div class="stat-label">Games Played</div>
            <div class="stat-change {{ $gameGrowthPercentage >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $gameGrowthPercentage >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ $gameGrowthPercentage >= 0 ? '+' : '' }}{{ $gameGrowthPercentage }}% this week</span>
            </div>
        </div>
        <div class="stat-graph">
            <div class="mini-chart games-chart"></div>
        </div>
    </div>

    <div class="stat-card warning-stat" data-animation-delay="300">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $gamesToday->count() }}</div>
            <div class="stat-label">Today's Games</div>
            <div class="stat-change {{ $todayGrowthPercentage >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $todayGrowthPercentage >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ $todayGrowthPercentage >= 0 ? '+' : '' }}{{ $todayGrowthPercentage }}% vs yesterday</span>
            </div>
        </div>
        <div class="stat-graph">
            <div class="mini-chart today-chart"></div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="dashboard-grid">
    <!-- Recent Games Activity -->
    <div class="dashboard-card recent-games-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-activity"></i>
                Recent Game Activity (Last 30 Days)
            </h3>
            <div class="card-actions">
                <a href="{{ route('admin.statistics') }}" class="btn btn-ghost btn-sm">
                    <i class="fas fa-chart-bar"></i>
                    View All Stats
                </a>
            </div>
        </div>
        <div class="card-content">
            @if($recentGames->count() > 0)
                <div class="activity-summary">
                    <div class="summary-stats">
                        <div class="summary-item">
                            <span class="summary-label">Average Score:</span>
                            <span class="summary-value">{{ number_format($recentGames->avg('correct_answers'), 1) }}/20</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Average Accuracy:</span>
                            <span class="summary-value">{{ number_format($recentGames->avg('accuracy'), 1) }}%</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Average Duration:</span>
                            <span class="summary-value">{{ gmdate('i:s', $recentGames->avg('duration_seconds') ?? 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="games-list">
                    @foreach($recentGames as $game)
                        <div class="game-item" data-animation-delay="{{ $loop->index * 50 }}">
                            <div class="game-player">
                                <div class="player-info">
                                    <div class="player-name">{{ $game->user->name ?? 'Guest Player' }}</div>
                                    <div class="game-time">{{ $game->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="game-stats">
                                <div class="score-badge {{ $game->accuracy >= 80 ? 'high-score' : ($game->accuracy >= 60 ? 'medium-score' : 'low-score') }}">
                                    {{ $game->correct_answers }}/{{ $game->total_questions }}
                                </div>
                                <div class="accuracy">{{ number_format($game->accuracy, 1) }}%</div>
                            </div>
                            <div class="game-duration">
                                <i class="fas fa-stopwatch"></i>
                                {{ gmdate('i:s', $game->duration_seconds ?? 0) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state-small">
                    <i class="fas fa-gamepad"></i>
                    <p>No games played in the last 30 days</p>
                    <small>Games will appear here once users start playing</small>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-card quick-actions-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h3>
        </div>
        <div class="card-content">
            <div class="actions-grid">
                <a href="{{ route('admin.users') }}" class="action-item">
                    <div class="action-icon users-action">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-text">
                        <div class="action-title">Manage Users</div>
                        <div class="action-description">View and manage user accounts</div>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('admin.questions') }}" class="action-item">
                    <div class="action-icon questions-action">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="action-text">
                        <div class="action-title">Question Bank</div>
                        <div class="action-description">Review trivia questions</div>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('admin.statistics') }}" class="action-item">
                    <div class="action-icon stats-action">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-text">
                        <div class="action-title">Analytics</div>
                        <div class="action-description">View detailed statistics</div>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('trivia.index') }}" class="action-item">
                    <div class="action-icon play-action">
                        <i class="fas fa-play"></i>
                    </div>
                    <div class="action-text">
                        <div class="action-title">Play Game</div>
                        <div class="action-description">Test the trivia experience</div>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="dashboard-card system-status-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-server"></i>
                System Status
            </h3>
        </div>
        <div class="card-content">
            <div class="status-grid">
                <div class="status-item">
                    <div class="status-indicator online"></div>
                    <div class="status-text">
                        <div class="status-title">Trivia API</div>
                        <div class="status-description">Operational</div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-indicator online"></div>
                    <div class="status-text">
                        <div class="status-title">Database</div>
                        <div class="status-description">Connected</div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-indicator online"></div>
                    <div class="status-text">
                        <div class="status-title">Cache</div>
                        <div class="status-description">Active</div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-indicator online"></div>
                    <div class="status-text">
                        <div class="status-title">Sessions</div>
                        <div class="status-description">Healthy</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="dashboard-card performance-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-tachometer-alt"></i>
                Performance Overview (Last 30 Days)
            </h3>
        </div>
        <div class="card-content">
            <div class="performance-metrics">
                <div class="metric-item">
                    <div class="metric-label">Average Game Duration</div>
                    <div class="metric-value">
                        {{ gmdate('i:s', $averageGameDurationLast30Days) }}
                    </div>
                    <div class="metric-bar">
                        @php
                            // Calculate duration percentage based on ideal time (5-10 minutes is good)
                            $optimalDuration = 600; // 10 minutes in seconds
                            $durationPercentage = $averageGameDurationLast30Days > 0 
                                ? min(100, ($averageGameDurationLast30Days / $optimalDuration) * 100) 
                                : 0;
                        @endphp
                        <div class="metric-fill" style="width: {{ $durationPercentage }}%"></div>
                    </div>
                </div>

                <div class="metric-item">
                    <div class="metric-label">Average Accuracy (30 Days)</div>
                    <div class="metric-value">
                        {{ number_format($averageAccuracyLast30Days, 1) }}%
                    </div>
                    <div class="metric-bar">
                        <div class="metric-fill" style="width: {{ $averageAccuracyLast30Days }}%"></div>
                    </div>
                </div>

                <div class="metric-item">
                    <div class="metric-label">Completion Rate (30 Days)</div>
                    <div class="metric-value">{{ number_format($completionRate, 1) }}%</div>
                    <div class="metric-bar">
                        <div class="metric-fill" style="width: {{ $completionRate }}%"></div>
                    </div>
                </div>

                <div class="metric-item">
                    <div class="metric-label">Perfect Games (30 Days)</div>
                    <div class="metric-value">{{ $perfectGamesLast30Days }} games</div>
                    <div class="metric-bar">
                        @php
                            $perfectGamesPercentage = $totalGamesLast30Days > 0 
                                ? ($perfectGamesLast30Days / $totalGamesLast30Days) * 100 
                                : 0;
                        @endphp
                        <div class="metric-fill" style="width: {{ $perfectGamesPercentage }}%"></div>
                    </div>
                </div>

                <div class="metric-item">
                    <div class="metric-label">High Score Games (≥80%)</div>
                    <div class="metric-value">{{ $highScoreGamesLast30Days }} games</div>
                    <div class="metric-bar">
                        @php
                            $highScorePercentage = $totalGamesLast30Days > 0 
                                ? ($highScoreGamesLast30Days / $totalGamesLast30Days) * 100 
                                : 0;
                        @endphp
                        <div class="metric-fill" style="width: {{ $highScorePercentage }}%"></div>
                    </div>
                </div>

                <div class="metric-item">
                    <div class="metric-label">Player Retention Rate</div>
                    <div class="metric-value">{{ number_format($playerRetentionRate, 1) }}%</div>
                    <div class="metric-bar">
                        <div class="metric-fill" style="width: {{ $playerRetentionRate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Trends -->
    <div class="dashboard-card weekly-trends-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i>
                Weekly Trends (Last 4 Weeks)
            </h3>
        </div>
        <div class="card-content">
            <div class="trends-grid">
                @foreach($weeklyTrends as $trend)
                    <div class="trend-item">
                        <div class="trend-header">
                            <h4 class="trend-title">{{ $trend['week'] }}</h4>
                        </div>
                        <div class="trend-stats">
                            <div class="trend-stat">
                                <div class="trend-label">Games</div>
                                <div class="trend-value">{{ $trend['games_count'] }}</div>
                            </div>
                            <div class="trend-stat">
                                <div class="trend-label">Avg Accuracy</div>
                                <div class="trend-value">{{ number_format($trend['avg_accuracy'], 1) }}%</div>
                            </div>
                            <div class="trend-stat">
                                <div class="trend-label">Players</div>
                                <div class="trend-value">{{ $trend['unique_players'] }}</div>
                            </div>
                        </div>
                        <div class="trend-bar">
                            @php
                                $maxGames = collect($weeklyTrends)->max('games_count');
                                $barWidth = $maxGames > 0 ? ($trend['games_count'] / $maxGames) * 100 : 0;
                            @endphp
                            <div class="trend-fill" style="width: {{ $barWidth }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats cards on load
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate-in');
        }, index * 100);
    });

    // Animate dashboard cards
    const dashboardCards = document.querySelectorAll('.dashboard-card');
    dashboardCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate-in');
        }, 400 + (index * 150));
    });

    // Animate game items
    const gameItems = document.querySelectorAll('.game-item');
    gameItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-in');
        }, 800 + (index * 50));
    });

    // Initialize mini charts and dashboard functionality
    if (window.initializeAdminDashboard) {
        window.initializeAdminDashboard();
    }
});
</script>
@endpush
@endsection