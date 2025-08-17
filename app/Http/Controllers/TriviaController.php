<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\TriviaService;
use App\Models\GameSession;
use App\Models\User;
use Carbon\Carbon;

class TriviaController extends Controller
{
    private TriviaService $triviaService;
    
    public function __construct(TriviaService $triviaService)
    {
        $this->triviaService = $triviaService;
    }
    
    /**
     * Show the game start page
     */
    public function index(Request $request)
    {
        // Check for saved game
        $savedGame = null;
        $gameToken = $request->cookie('trivia_game_token');
        
        if ($gameToken) {
            $savedGame = GameSession::findActiveGameByToken($gameToken);
            if (!$savedGame) {
                // Clear invalid cookie
                cookie()->queue(cookie()->forget('trivia_game_token'));
            }
        }
        
        return view('trivia.index', compact('savedGame'));
    }
    
    /**
     * Start a new game
     */
    public function start(Request $request)
    {
        $startTime = Carbon::now();
        $gameSession = null;
        $sessionToken = GameSession::generateSessionToken();
        
        // Create game session for both authenticated users and guests
        if (Auth::check()) {
            $gameSession = GameSession::create([
                'user_id' => Auth::id(),
                'session_token' => $sessionToken,
                'start_time' => $startTime,
                'total_questions' => 0,
                'correct_answers' => 0,
                'completed' => false,
                'expires_at' => now()->addHours(24)
            ]);
        } else {
            // Create a guest identifier using session ID or generate a unique one
            $guestIdentifier = $request->session()->getId() ?: 'guest_' . uniqid();
            
            $gameSession = GameSession::create([
                'user_id' => null,
                'guest_identifier' => $guestIdentifier,
                'session_token' => $sessionToken,
                'start_time' => $startTime,
                'total_questions' => 0,
                'correct_answers' => 0,
                'completed' => false,
                'expires_at' => now()->addHours(24)
            ]);
        }
        
        $gameState = [
            'current_question' => 1,
            'correct_answers' => 0,
            'used_questions' => [],
            'game_active' => true,
            'last_question' => null,
            'last_correct_answer' => null,
            'start_time' => $startTime,
            'game_session_id' => $gameSession->id
        ];
        
        $request->session()->put('trivia', $gameState);
        
        // Save game state to database for persistence
        $gameSession->saveGameState($gameState);
        
        // Set cookie with game token (expires in 24 hours)
        cookie()->queue('trivia_game_token', $sessionToken, 24 * 60);
        
        return $this->nextQuestion($request);
    }
    
    /**
     * Continue a saved game
     */
    public function continueGame(Request $request)
    {
        $gameToken = $request->cookie('trivia_game_token');
        
        if (!$gameToken) {
            return redirect()->route('trivia.index')->with('error', 'No saved game found.');
        }
        
        $savedGame = GameSession::findActiveGameByToken($gameToken);
        
        if (!$savedGame || !$savedGame->game_state) {
            cookie()->queue(cookie()->forget('trivia_game_token'));
            return redirect()->route('trivia.index')->with('error', 'Saved game is no longer available.');
        }
        
        // Restore game state with proper datetime conversion
        $gameState = $savedGame->getRestoredGameState();
        $request->session()->put('trivia', $gameState);
        
        return $this->nextQuestion($request);
    }

    /**
     * Get the next question
     */
    public function nextQuestion(Request $request)
    {
        $gameState = $request->session()->get('trivia');
        
        if (!$gameState || !$gameState['game_active']) {
            return redirect()->route('trivia.index');
        }
        
        if ($gameState['current_question'] == 1 && !isset($gameState['gameplay_start_time'])) {
            $gameState['gameplay_start_time'] = Carbon::now();
        }
        
        // Check if we already have a current question (from page reload or restored game)
        if (!isset($gameState['current_question_data'])) {
            $questionData = $this->triviaService->getTriviaQuestion($gameState['used_questions']);
            
            if (!$questionData) {
                $questionData = $this->triviaService->getFallbackQuestionAvoidingRepeats($gameState['used_questions']);
            }
            
            if (isset($questionData['used_number'])) {
                $gameState['used_questions'][] = $questionData['used_number'];
            }
            
            $gameState['current_question_data'] = $questionData;
            $request->session()->put('trivia', $gameState);
            
            // Save game state for persistence
            if (isset($gameState['game_session_id'])) {
                $gameSession = GameSession::find($gameState['game_session_id']);
                if ($gameSession && !$gameSession->completed) {
                    $gameSession->saveGameState($gameState);
                }
            }
        } else {
            // Use existing question data
            $questionData = $gameState['current_question_data'];
        }
        
        $isAdmin = false;
        if (Auth::check()) {
            $user = Auth::user();
            $isAdmin = $user && isset($user->is_admin) && $user->is_admin;
        }

        return view('trivia.question', [
            'question' => $questionData['question'],
            'options' => $questionData['options'],
            'current_question' => $gameState['current_question'],
            'correct_answers' => $gameState['correct_answers'],
            'correct_answer' => $questionData['correct_answer'], 
            'full_fact' => $questionData['full_fact'] ?? null, 
            'is_admin' => $isAdmin,
            'gameplay_start_time' => $gameState['gameplay_start_time'] ?? null
        ]);
    }
    
    /**
     * Process the submitted answer
     */
    public function submitAnswer(Request $request)
    {
        $request->validate([
            'answer' => 'required|integer'
        ]);
        
        $gameState = $request->session()->get('trivia');
        
        if (!$gameState || !$gameState['game_active']) {
            return redirect()->route('trivia.index');
        }
        
        $questionData = $gameState['current_question_data'];
        $userAnswer = (int)$request->input('answer');
        $correctAnswer = $questionData['correct_answer'];
        $isCorrect = $userAnswer === $correctAnswer;
        
        $gameState['last_question'] = $questionData;
        $gameState['last_correct_answer'] = $correctAnswer;
        
        if ($isCorrect) {
            $gameState['correct_answers']++;
            
            if ($gameState['correct_answers'] >= 20) {
                $gameState['game_active'] = false;
                $request->session()->put('trivia', $gameState);
                return $this->gameWon($request);
            }
            
            $gameState['current_question']++;
            // Clear current question data so a new question is generated
            unset($gameState['current_question_data']);
            $request->session()->put('trivia', $gameState);
            
            // Save game state for persistence
            if (isset($gameState['game_session_id'])) {
                $gameSession = GameSession::find($gameState['game_session_id']);
                if ($gameSession && !$gameSession->completed) {
                    $gameSession->saveGameState($gameState);
                }
            }
            
            return view('trivia.correct', [
                'correct_answers' => $gameState['correct_answers'],
                'current_question' => $gameState['current_question'],
                'gameplay_start_time' => $gameState['gameplay_start_time'] ?? null
            ]);
            
        } else {
            $gameState['game_active'] = false;
            $request->session()->put('trivia', $gameState);
            
            return $this->gameOver($request);
        }
    }
    
    /**
     * Show game over screen
     */
    private function gameOver(Request $request)
    {
        $gameState = $request->session()->get('trivia');
        
        $this->updateGameSession($gameState, false);
        
        return view('trivia.game_over', [
            'correct_answers' => $gameState['correct_answers'],
            'last_question' => $gameState['last_question'],
            'user_answer' => $request->input('answer'),
            'correct_answer' => $gameState['last_correct_answer']
        ]);
    }
    
    /**
     * Show game won screen
     */
    private function gameWon(Request $request)
    {
        $gameState = $request->session()->get('trivia');
        
        $this->updateGameSession($gameState, true);
        
        return view('trivia.game_won', [
            'correct_answers' => $gameState['correct_answers']
        ]);
    }
    
    /**
     * Update game session with final results
     */
    private function updateGameSession(array $gameState, bool $won)
    {
        // Update game session for both authenticated users and guests
        if (!isset($gameState['game_session_id'])) {
            return;
        }
        
        $gameSession = GameSession::find($gameState['game_session_id']);
        if (!$gameSession) {
            return;
        }
        
        $gameSession->update([
            'total_questions' => 20, 
            'correct_answers' => $gameState['correct_answers'],
            'duration_seconds' => 0, 
            'completed' => true,
            'game_state' => null, 
            'expires_at' => null  
        ]);
        
        // Clear the game token cookie since game is completed
        cookie()->queue(cookie()->forget('trivia_game_token'));
    }
    
    /**
     * Update game session duration from client-side timer
     */
    public function updateDuration(Request $request)
    {
        $request->validate([
            'duration' => 'required|integer|min:0',
            'question_times' => 'sometimes|array'
        ]);

        $gameState = $request->session()->get('trivia');
        if (!$gameState || !isset($gameState['game_session_id'])) {
            return response()->json(['success' => false, 'message' => 'No active game session']);
        }

        $gameSession = GameSession::find($gameState['game_session_id']);
        if (!$gameSession) {
            return response()->json(['success' => false, 'message' => 'Game session not found']);
        }

        $duration = $request->input('duration');
        $questionTimes = $request->input('question_times', []);
        
        // Log for debugging
        \Log::info('Updating game duration', [
            'game_session_id' => $gameSession->id,
            'is_guest' => $gameSession->isGuest(),
            'old_duration' => $gameSession->duration_seconds,
            'new_duration' => $duration,
            'question_times_count' => count($questionTimes)
        ]);

        // Update the duration with the client-side calculated value
        $updated = $gameSession->update([
            'duration_seconds' => $duration,
            'question_times' => $questionTimes,
            'end_time' => Carbon::now()
        ]);

        \Log::info('Duration update result', [
            'success' => $updated,
            'final_duration' => $gameSession->fresh()->duration_seconds
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Duration updated',
            'duration' => $duration,
            'formatted' => $gameSession->fresh()->duration
        ]);
    }

    /**
     * Abandon/Delete a saved game
     */
    public function abandonGame(Request $request)
    {
        $gameToken = $request->cookie('trivia_game_token');
        
        if ($gameToken) {
            $savedGame = GameSession::findActiveGameByToken($gameToken);
            if ($savedGame) {
                $savedGame->update([
                    'game_state' => null,
                    'expires_at' => null,
                    'completed' => true
                ]);
            }
        }
        
        // Clear the cookie
        cookie()->queue(cookie()->forget('trivia_game_token'));
        
        // Clear session
        $request->session()->forget('trivia');
        
        return redirect()->route('trivia.index')->with('success', 'Saved game has been abandoned. You can start a new game now.');
    }
}
