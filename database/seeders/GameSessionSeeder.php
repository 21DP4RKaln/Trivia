<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GameSession;
use Carbon\Carbon;

class GameSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user if none exists
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Player',
                'password' => bcrypt('password'),
            ]
        );

        // Create some sample game sessions
        $gameSessions = [
            [
                'user_id' => $user->id,
                'total_questions' => 20,
                'correct_answers' => 12,
                'start_time' => Carbon::now()->subDays(5),
                'end_time' => Carbon::now()->subDays(5)->addMinutes(8),
                'duration_seconds' => 480, // 8 minutes
                'completed' => true,
            ],
            [
                'user_id' => $user->id,
                'total_questions' => 20,
                'correct_answers' => 20,
                'start_time' => Carbon::now()->subDays(3),
                'end_time' => Carbon::now()->subDays(3)->addMinutes(12),
                'duration_seconds' => 720, // 12 minutes
                'completed' => true,
            ],
            [
                'user_id' => $user->id,
                'total_questions' => 20,
                'correct_answers' => 7,
                'start_time' => Carbon::now()->subDays(2),
                'end_time' => Carbon::now()->subDays(2)->addMinutes(4),
                'duration_seconds' => 240, // 4 minutes
                'completed' => true,
            ],
            [
                'user_id' => $user->id,
                'total_questions' => 20,
                'correct_answers' => 15,
                'start_time' => Carbon::now()->subDay(),
                'end_time' => Carbon::now()->subDay()->addMinutes(10),
                'duration_seconds' => 600, // 10 minutes
                'completed' => true,
            ],
            [
                'user_id' => $user->id,
                'total_questions' => 20,
                'correct_answers' => 18,
                'start_time' => Carbon::now()->subHours(2),
                'end_time' => Carbon::now()->subHours(2)->addMinutes(15),
                'duration_seconds' => 900, // 15 minutes
                'completed' => true,
            ],
        ];

        foreach ($gameSessions as $session) {
            GameSession::create($session);
        }

        $this->command->info('Sample game sessions created for user: ' . $user->email);
    }
}
