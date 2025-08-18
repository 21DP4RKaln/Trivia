@extends('admin.layout')

@section('title', 'Statistics')

@section('content')
@vite('resources/css/admin/admin-statistics.css')

<div class="statistics-container">
    <!-- Page Header -->
    <div class="statistics-header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title">Statistics</h1>
                    <p class="page-subtitle">Comprehensive insights into game performance and user engagement</p>
                </div>
            </div>
            <div class="header-right">
                <div class="live-indicator">
                    <span class="live-dot"></span>
                    <span class="live-text">Live Data</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="metrics-grid">
        <div class="metric-card completion-metric">
            <div class="metric-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ number_format($stats['completion_rate'], 1) }}%</div>
                <div class="metric-label">Completion Rate</div>
                <div class="metric-description">{{ number_format($stats['total_games']) }} completed of {{ number_format($stats['total_started']) }} started</div>
            </div>
        </div>

        <div class="metric-card accuracy-metric">
            <div class="metric-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ number_format($stats['average_accuracy'], 1) }}%</div>
                <div class="metric-label">Average Accuracy</div>
                <div class="metric-description">Across all completed games</div>
            </div>
        </div>

        <div class="metric-card duration-metric">
            <div class="metric-icon">
                <i class="fas fa-stopwatch"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ gmdate('i:s', $stats['average_duration'] ?? 0) }}</div>
                <div class="metric-label">Average Duration</div>
                <div class="metric-description">Per completed game</div>
            </div>
        </div>

        <div class="metric-card perfect-metric">
            <div class="metric-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ number_format($stats['perfect_games']) }}</div>
                <div class="metric-label">Perfect Games</div>
                <div class="metric-description">20/20 correct answers</div>
            </div>
        </div>
    </div>

    <!-- Score Distribution Chart -->
    <div class="analytics-grid">
        <div class="analytics-card score-distribution-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Score Distribution
                </h3>
                <div class="card-subtitle">Performance breakdown across all games</div>
            </div>
            <div class="card-content">
                <div class="distribution-chart">
                    @foreach($scoreDistribution as $distribution)
                        @php
                            $percentage = $stats['total_games'] > 0 ? ($distribution->count / $stats['total_games']) * 100 : 0;
                            $colorClass = '';
                            $iconClass = '';
                            
                            if (str_contains($distribution->score_range, 'Excellent')) {
                                $colorClass = 'excellent';
                                $iconClass = 'fas fa-crown';
                            } elseif (str_contains($distribution->score_range, 'Good')) {
                                $colorClass = 'good';
                                $iconClass = 'fas fa-thumbs-up';
                            } elseif (str_contains($distribution->score_range, 'Average')) {
                                $colorClass = 'average';
                                $iconClass = 'fas fa-equals';
                            } elseif (str_contains($distribution->score_range, 'Below Average')) {
                                $colorClass = 'below-average';
                                $iconClass = 'fas fa-arrow-down';
                            } else {
                                $colorClass = 'poor';
                                $iconClass = 'fas fa-times';
                            }
                        @endphp
                        
                        <div class="distribution-item {{ $colorClass }}">
                            <div class="distribution-header">
                                <div class="distribution-icon">
                                    <i class="{{ $iconClass }}"></i>
                                </div>
                                <div class="distribution-info">
                                    <div class="distribution-label">{{ $distribution->score_range }}</div>
                                    <div class="distribution-stats">
                                        <span class="distribution-count">{{ number_format($distribution->count) }} games</span>
                                        <span class="distribution-percentage">{{ number_format($percentage, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="distribution-bar">
                                <div class="distribution-fill" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- User Performance Rankings -->
        <div class="analytics-card user-performance-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-medal"></i>
                    Top Performers
                </h3>
                <div class="card-subtitle">Most active and successful players</div>
            </div>
            <div class="card-content">
                <div class="performance-list">
                    @foreach($userStats->take(8) as $index => $user)
                        <div class="performance-item" style="animation-delay: {{ $index * 0.1 }}s">
                            <div class="performance-rank">
                                @if($index === 0)
                                    <i class="fas fa-crown rank-gold"></i>
                                @elseif($index === 1)
                                    <i class="fas fa-medal rank-silver"></i>
                                @elseif($index === 2)
                                    <i class="fas fa-award rank-bronze"></i>
                                @else
                                    <span class="rank-number">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <div class="performance-info">
                                <div class="performance-name">{{ $user->name }}</div>
                                <div class="performance-stats">
                                    <span class="stat-item">
                                        <i class="fas fa-gamepad"></i>
                                        {{ $user->games_played }} games
                                    </span>
                                    <span class="stat-item">
                                        <i class="fas fa-bullseye"></i>
                                        {{ number_format($user->avg_accuracy, 1) }}% avg
                                    </span>
                                    @if($user->perfect_games > 0)
                                        <span class="stat-item perfect">
                                            <i class="fas fa-star"></i>
                                            {{ $user->perfect_games }} perfect
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="performance-score">
                                <div class="score-value">{{ number_format($user->best_score) }}/20</div>
                                <div class="score-label">Best Score</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Guest Games Statistics -->
        @if($guestStats && $guestStats->total_games > 0)
            <div class="analytics-card guest-stats-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-secret"></i>
                        Guest Player Statistics
                    </h3>
                    <div class="card-subtitle">Performance metrics for anonymous players</div>
                </div>
                <div class="card-content">
                    <div class="guest-stats-grid">
                        <div class="guest-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-gamepad"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ number_format($guestStats->total_games) }}</div>
                                <div class="stat-label">Total Games</div>
                            </div>
                        </div>
                        
                        <div class="guest-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ number_format($guestStats->avg_score, 1) }}</div>
                                <div class="stat-label">Average Score</div>
                            </div>
                        </div>
                        
                        <div class="guest-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $guestStats->best_score }}/20</div>
                                <div class="stat-label">Best Score</div>
                            </div>
                        </div>
                        
                        <div class="guest-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ number_format($guestStats->avg_accuracy, 1) }}%</div>
                                <div class="stat-label">Average Accuracy</div>
                            </div>
                        </div>
                        
                        <div class="guest-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ gmdate('i:s', $guestStats->avg_duration ?? 0) }}</div>
                                <div class="stat-label">Average Duration</div>
                            </div>
                        </div>
                        
                        <div class="guest-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $guestStats->perfect_games }}</div>
                                <div class="stat-label">Perfect Games</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="guest-note">
                        <i class="fas fa-info-circle"></i>
                        <span>Guest games are tracked but not linked to user accounts. Encourage users to register to save their progress!</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Activity Feed -->
    <div class="analytics-card recent-activity-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-activity"></i>
                Recent Game Activity
            </h3>
            <div class="card-subtitle">Latest 10 completed games</div>
        </div>
        <div class="card-content">
            @if($recentGames->count() > 0)
                <div class="activity-feed">
                    @foreach($recentGames as $index => $game)
                        <div class="activity-item" style="animation-delay: {{ $index * 0.05 }}s">
                            <div class="activity-content">
                                <div class="activity-main">
                                    <div class="activity-header">
                                        <span class="activity-name">{{ $game->user->name ?? 'Guest Player' }}</span>
                                        <span class="activity-action">completed a game</span>
                                        <span class="activity-time">{{ $game->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-stats">
                                            <div class="stat-badge score-badge {{ $game->accuracy >= 80 ? 'high' : ($game->accuracy >= 60 ? 'medium' : 'low') }}">
                                                <i class="fas fa-trophy"></i>
                                                {{ $game->correct_answers }}/{{ $game->total_questions }}
                                            </div>
                                            <div class="stat-badge accuracy-badge">
                                                <i class="fas fa-bullseye"></i>
                                                {{ number_format($game->accuracy, 1) }}%
                                            </div>
                                            <div class="stat-badge duration-badge">
                                                <i class="fas fa-stopwatch"></i>
                                                {{ gmdate('i:s', $game->duration_seconds ?? 0) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>No Recent Activity</h3>
                    <p>Game activity will appear here as users complete games.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- All Games Table -->
    <div class="analytics-card games-table-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i>
                All Games
            </h3>
            <div class="card-subtitle">Complete game history with search and filters</div>
        </div>
        
        <!-- Search and Filter Controls -->
        <div class="table-controls">
            <form method="GET" action="{{ route('admin.statistics') }}" class="controls-form">
                <div class="control-group">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search by player name or email..." 
                            value="{{ request('search') }}"
                            class="search-input"
                        >
                    </div>
                </div>
                
                <div class="control-group">
                    <select name="score_filter" class="filter-select">
                        <option value="">All Scores</option>
                        <option value="perfect" {{ request('score_filter') === 'perfect' ? 'selected' : '' }}>Perfect (20/20)</option>
                        <option value="excellent" {{ request('score_filter') === 'excellent' ? 'selected' : '' }}>Excellent (18-20)</option>
                        <option value="good" {{ request('score_filter') === 'good' ? 'selected' : '' }}>Good (15-17)</option>
                        <option value="average" {{ request('score_filter') === 'average' ? 'selected' : '' }}>Average (12-14)</option>
                        <option value="below_average" {{ request('score_filter') === 'below_average' ? 'selected' : '' }}>Below Average (8-11)</option>
                        <option value="poor" {{ request('score_filter') === 'poor' ? 'selected' : '' }}>Poor (0-7)</option>
                    </select>
                </div>
                
                <div class="control-group">
                    <input 
                        type="date" 
                        name="date_from" 
                        placeholder="From Date"
                        value="{{ request('date_from') }}"
                        class="date-input"
                    >
                </div>
                
                <div class="control-group">
                    <input 
                        type="date" 
                        name="date_to" 
                        placeholder="To Date"
                        value="{{ request('date_to') }}"
                        class="date-input"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    Apply Filters
                </button>
                
                @if(request()->hasAny(['search', 'score_filter', 'date_from', 'date_to']))
                    <a href="{{ route('admin.statistics') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                @endif
            </form>
        </div>
        
        <div class="card-content">
            @if($allGames->count() > 0)
                <div class="table-wrapper">
                    <table class="games-table">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Score</th>
                                <th>Accuracy</th>
                                <th>Duration</th>
                                <th>Completed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allGames as $index => $game)
                                <tr class="game-row" style="animation-delay: {{ $index * 0.02 }}s">
                                    <td class="player-cell">
                                        <div class="player-info">
                                            <div class="player-details">
                                                <div class="player-name">{{ $game->user->name ?? 'Guest Player' }}</div>
                                                <div class="player-email">{{ $game->user->email ?? ($game->guest_identifier ? 'Guest ID: ' . substr($game->guest_identifier, -8) : 'Anonymous') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="score-cell">
                                        <div class="score-info">
                                            <div class="score-main">{{ $game->correct_answers }}/{{ $game->total_questions }}</div>
                                        </div>
                                    </td>
                                    <td class="accuracy-cell">
                                        <div class="accuracy-display {{ $game->accuracy >= 80 ? 'high' : ($game->accuracy >= 60 ? 'medium' : 'low') }}">
                                            {{ number_format($game->accuracy, 1) }}%
                                        </div>
                                    </td>
                                    <td class="duration-cell">
                                        <div class="duration-display">
                                            <i class="fas fa-stopwatch"></i>
                                            {{ gmdate('i:s', $game->duration_seconds ?? 0) }}
                                        </div>
                                    </td>
                                    <td class="date-cell">
                                        <div class="date-info">
                                            <div class="date-main">{{ $game->created_at->format('M j, Y') }}</div>
                                            <div class="date-time">{{ $game->created_at->format('g:i A') }}</div>
                                        </div>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="btn-icon view-details" onclick="viewGameDetails({{ $game->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($allGames->hasPages())
                    <div class="pagination-wrapper">
                        {{ $allGames->withQueryString()->links('pagination.custom') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <h3>No Games Found</h3>
                    @if(request()->hasAny(['search', 'score_filter', 'date_from', 'date_to']))
                        <p>No games match your current filters. Try adjusting your search criteria.</p>
                        <a href="{{ route('admin.statistics') }}" class="btn btn-primary">
                            <i class="fas fa-refresh"></i>
                            View All Games
                        </a>
                    @else
                        <p>No games have been completed yet. Games will appear here once users start playing.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Game Details Modal -->
<div id="gameDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Game Details</h3>
            <button class="modal-close" onclick="closeGameDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="gameDetailsContent">
            <!-- Game details will be loaded here via AJAX -->
            <div class="loading-state">
                <div class="spinner"></div>
                <p>Loading game details...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate metric cards
    const metricCards = document.querySelectorAll('.metric-card');
    metricCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate-in');
        }, index * 100);
    });
    
    // Animate distribution items
    const distributionItems = document.querySelectorAll('.distribution-item');
    distributionItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-in');
        }, 500 + (index * 150));
    });
    
    // Animate performance items
    const performanceItems = document.querySelectorAll('.performance-item');
    performanceItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-in');
        }, 800 + (index * 100));
    });
    
    // Animate activity items
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('animate-in');
        }, 1000 + (index * 50));
    });
    
    // Animate table rows
    const gameRows = document.querySelectorAll('.game-row');
    gameRows.forEach((row, index) => {
        setTimeout(() => {
            row.classList.add('animate-in');
        }, 1200 + (index * 20));
    });
});

function viewGameDetails(gameId) {
    const modal = document.getElementById('gameDetailsModal');
    const content = document.getElementById('gameDetailsContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="loading-state">
            <div class="spinner"></div>
            <p>Loading game details...</p>
        </div>
    `;
    
    modal.classList.add('show');
    
    // Fetch game details
    fetch(`/admin/game-details/${gameId}`)
        .then(response => response.json())
        .then(data => {
            content.innerHTML = `
                <div class="game-details">
                    <div class="details-grid">
                        <div class="detail-section">
                            <h4>Player Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">${data.user?.name || 'Guest Player'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">${data.user?.email || 'No email'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Total Games:</span>
                                <span class="detail-value">${data.user?.total_games || 0}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Average Accuracy:</span>
                                <span class="detail-value">${(data.user?.average_accuracy || 0).toFixed(1)}%</span>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Game Performance</h4>
                            <div class="detail-item">
                                <span class="detail-label">Score:</span>
                                <span class="detail-value">${data.correct_answers}/${data.total_questions}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Accuracy:</span>
                                <span class="detail-value">${data.accuracy.toFixed(1)}%</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Duration:</span>
                                <span class="detail-value">${formatDuration(data.duration_seconds || 0)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Completed:</span>
                                <span class="detail-value">${new Date(data.created_at).toLocaleString()}</span>
                            </div>
                        </div>
                    </div>
                    
                    ${data.question_times && data.question_times.length > 0 ? `
                        <div class="timing-analysis">
                            <h4>Question Timing Analysis</h4>
                            <div class="timing-stats">
                                <div class="timing-overview">
                                    <div class="timing-stat">
                                        <span class="timing-label">Average per question:</span>
                                        <span class="timing-value">${(data.question_times.reduce((sum, q) => sum + q.duration, 0) / data.question_times.length).toFixed(1)}s</span>
                                    </div>
                                    <div class="timing-stat">
                                        <span class="timing-label">Fastest question:</span>
                                        <span class="timing-value">${Math.min(...data.question_times.map(q => q.duration))}s</span>
                                    </div>
                                    <div class="timing-stat">
                                        <span class="timing-label">Slowest question:</span>
                                        <span class="timing-value">${Math.max(...data.question_times.map(q => q.duration))}s</span>
                                    </div>
                                </div>
                                <div class="timing-breakdown">
                                    ${data.question_times.map((time, index) => `
                                        <div class="timing-item ${time.duration <= 5 ? 'fast' : time.duration <= 10 ? 'medium' : 'slow'}">
                                            <span class="question-number">Q${index + 1}</span>
                                            <span class="question-time">${time.duration}s</span>
                                            <span class="question-result ${time.correct ? 'correct' : 'incorrect'}">
                                                <i class="fas fa-${time.correct ? 'check' : 'times'}"></i>
                                            </span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="error-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Failed to load game details. Please try again.</p>
                </div>
            `;
        });
}

function closeGameDetails() {
    const modal = document.getElementById('gameDetailsModal');
    modal.classList.remove('show');
}

function formatDuration(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

// Close modal when clicking outside
document.getElementById('gameDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGameDetails();
    }
});
</script>
@endpush
@endsection