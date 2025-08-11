<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GameSession;
use App\Services\TriviaService;

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
        $totalUsers = User::count();
        $adminUsers = User::where('is_admin', true)->count();
        $totalGames = GameSession::where('completed', true)->count();
        $recentGames = GameSession::with('user')
            ->where('completed', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'adminUsers', 'totalGames', 'recentGames'));
    }

    /**
     * Manage users
     */
    public function users()
    {
        $users = User::with(['gameSessions' => function($query) {
            $query->where('completed', true);
        }])->paginate(20);

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
    public function statistics()
    {
        $stats = [
            'total_games' => GameSession::where('completed', true)->count(),
            'average_score' => GameSession::where('completed', true)->avg('correct_answers'),
            'highest_score' => GameSession::where('completed', true)->max('correct_answers'),
            'average_duration' => GameSession::where('completed', true)->avg('duration_seconds'),
            'perfect_games' => GameSession::where('completed', true)->where('correct_answers', 20)->count(),
        ];

        $dailyGames = GameSession::where('completed', true)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.statistics', compact('stats', 'dailyGames'));
    }
}
