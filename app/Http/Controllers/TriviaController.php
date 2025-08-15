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
    public function index()
    {
        return view('trivia.index');
    }
    
    /**
     * Start a new game
     */
    public function start(Request $request)
    {
        $startTime = Carbon::now();
        $gameSession = null;
        
        if (Auth::check()) {
            $gameSession = GameSession::create([
                'user_id' => Auth::id(),
                'start_time' => $startTime,
                'total_questions' => 0,
                'correct_answers' => 0,
                'completed' => false
            ]);
        }
        
        $request->session()->put('trivia', [
            'current_question' => 1,
            'correct_answers' => 0,
            'used_questions' => [],
            'game_active' => true,
            'last_question' => null,
            'last_correct_answer' => null,
            'start_time' => $startTime,
            'game_session_id' => $gameSession?->id
        ]);
        
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
        
        $questionData = $this->triviaService->getTriviaQuestion($gameState['used_questions']);
        
        if (!$questionData) {
            $questionData = $this->triviaService->getFallbackQuestionAvoidingRepeats($gameState['used_questions']);
        }
        
        if (isset($questionData['used_number'])) {
            $gameState['used_questions'][] = $questionData['used_number'];
        }
        
        $gameState['current_question_data'] = $questionData;
        $request->session()->put('trivia', $gameState);
        
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
            $request->session()->put('trivia', $gameState);
            
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
        if (!Auth::check() || !isset($gameState['game_session_id'])) {
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
            'completed' => true
        ]);
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

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Not authenticated']);
        }

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
}
