<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearGameSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trivia:clear-sessions {--user= : Clear sessions for specific user email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear game sessions for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->option('user');
        
        if ($userEmail) {
            $user = \App\Models\User::where('email', $userEmail)->first();
            if (!$user) {
                $this->error("User with email {$userEmail} not found.");
                return 1;
            }
            
            $count = $user->gameSessions()->delete();
            $this->info("Cleared {$count} game sessions for user {$userEmail}.");
        } else {
            $count = \App\Models\GameSession::query()->delete();
            $this->info("Cleared {$count} game sessions for all users.");
        }
        
        return 0;
    }
}
