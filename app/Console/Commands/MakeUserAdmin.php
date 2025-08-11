<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email? : The email of the user to make admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user an administrator. If no email provided, makes the first user admin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            // Make specific user admin
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("User with email '{$email}' not found.");
                return 1;
            }
            
            if ($user->is_admin) {
                $this->info("User '{$user->name}' ({$user->email}) is already an admin.");
                return 0;
            }
            
            $user->is_admin = true;
            $user->save();
            
            $this->info("User '{$user->name}' ({$user->email}) has been made an admin.");
        } else {
            // Make first user admin
            $user = User::orderBy('id')->first();
            
            if (!$user) {
                $this->error("No users found in the database.");
                return 1;
            }
            
            if ($user->is_admin) {
                $this->info("First user '{$user->name}' ({$user->email}) is already an admin.");
                return 0;
            }
            
            $user->is_admin = true;
            $user->save();
            
            $this->info("First user '{$user->name}' ({$user->email}) has been made an admin.");
        }
        
        return 0;
    }
}
