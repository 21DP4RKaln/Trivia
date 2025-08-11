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
                do {
                    $number = rand(1, 1000);
                } while (in_array($number, $usedNumbers) && count($usedNumbers) < 1000);
                
                if (count($usedNumbers) >= 1000) {
                    $number = rand(1, 1000);
                }
                
                $response = Http::timeout(10)->get(self::API_URL . "/{$number}");
                
                if ($response->successful()) {
                    $fact = $response->body();
                    
                    if ($this->isValidFact($fact)) {
                        preg_match('/^(\d+)/', $fact, $matches);
                        $correctAnswer = $matches[1] ?? $number;
                        
                        Log::info('Valid fact received from API', ['number' => $number, 'fact' => $fact]);
                        
                        return [
                            'question' => $this->formatQuestion($fact),
                            'correct_answer' => (int)$correctAnswer,
                            'options' => $this->generateOptions((int)$correctAnswer),
                            'full_fact' => $fact,
                            'used_number' => $number
                        ];
                    } else {
                        $attempts++;
                        Log::warning('Invalid fact received from API', ['fact' => $fact, 'number' => $number]);
                        continue;
                    }
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
     * Check if the API response is a valid fact or an error message
     */
    private function isValidFact(string $fact): bool
    {
        // Check for common error messages or invalid responses
        $invalidPatterns = [
            'missing a fact',
            'submit one to',
            'google mail',
            'numbersapi',
            'error',
            'not found',
            'invalid',
            'unavailable',
            'unremarkable number',
            'an unremarkable number',
            'is unremarkable',
            'uninteresting number',
            'an uninteresting number', 
            'is uninteresting',
            'nothing special',
            'nothing interesting',
            'no special properties',
            'ordinary number',
            'boring number',
            'dull number',
            'no particular significance',
            'has no interesting properties',
            'not particularly interesting',
            'is not notable',
            'not significant',
            'lacks interesting properties'
        ];
        
        $factLower = strtolower($fact);
        
        foreach ($invalidPatterns as $pattern) {
            if (str_contains($factLower, $pattern)) {
                return false;
            }
        }
        
        // Must start with a number followed by space and text
        if (!preg_match('/^\d+\s+.{10,}/', $fact)) {
            return false;
        }
        
        // Should not be too short 
        if (strlen($fact) < 15) {
            return false;
        }
        
        // Additional check: if the fact only says it's unremarkable or has no properties
        if (preg_match('/^\d+\s+is\s+(an?\s+)?(unremarkable|uninteresting|ordinary|boring|dull|not\s+particularly\s+interesting|not\s+notable|not\s+significant)\s+(number)?\.?$/i', $fact)) {
            return false;
        }
        
        // Check for facts that just say the number has no interesting properties
        if (preg_match('/^\d+\s+(has\s+no|lacks)\s+(interesting|special|notable)\s+properties/i', $fact)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Format the fact into a question
     */
    private function formatQuestion(string $fact): string
    {
        preg_match('/^(\d+)\s+(.+)/', $fact, $matches);
        
        if (count($matches) < 3) {
            return "What number is related to this fact: " . $fact . "?";
        }
        
        $number = $matches[1];
        $factText = trim($matches[2]);
        
        $factText = rtrim($factText, '.');
        
        // Create better question formats based on common patterns
        if (str_contains($factText, 'is the number of')) {
            $factText = preg_replace('/^is the number of/', 'How many', $factText);
            return ucfirst($factText) . "?";
        } elseif (str_contains($factText, 'is the atomic number of')) {
            $factText = preg_replace('/^is the atomic number of/', 'What is the atomic number of', $factText);
            return ucfirst($factText) . "?";
        } elseif (str_contains($factText, 'is the year')) {
            $factText = preg_replace('/^is the year/', 'In what year', $factText);
            return ucfirst($factText) . "?";
        } elseif (str_contains($factText, 'was the year')) {
            $factText = preg_replace('/^was the year that/', 'In what year did', $factText);
            $factText = preg_replace('/^was the year/', 'In what year', $factText);
            return ucfirst($factText) . "?";
        } elseif (preg_match('/^is\s+(.+)/', $factText, $isMatches)) {
            return "What number is " . $isMatches[1] . "?";
        } elseif (preg_match('/^was\s+(.+)/', $factText, $wasMatches)) {
            return "What number was " . $wasMatches[1] . "?";
        } elseif (str_contains($factText, 'the')) {
            return "What number represents " . $factText . "?";
        } else {
            return "What number " . $factText . "?";
        }
    }
    
    /**
     * Generate multiple choice options
     */
    private function generateOptions(int $correctAnswer): array
    {
        $options = [$correctAnswer];
        
        while (count($options) < 4) {
            // Generate random options with better distribution
            if ($correctAnswer <= 10) {
                $variance = rand(1, 5);
            } elseif ($correctAnswer <= 100) {
                $variance = rand(1, min(20, $correctAnswer));
            } else {
                $variance = rand(1, min(100, $correctAnswer));
            }
            
            // Generate both positive and negative offsets
            $option = $correctAnswer + (rand(0, 1) ? $variance : -$variance);
            
            // Ensure positive numbers and no duplicates
            if ($option > 0 && !in_array($option, $options)) {
                $options[] = $option;
            } else {
                // If negative or duplicate, try a different approach
                $option = $correctAnswer + rand(1, max(1, $correctAnswer));
                if ($option > 0 && !in_array($option, $options)) {
                    $options[] = $option;
                }
            }
        }
        
        // Shuffle the options so correct answer isn't always in same position
        shuffle($options);
        
        return $options;
    }
    
    /**
     * Get a random math trivia question
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
            ],
            [
                'question' => 'What is 7 multiplied by 7?',
                'correct_answer' => 49,
                'full_fact' => '49 is 7 multiplied by 7.'
            ],
            [
                'question' => 'How many minutes are in an hour?',
                'correct_answer' => 60,
                'full_fact' => '60 is the number of minutes in an hour.'
            ],
            [
                'question' => 'What is the number of degrees in a right angle?',
                'correct_answer' => 90,
                'full_fact' => '90 is the number of degrees in a right angle.'
            ],
            [
                'question' => 'How many years are in a century?',
                'correct_answer' => 100,
                'full_fact' => '100 is the number of years in a century.'
            ],
            [
                'question' => 'What is 12 multiplied by 12?',
                'correct_answer' => 144,
                'full_fact' => '144 is 12 multiplied by 12.'
            ],
            [
                'question' => 'How many degrees are in a circle?',
                'correct_answer' => 360,
                'full_fact' => '360 is the number of degrees in a circle.'
            ],
            [
                'question' => 'What is the atomic number of Gold?',
                'correct_answer' => 79,
                'full_fact' => '79 is the atomic number of Gold.'
            ],
            [
                'question' => 'How many keys are on a standard piano?',
                'correct_answer' => 88,
                'full_fact' => '88 is the number of keys on a standard piano.'
            ],
            [
                'question' => 'What is the freezing point of water in Fahrenheit?',
                'correct_answer' => 32,
                'full_fact' => '32 is the freezing point of water in degrees Fahrenheit.'
            ],
            [
                'question' => 'How many letters are in the English alphabet?',
                'correct_answer' => 26,
                'full_fact' => '26 is the number of letters in the English alphabet.'
            ],
            [
                'question' => 'What is the perfect score in bowling?',
                'correct_answer' => 300,
                'full_fact' => '300 is the perfect score in bowling.'
            ]
        ];
        
        $selected = $questions[array_rand($questions)];
        $selected['options'] = $this->generateOptions($selected['correct_answer']);
        $selected['used_number'] = 'fallback_' . $selected['correct_answer']; 
        
        return $selected;
    }

    /**
     * Get a fallback question that hasn't been used yet
     */
    public function getFallbackQuestionAvoidingRepeats(array $usedNumbers = []): array
    {
        $questions = [
            [
                'question' => 'What is the atomic number of Carbon?',
                'correct_answer' => 6,
                'full_fact' => '6 is the atomic number of Carbon.',
                'id' => 'fallback_carbon'
            ],
            [
                'question' => 'How many sides does a hexagon have?',
                'correct_answer' => 6,
                'full_fact' => '6 is the number of sides a hexagon has.',
                'id' => 'fallback_hexagon'
            ],
            [
                'question' => 'What is the square root of 64?',
                'correct_answer' => 8,
                'full_fact' => '8 is the square root of 64.',
                'id' => 'fallback_sqrt64'
            ],
            [
                'question' => 'How many planets are in our solar system?',
                'correct_answer' => 8,
                'full_fact' => '8 is the number of planets in our solar system.',
                'id' => 'fallback_planets'
            ],
            [
                'question' => 'What is 7 multiplied by 7?',
                'correct_answer' => 49,
                'full_fact' => '49 is 7 multiplied by 7.',
                'id' => 'fallback_7x7'
            ],
            [
                'question' => 'How many minutes are in an hour?',
                'correct_answer' => 60,
                'full_fact' => '60 is the number of minutes in an hour.',
                'id' => 'fallback_minutes'
            ],
            [
                'question' => 'What is the number of degrees in a right angle?',
                'correct_answer' => 90,
                'full_fact' => '90 is the number of degrees in a right angle.',
                'id' => 'fallback_right_angle'
            ],
            [
                'question' => 'How many years are in a century?',
                'correct_answer' => 100,
                'full_fact' => '100 is the number of years in a century.',
                'id' => 'fallback_century'
            ],
            [
                'question' => 'What is 12 multiplied by 12?',
                'correct_answer' => 144,
                'full_fact' => '144 is 12 multiplied by 12.',
                'id' => 'fallback_12x12'
            ],
            [
                'question' => 'How many degrees are in a circle?',
                'correct_answer' => 360,
                'full_fact' => '360 is the number of degrees in a circle.',
                'id' => 'fallback_circle'
            ],
            [
                'question' => 'What is the atomic number of Gold?',
                'correct_answer' => 79,
                'full_fact' => '79 is the atomic number of Gold.',
                'id' => 'fallback_gold'
            ],
            [
                'question' => 'How many keys are on a standard piano?',
                'correct_answer' => 88,
                'full_fact' => '88 is the number of keys on a standard piano.',
                'id' => 'fallback_piano'
            ],
            [
                'question' => 'What is the freezing point of water in Fahrenheit?',
                'correct_answer' => 32,
                'full_fact' => '32 is the freezing point of water in degrees Fahrenheit.',
                'id' => 'fallback_freezing'
            ],
            [
                'question' => 'How many letters are in the English alphabet?',
                'correct_answer' => 26,
                'full_fact' => '26 is the number of letters in the English alphabet.',
                'id' => 'fallback_alphabet'
            ],
            [
                'question' => 'What is the perfect score in bowling?',
                'correct_answer' => 300,
                'full_fact' => '300 is the perfect score in bowling.',
                'id' => 'fallback_bowling'
            ]
        ];
        
        // Filter out already used fallback questions
        $availableQuestions = array_filter($questions, function($question) use ($usedNumbers) {
            return !in_array($question['id'], $usedNumbers);
        });
        
        // If all fallback questions have been used, reset and use any
        if (empty($availableQuestions)) {
            $availableQuestions = $questions;
        }
        
        $selected = $availableQuestions[array_rand($availableQuestions)];
        $selected['options'] = $this->generateOptions($selected['correct_answer']);
        $selected['used_number'] = $selected['id']; 
        
        return $selected;
    }
}
