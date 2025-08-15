<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Trivia Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @vite('resources/css/auth/user-dashboard.css')
    @vite('resources/css/pagination.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-bg">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="dashboard-card p-8 mb-8">
                <div class="header-content">
                    <div class="header-text">
                        <h1 class="page-title">Welcome back, {{ $user->name }}!</h1>
                        <p class="page-subtitle">Track your trivia mastery and compete with yourself</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('trivia.index') }}" class="btn btn-primary">Play Game</a>
                        @if($user->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-admin">Admin Panel</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-outline">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid mb-8">
                <div class="stat-card">
                    <div class="stat-number">{{ $user->total_games }}</div>
                    <div class="stat-label">Games Played</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">{{ $bestSession ? $bestSession->correct_answers . '/20' : '0/20' }}</div>
                    <div class="stat-label">Best Score</div>
                    @if($bestSession)
                        <div class="stat-detail">
                            {{ $bestSession->created_at->diffForHumans() }}
                        </div>
                    @endif
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">{{ $fastestSession ? $fastestSession->duration : 'N/A' }}</div>
                    <div class="stat-label">Fastest Time</div>
                    @if($fastestSession && $fastestSession->question_times)
                        <div class="stat-detail">
                            Avg per question: {{ number_format($fastestSession->average_question_time, 1) }}s
                        </div>
                    @endif
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">{{ $user->average_accuracy }}%</div>
                    <div class="stat-label">Average Accuracy</div>
                    @if($bestSession && $bestSession->question_times)
                        <div class="stat-detail">
                            Best timing split: {{ $bestSession->fastest_question_time }}s - {{ $bestSession->slowest_question_time }}s
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Timing Analysis Section -->
            @if($recentGames->where('question_times', '!=', null)->count() > 0)
            <div class="timing-analysis">
                <h2 class="table-title mb-8">Question Timing Analysis</h2>
                
                <!-- Timing Stats Overview -->
                <div class="timing-stats-grid mb-8">
                    @php
                        $gamesWithTiming = $recentGames->where('question_times', '!=', null);
                        $allQuestionTimes = $gamesWithTiming->flatMap(function($game) {
                            return collect($game->question_times ?? []);
                        });
                        $avgQuestionTime = $allQuestionTimes->avg('duration');
                        $fastestQuestion = $allQuestionTimes->min('duration');
                        $slowestQuestion = $allQuestionTimes->max('duration');
                    @endphp
                    
                    <div class="timing-stat-item">
                        <div class="timing-stat-number average">{{ number_format($avgQuestionTime ?? 0, 1) }}s</div>
                        <div class="timing-stat-label">Average Question Time</div>
                    </div>
                    <div class="timing-stat-item">
                        <div class="timing-stat-number fastest">{{ $fastestQuestion ?? 0 }}s</div>
                        <div class="timing-stat-label">Fastest Question</div>
                    </div>
                    <div class="timing-stat-item">
                        <div class="timing-stat-number slowest">{{ $slowestQuestion ?? 0 }}s</div>
                        <div class="timing-stat-label">Slowest Question</div>
                    </div>
                </div>
                
                <!-- Timing Distribution -->
                @if($bestSession && $bestSession->question_times)
                <div class="performance-breakdown">
                    <h3 class="breakdown-title">Your Best Performance Breakdown</h3>
                    @php $breakdown = $bestSession->question_timing_breakdown; @endphp
                    <div class="breakdown-grid">
                        <div class="breakdown-item">
                            <div class="breakdown-number fast">{{ $breakdown['fast'] }}</div>
                            <div class="breakdown-label">Fast (≤5s)</div>
                        </div>
                        <div class="breakdown-item">
                            <div class="breakdown-number medium">{{ $breakdown['medium'] }}</div>
                            <div class="breakdown-label">Medium (6-10s)</div>
                        </div>
                        <div class="breakdown-item">
                            <div class="breakdown-number slow">{{ $breakdown['slow'] }}</div>
                            <div class="breakdown-label">Slow (>10s)</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Recent Games -->
            <div class="table-container">
                <div class="table-header">
                    <h2 class="table-title">Recent Games</h2>
                </div>
                
                @if($recentGames->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table-wrapper">
                            <thead class="table-head">
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Duration</th>
                                    <th class="text-center">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentGames as $index => $game)
                                    <tr class="table-row">
                                        <td>
                                            <div class="date-cell">
                                                <div class="date-primary">{{ $game->created_at->format('M j, Y') }}</div>
                                                <div class="date-time" data-utc-time="{{ $game->created_at->toISOString() }}">{{ $game->created_at->format('g:i A') }}</div>
                                            </div>
                                        </td>
                                        <td class="font-bold text-center">
                                            {{ $game->correct_answers }}/{{ $game->total_questions }}
                                        </td>
                                        <td class="text-center">
                                            <span class="font-mono">{{ $game->duration }}</span>
                                        </td>
                                        <td>
                                            <div class="accuracy-badge-wrapper">
                                                @php
                                                    $accuracy = $game->accuracy;
                                                    $badgeClass = '';
                                                    
                                                    if ($accuracy >= 90) {
                                                        $badgeClass = 'accuracy-excellent';
                                                    } elseif ($accuracy >= 80) {
                                                        $badgeClass = 'accuracy-great';
                                                    } elseif ($accuracy >= 60) {
                                                        $badgeClass = 'accuracy-good';
                                                    } elseif ($accuracy >= 40) {
                                                        $badgeClass = 'accuracy-fair';
                                                    } else {
                                                        $badgeClass = 'accuracy-poor';
                                                    }
                                                @endphp
                                                
                                                <div class="accuracy-badge {{ $badgeClass }}">
                                                    <span class="accuracy-percentage">{{ $accuracy }}%</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($recentGames->hasPages())
                        <div class="pagination-wrapper">
                            {{ $recentGames->links('pagination.custom') }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <p class="empty-message">No games played yet. Time to start your journey!</p>
                        <a href="{{ route('trivia.index') }}" class="btn btn-primary empty-cta">Start Your First Game!</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Convert UTC times to user's local timezone
        document.addEventListener('DOMContentLoaded', function() {
            const timeElements = document.querySelectorAll('.date-time[data-utc-time]');
            
            timeElements.forEach(function(element) {
                const utcTime = element.getAttribute('data-utc-time');
                if (utcTime) {
                    const localDate = new Date(utcTime);
                    const localTimeString = localDate.toLocaleTimeString([], {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    element.textContent = localTimeString;
                }
            });
        });
    </script>
</body>
</html>
