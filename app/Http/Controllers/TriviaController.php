<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TriviaService;

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
        // Reset game session
        $request->session()->put('trivia_game', [
            'current_question' => 1,
            'correct_answers' => 0,
            'used_questions' => [],
            'game_active' => true,
            'last_question' => null,
            'last_correct_answer' => null
        ]);
        
        return $this->nextQuestion($request);
    }
    
    /**
     * Get the next question
     */
    public function nextQuestion(Request $request)
    {
        $gameState = $request->session()->get('trivia_game');
        
        if (!$gameState || !$gameState['game_active']) {
            return redirect()->route('trivia.index');
        }
        
        // Try to get question from API
        $questionData = $this->triviaService->getTriviaQuestion($gameState['used_questions']);
        
        // Use fallback if API fails
        if (!$questionData) {
            $questionData = $this->triviaService->getFallbackQuestion();
        } else {
            // Track used numbers to avoid repeats
            if (isset($questionData['used_number'])) {
                $gameState['used_questions'][] = $questionData['used_number'];
            }
        }
        
        // Store current question in session
        $gameState['current_question_data'] = $questionData;
        $request->session()->put('trivia_game', $gameState);
        
        return view('trivia.question', [
            'question' => $questionData['question'],
            'options' => $questionData['options'],
            'current_question' => $gameState['current_question'],
            'correct_answers' => $gameState['correct_answers']
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
        
        $gameState = $request->session()->get('trivia_game');
        
        if (!$gameState || !$gameState['game_active']) {
            return redirect()->route('trivia.index');
        }
        
        $questionData = $gameState['current_question_data'];
        $userAnswer = (int)$request->input('answer');
        $correctAnswer = $questionData['correct_answer'];
        $isCorrect = $userAnswer === $correctAnswer;
        
        // Store last question info for game over screen
        $gameState['last_question'] = $questionData;
        $gameState['last_correct_answer'] = $correctAnswer;
        
        if ($isCorrect) {
            $gameState['correct_answers']++;
            
            // Check if player has answered 20 questions correctly
            if ($gameState['correct_answers'] >= 20) {
                $gameState['game_active'] = false;
                $request->session()->put('trivia_game', $gameState);
                return $this->gameWon($request);
            }
            
            // Move to next question
            $gameState['current_question']++;
            $request->session()->put('trivia_game', $gameState);
            
            return view('trivia.correct', [
                'correct_answers' => $gameState['correct_answers'],
                'current_question' => $gameState['current_question']
            ]);
            
        } else {
            // Game over - wrong answer
            $gameState['game_active'] = false;
            $request->session()->put('trivia_game', $gameState);
            
            return $this->gameOver($request);
        }
    }
    
    /**
     * Show game over screen
     */
    private function gameOver(Request $request)
    {
        $gameState = $request->session()->get('trivia_game');
        
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
        $gameState = $request->session()->get('trivia_game');
        
        return view('trivia.game_won', [
            'correct_answers' => $gameState['correct_answers']
        ]);
    }
}
