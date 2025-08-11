<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TriviaService
{
    private const API_URL = 'http://numbersapi.com';
      /**
     * Get a trivia question from the Numbers API
     */
    public function getTriviaQuestion(array $usedNumbers = []): ?array
    {
        $attempts = 0;
        $maxAttempts = 10;
        
        while ($attempts < $maxAttempts) {
            try {
                // Generate a random number that hasn't been used
                do {
                    $number = rand(1, 1000);
                } while (in_array($number, $usedNumbers) && count($usedNumbers) < 1000);
                
                $response = Http::timeout(10)->get(self::API_URL . "/{$number}");
                
                if ($response->successful()) {
                    $fact = $response->body();
                    
                    // Extract the number from the fact text
                    preg_match('/^(\d+)/', $fact, $matches);
                    $correctAnswer = $matches[1] ?? $number;
                    
                    return [
                        'question' => $this->formatQuestion($fact),
                        'correct_answer' => (int)$correctAnswer,
                        'options' => $this->generateOptions((int)$correctAnswer),
                        'full_fact' => $fact,
                        'used_number' => $number
                    ];
                }
                
                $attempts++;
                Log::warning('Failed to fetch trivia question, attempt ' . $attempts, ['status' => $response->status()]);
                
            } catch (\Exception $e) {
                $attempts++;
                Log::warning('Error fetching trivia question, attempt ' . $attempts, ['error' => $e->getMessage()]);
            }
        }
        
        Log::error('Failed to fetch trivia question after maximum attempts');
        return null;
    }
    
    /**
     * Format the fact into a question
     */
    private function formatQuestion(string $fact): string
    {
        // Remove the number from the beginning of the fact to create a question
        $question = preg_replace('/^\d+\s+/', '', $fact);
        
        // Make it a question about the number
        if (str_contains($question, 'is')) {
            $question = str_replace('is', 'is what number that is', $question);
        } else {
            $question = "What number " . $question . "?";
        }
        
        return ucfirst($question);
    }
    
    /**
     * Generate multiple choice options
     */
    private function generateOptions(int $correctAnswer): array
    {
        $options = [$correctAnswer];
        
        while (count($options) < 4) {
            // Generate random options around the correct answer
            $variance = rand(1, max(1, abs($correctAnswer)));
            $option = $correctAnswer + (rand(0, 1) ? $variance : -$variance);
            
            // Ensure positive numbers and no duplicates
            if ($option > 0 && !in_array($option, $options)) {
                $options[] = $option;
            }
        }
        
        // Shuffle the options
        shuffle($options);
        
        return $options;
    }
    
    /**
     * Get a random math trivia question (fallback)
     */
    public function getFallbackQuestion(): array
    {
        $questions = [
            [
                'question' => 'What is the atomic number of Carbon?',
                'correct_answer' => 6,
                'full_fact' => '6 is the atomic number of Carbon.'
            ],
            [
                'question' => 'How many sides does a hexagon have?',
                'correct_answer' => 6,
                'full_fact' => '6 is the number of sides a hexagon has.'
            ],
            [
                'question' => 'What is the square root of 64?',
                'correct_answer' => 8,
                'full_fact' => '8 is the square root of 64.'
            ],
            [
                'question' => 'How many planets are in our solar system?',
                'correct_answer' => 8,
                'full_fact' => '8 is the number of planets in our solar system.'
            ]
        ];
        
        $selected = $questions[array_rand($questions)];
        $selected['options'] = $this->generateOptions($selected['correct_answer']);
        
        return $selected;
    }
}
