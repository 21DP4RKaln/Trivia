<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GameSession;
use Carbon\Carbon;

class Enhanced30DayGameSessionSeeder extends Seeder
{
    /**
     * Run the database seeds with realistic 30-day game data.
     */
    public function run(): void
    {
        // Ensure we have some test users
        $users = collect();
        
        // Create test users if they don't exist
        $testUsers = [
            ['name' => 'Test Player', 'email' => 'test@example.com'],
            ['name' => 'John Smith', 'email' => 'john@example.com'],
            ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
            ['name' => 'Mike Johnson', 'email' => 'mike@example.com'],
            ['name' => 'Sarah Wilson', 'email' => 'sarah@example.com'],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'created_at' => Carbon::now()->subDays(rand(45, 90)),
                ]
            );
            $users->push($user);
        }

        // Clear existing game sessions for these test users to avoid duplicates
        GameSession::whereIn('user_id', $users->pluck('id'))->delete();

        // Generate realistic game sessions over the last 30 days
        $now = Carbon::now();
        $startDate = $now->copy()->subDays(30);
        
        $totalSessions = 0;
        
        for ($day = 0; $day < 30; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            
            // Skip some days randomly to simulate real usage patterns
            if (rand(1, 10) <= 2) continue; 
            
            // Generate 1-8 games per active day
            $gamesThisDay = rand(1, 8);
            
            for ($game = 0; $game < $gamesThisDay; $game++) {
                $user = $users->random();
                
                // Random game performance based on user skill simulation
                $userSkillLevel = rand(1, 100);
                
                if ($userSkillLevel >= 80) {
                    // High skill player
                    $correctAnswers = rand(15, 20);
                    $duration = rand(300, 600); // 5-10 minutes
                } elseif ($userSkillLevel >= 50) {
                    // Medium skill player
                    $correctAnswers = rand(8, 16);
                    $duration = rand(400, 800); // 6.5-13 minutes
                } else {
                    // Beginner player
                    $correctAnswers = rand(2, 12);
                    $duration = rand(600, 1200); // 10-20 minutes
                }
                
                // Add some random variation to make it more realistic
                $gameStartTime = $currentDate->copy()->addHours(rand(8, 22))->addMinutes(rand(0, 59));
                $gameEndTime = $gameStartTime->copy()->addSeconds($duration);
                // Calculate accuracy - this will be automatically calculated by the model's accessor
                $calculatedAccuracy = ($correctAnswers / 20) * 100;
                
                // Simulate some incomplete games 
                $completed = rand(1, 10) > 1; 
                  if (!$completed) {
                    $correctAnswers = rand(1, 10); 
                    $duration = rand(60, 300); 
                    $gameEndTime = null;
                    // Don't set accuracy for incomplete games - model will calculate it
                }

                GameSession::create([
                    'user_id' => $user->id,
                    'total_questions' => 20,
                    'correct_answers' => $correctAnswers,
                    'start_time' => $gameStartTime,
                    'end_time' => $gameEndTime,
                    'duration_seconds' => $duration,
                    'completed' => $completed,
                    'created_at' => $gameStartTime,
                    'updated_at' => $gameEndTime ?? $gameStartTime,
                ]);
                
                $totalSessions++;
            }
        }

        // Add a few games from today
        $today = Carbon::today();
        for ($i = 0; $i < rand(3, 7); $i++) {
            $user = $users->random();
            $correctAnswers = rand(8, 20);
            $duration = rand(300, 900);
            $gameStartTime = $today->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59));
            $gameEndTime = $gameStartTime->copy()->addSeconds($duration);
            
            GameSession::create([
                'user_id' => $user->id,
                'total_questions' => 20,
                'correct_answers' => $correctAnswers,
                'start_time' => $gameStartTime,
                'end_time' => $gameEndTime,
                'duration_seconds' => $duration,
                'completed' => true,
                'created_at' => $gameStartTime,
                'updated_at' => $gameEndTime,
            ]);
            
            $totalSessions++;
        }

        $this->command->info("Created {$totalSessions} realistic game sessions over 30 days for " . $users->count() . " users");
        $this->command->info("Users created/updated: " . $users->pluck('email')->implode(', '));
    }
}
