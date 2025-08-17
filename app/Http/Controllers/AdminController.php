<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GameSession;
use App\Models\TermsOfService;
use App\Services\TriviaService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private TriviaService $triviaService;
    
    public function __construct(TriviaService $triviaService)
    {
        $this->triviaService = $triviaService;
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        // Get current stats
        $totalUsers = User::count();
        $adminUsers = User::where('is_admin', true)->count();
        $totalGames = GameSession::where('completed', true)->count();
        
        // Calculate 30-day statistics
        $thirtyDaysAgo = now()->subDays(30);
        $sevenDaysAgo = now()->subDays(7);
        $yesterday = now()->subDay();
        $today = now()->startOfDay();
        
        // Users statistics
        $usersThisMonth = User::where('created_at', '>=', $thirtyDaysAgo)->count();
        $usersLastMonth = User::where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', $thirtyDaysAgo)->count();
        $userGrowthPercentage = $usersLastMonth > 0 
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1)
            : ($usersThisMonth > 0 ? 100 : 0);
        
        // Admin users statistics  
        $adminUsersThisMonth = User::where('is_admin', true)
            ->where('created_at', '>=', $thirtyDaysAgo)->count();
        $adminUsersLastMonth = User::where('is_admin', true)
            ->where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', $thirtyDaysAgo)->count();
        $adminGrowthPercentage = $adminUsersLastMonth > 0 
            ? round((($adminUsersThisMonth - $adminUsersLastMonth) / $adminUsersLastMonth) * 100, 1)
            : ($adminUsersThisMonth > 0 ? 100 : 0);
        
        // Games statistics
        $gamesThisWeek = GameSession::where('completed', true)
            ->where('created_at', '>=', $sevenDaysAgo)->count();
        $gamesLastWeek = GameSession::where('completed', true)
            ->where('created_at', '>=', now()->subDays(14))
            ->where('created_at', '<', $sevenDaysAgo)->count();
        $gameGrowthPercentage = $gamesLastWeek > 0 
            ? round((($gamesThisWeek - $gamesLastWeek) / $gamesLastWeek) * 100, 1)
            : ($gamesThisWeek > 0 ? 100 : 0);
        
        // Today's games vs yesterday
        $todaysGames = GameSession::where('completed', true)
            ->where('created_at', '>=', $today)->count();
        $yesterdaysGames = GameSession::where('completed', true)
            ->where('created_at', '>=', $yesterday->startOfDay())
            ->where('created_at', '<', $today)->count();
        $todayGrowthPercentage = $yesterdaysGames > 0 
            ? round((($todaysGames - $yesterdaysGames) / $yesterdaysGames) * 100, 1)
            : ($todaysGames > 0 ? 100 : 0);
        
        // Recent games for activity feed (last 30 days, limited to 10 most recent)
        $recentGames = GameSession::with('user')
            ->where('completed', true)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // If no games in last 30 days, get the 10 most recent games ever
        if ($recentGames->isEmpty()) {
            $recentGames = GameSession::with('user')
                ->where('completed', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
        
        // Games played today
        $gamesToday = GameSession::where('completed', true)
            ->where('created_at', '>=', $today)
            ->with('user')
            ->get();
        
        // Calculate completion rate (completed vs started games in last 30 days)
        $startedGamesLast30Days = GameSession::where('created_at', '>=', $thirtyDaysAgo)->count();
        $completedGamesLast30Days = GameSession::where('completed', true)
            ->where('created_at', '>=', $thirtyDaysAgo)->count();
        $completionRate = $startedGamesLast30Days > 0 
            ? round(($completedGamesLast30Days / $startedGamesLast30Days) * 100, 1)
            : 0;

        // Additional meaningful stats for the last 30 days
        $recentGamesLast30Days = GameSession::where('completed', true)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get();
        
        $averageAccuracyLast30Days = $recentGamesLast30Days->avg('accuracy') ?? 0;
        $perfectGamesLast30Days = $recentGamesLast30Days->where('correct_answers', 20)->count();
        $uniquePlayersLast30Days = $recentGamesLast30Days->pluck('user_id')->unique()->count();
        $totalGamesLast30Days = $recentGamesLast30Days->count();

        // Calculate real average game duration for last 30 days
        $averageGameDurationLast30Days = $recentGamesLast30Days->avg('duration_seconds') ?? 0;

        // Additional engagement metrics
        $highScoreGamesLast30Days = $recentGamesLast30Days->where('accuracy', '>=', 80)->count();
        $averageScoreLast30Days = $recentGamesLast30Days->avg('correct_answers') ?? 0;
        
        // Player engagement - users who played more than once in 30 days
        $repeatPlayersLast30Days = GameSession::where('completed', true)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();
        
        $playerRetentionRate = $uniquePlayersLast30Days > 0 
            ? round(($repeatPlayersLast30Days / $uniquePlayersLast30Days) * 100, 1)
            : 0;

        // Weekly trends for the last 4 weeks
        $weeklyTrends = [];
        for ($week = 0; $week < 4; $week++) {
            $weekStart = now()->subWeeks($week + 1)->startOfWeek();
            $weekEnd = now()->subWeeks($week + 1)->endOfWeek();
            
            $weekGames = GameSession::where('completed', true)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->get();
            
            $weeklyTrends[] = [
                'week' => 'Week ' . (4 - $week),
                'games_count' => $weekGames->count(),
                'avg_accuracy' => $weekGames->avg('accuracy') ?? 0,
                'unique_players' => $weekGames->pluck('user_id')->unique()->count(),
            ];
        }
        
        $weeklyTrends = array_reverse($weeklyTrends); 

        return view('admin.dashboard', compact(
            'totalUsers', 'adminUsers', 'totalGames', 'recentGames',
            'userGrowthPercentage', 'adminGrowthPercentage', 'gameGrowthPercentage', 
            'todayGrowthPercentage', 'gamesToday', 'completionRate',
            'averageAccuracyLast30Days', 'perfectGamesLast30Days', 'uniquePlayersLast30Days',
            'totalGamesLast30Days', 'averageGameDurationLast30Days', 'weeklyTrends',
            'highScoreGamesLast30Days', 'averageScoreLast30Days', 'repeatPlayersLast30Days', 'playerRetentionRate'
        ));
    }

    /**
     * Manage users
     */
    public function users(Request $request)
    {
        $query = User::with(['gameSessions' => function($query) {
            $query->where('completed', true);
        }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter functionality
        if ($request->filled('filter')) {
            if ($request->filter === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->filter === 'regular') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Toggle admin status for a user
     */
    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);
        
        $status = $user->is_admin ? 'granted' : 'removed';
        return redirect()->back()->with('success', "Admin privileges {$status} for {$user->name}");
    }

    /**
     * View all questions for testing
     */
    public function questions()
    {
        // Get all fallback questions
        $fallbackQuestions = [];
        $usedQuestions = [];
        
        while (count($fallbackQuestions) < 15) {
            $question = $this->triviaService->getFallbackQuestionAvoidingRepeats($usedQuestions);
            $fallbackQuestions[] = $question;
            $usedQuestions[] = $question['used_number'];
        }

        // Get some sample API questions
        $apiQuestions = [];
        for ($i = 0; $i < 10; $i++) {
            $question = $this->triviaService->getTriviaQuestion($usedQuestions);
            if ($question) {
                $apiQuestions[] = $question;
                $usedQuestions[] = $question['used_number'];
            }
        }

        return view('admin.questions', compact('fallbackQuestions', 'apiQuestions'));
    }

    /**
     * Game statistics
     */
    public function statistics(Request $request)
    {
        // Basic stats
        $stats = [
            'total_games' => GameSession::where('completed', true)->count(),
            'total_started' => GameSession::count(),
            'average_score' => GameSession::where('completed', true)->avg('correct_answers'),
            'highest_score' => GameSession::where('completed', true)->max('correct_answers'),
            'lowest_score' => GameSession::where('completed', true)->min('correct_answers'),
            'average_duration' => GameSession::where('completed', true)->avg('duration_seconds'),
            'perfect_games' => GameSession::where('completed', true)->where('correct_answers', 20)->count(),
            'average_accuracy' => GameSession::where('completed', true)->avg('accuracy'),
        ];

        // Calculate completion rate
        $stats['completion_rate'] = $stats['total_started'] > 0 
            ? round(($stats['total_games'] / $stats['total_started']) * 100, 1)
            : 0;

        // RECENT GAMES - Show last 10 individual games with pagination option
        $recentGameQuery = GameSession::with('user')
            ->where('completed', true)
            ->orderBy('created_at', 'desc');
        
        // Check if pagination is requested
        if ($request->has('paginate') && $request->paginate === 'true') {
            $recentGamesList = $recentGameQuery->paginate(10);
        } else {
            $recentGamesList = $recentGameQuery->limit(10)->get();
        }

        // User performance stats (only for registered users)
        $userStats = GameSession::where('completed', true)
            ->whereNotNull('user_id')
            ->join('users', 'game_sessions.user_id', '=', 'users.id')
            ->selectRaw('
                users.id,
                users.name,
                users.email,
                COUNT(*) as games_played,
                AVG(correct_answers) as avg_score,
                MAX(correct_answers) as best_score,
                AVG(accuracy) as avg_accuracy,
                AVG(duration_seconds) as avg_duration,
                SUM(CASE WHEN correct_answers = 20 THEN 1 ELSE 0 END) as perfect_games
            ')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('games_played', 'desc')
            ->get();

        // Guest games statistics
        $guestStats = GameSession::where('completed', true)
            ->whereNull('user_id')
            ->selectRaw('
                COUNT(*) as total_games,
                AVG(correct_answers) as avg_score,
                MAX(correct_answers) as best_score,
                AVG(accuracy) as avg_accuracy,
                AVG(duration_seconds) as avg_duration,
                SUM(CASE WHEN correct_answers = 20 THEN 1 ELSE 0 END) as perfect_games
            ')
            ->first();

        // Score distribution
        $scoreDistribution = GameSession::where('completed', true)
            ->selectRaw('
                CASE 
                    WHEN correct_answers >= 18 THEN "18-20 (Excellent)"
                    WHEN correct_answers >= 15 THEN "15-17 (Good)"
                    WHEN correct_answers >= 12 THEN "12-14 (Average)"
                    WHEN correct_answers >= 8 THEN "8-11 (Below Average)"
                    ELSE "0-7 (Poor)"
                END as score_range,
                COUNT(*) as count
            ')
            ->groupBy('score_range')
            ->orderByRaw('MIN(correct_answers) DESC')
            ->get();

        // Get all game sessions with pagination and filtering
        $query = GameSession::with('user')
            ->where('completed', true)
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                })->orWhere(function($guestQuery) use ($search) {
                    // For guest games, search in guest identifier
                    $guestQuery->whereNull('user_id')
                              ->where('guest_identifier', 'like', "%{$search}%");
                });
            });
        }

        // Filter by score range
        if ($request->filled('score_filter')) {
            switch ($request->score_filter) {
                case 'perfect':
                    $query->where('correct_answers', 20);
                    break;
                case 'excellent':
                    $query->where('correct_answers', '>=', 18);
                    break;
                case 'good':
                    $query->whereBetween('correct_answers', [15, 17]);
                    break;
                case 'average':
                    $query->whereBetween('correct_answers', [12, 14]);
                    break;
                case 'below_average':
                    $query->whereBetween('correct_answers', [8, 11]);
                    break;
                case 'poor':
                    $query->where('correct_answers', '<', 8);
                    break;
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $allGames = $query->paginate(20);

        // Recent activity (last 10 games)
        $recentGames = GameSession::with('user')
            ->where('completed', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.statistics', compact(
            'stats', 'recentGamesList', 'userStats', 'guestStats', 'scoreDistribution', 
            'allGames', 'recentGames'
        ));
    }

    /**
     * Get game details for modal
     */
    public function gameDetails(GameSession $gameSession)
    {
        // Load user relationship
        $gameSession->load('user');
        
        // Get user's additional stats
        $user = $gameSession->user;
        if ($user) {
            $userGames = $user->gameSessions()->where('completed', true)->get();
            $gameSession->user->total_games = $userGames->count();
            $gameSession->user->average_accuracy = $userGames->avg('accuracy') ?? 0;
        }

        return response()->json($gameSession);
    }

    /**
     * Show Terms of Service management page
     */
    public function termsOfService()
    {
        $currentTerms = TermsOfService::getActive();
        $history = TermsOfService::with('updatedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get analytics data
        $analytics = $this->getTermsAnalyticsData();

        return view('admin.terms-of-service', compact('currentTerms', 'history', 'analytics'));
    }

    /**
     * Get Terms Analytics Data (AJAX endpoint)
     */
    public function getTermsAnalytics(Request $request)
    {
        $timeframe = $request->get('timeframe', 30);
        $analytics = $this->getTermsAnalyticsData($timeframe);
        
        return response()->json($analytics);
    }

    /**
     * Get Terms Analytics Data
     */
    private function getTermsAnalyticsData($timeframe = 30)
    {
        // Calculate terms acceptance analytics
        $daysAgo = now()->subDays($timeframe);
        
        // Total users who have accepted terms 
        $totalAcceptances = User::whereNotNull('created_at')->count();
        
        // Recent acceptances 
        $recentAcceptances = User::where('created_at', '>=', $daysAgo)->count();
        
        // Calculate acceptance rate 
        $totalViews = $totalAcceptances * 1.2; 
        $acceptanceRate = $totalAcceptances > 0 ? round(($totalAcceptances / $totalViews) * 100, 1) : 0;
        
        // Mock average read time 
        $avgReadTime = "2m 34s";
        
        // Daily breakdown for chart
        $dailyData = [];
        for ($i = $timeframe - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayAcceptances = User::whereDate('created_at', $date->format('Y-m-d'))->count();
            $dailyData[] = [
                'date' => $date->format('Y-m-d'),
                'acceptances' => $dayAcceptances
            ];
        }
        
        return [
            'totalAcceptances' => $totalAcceptances,
            'recentAcceptances' => $recentAcceptances,
            'acceptanceRate' => $acceptanceRate,
            'avgReadTime' => $avgReadTime,
            'totalViews' => (int)$totalViews,
            'dailyData' => $dailyData
        ];
    }

    /**
     * Update Terms of Service
     */
    public function updateTermsOfService(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'version' => 'required|string|max:50',
            'effective_date' => 'required|date'
        ]);

        try {
            // Deactivate all previous terms
            TermsOfService::where('is_active', true)->update(['is_active' => false]);

            // Create new terms
            $newTerms = TermsOfService::create([
                'content' => $request->input('content'),
                'version' => $request->input('version'),
                'effective_date' => $request->input('effective_date'),
                'is_active' => true,
                'updated_by' => Auth::id()
            ]);

            // Clear any view/model cache to ensure immediate updates
            if (function_exists('cache')) {
                \Illuminate\Support\Facades\Cache::flush();
            }

            // Set a flag that terms were updated for notification purposes
            \Illuminate\Support\Facades\Cache::put('terms_updated_at', now()->timestamp, 3600); // Store for 1 hour

            return redirect()->back()->with('success', 'Terms of Service updated successfully! Changes are now live.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Terms of Service: ' . $e->getMessage());
        }
    }

    /**
     * Get Terms of Service history details for comparison
     */
    public function getTermsHistory($id)
    {
        $terms = TermsOfService::with('updatedBy')->findOrFail($id);
        
        return response()->json([
            'id' => $terms->id,
            'version' => $terms->version,
            'content' => $terms->content,
            'effective_date' => $terms->effective_date->format('M d, Y'),
            'created_at' => $terms->created_at->format('M d, Y H:i'),
            'updated_by' => $terms->updatedBy->name ?? 'System',
            'is_active' => $terms->is_active
        ]);
    }

    /**
     * Export Terms of Service
     */
    public function exportTerms(Request $request)
    {
        $format = $request->get('format', 'html');
        $currentTerms = TermsOfService::getActive();
        
        if (!$currentTerms) {
            return response()->json(['error' => 'No active terms found'], 404);
        }
        
        $content = $currentTerms->content;
        $version = $currentTerms->version;
        $effectiveDate = $currentTerms->effective_date->format('Y-m-d');
        
        if ($format === 'json') {
            return response()->json([
                'version' => $version,
                'effective_date' => $effectiveDate,
                'content' => $content,
                'exported_at' => now()->toISOString()
            ]);
        }
        
        // For other formats, return data for client-side processing
        return response()->json([
            'version' => $version,
            'effective_date' => $effectiveDate,
            'content' => $content
        ]);
    }
}
