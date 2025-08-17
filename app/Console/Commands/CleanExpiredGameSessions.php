<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GameSession;

class CleanExpiredGameSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trivia:clean-expired-games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired game sessions to free up storage space';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up expired game sessions...');
        
        $count = GameSession::clearExpiredGames();
        
        if ($count > 0) {
            $this->info("Cleaned up {$count} expired game session(s).");
        } else {
            $this->info('No expired game sessions found.');
        }
        
        return 0;
    }
}
