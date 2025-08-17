<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email : The email address to send test mail to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the mail configuration by sending a test password reset email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $token = 'test-token-' . time();
        
        try {
            $this->info("Sending test email to: {$email}");
            
            // Create a dummy user for testing
            $testUser = new User();
            $testUser->name = 'Test User';
            $testUser->email = $email;
            
            Mail::to($email)->send(new ResetPasswordMail($token, $email, $testUser));
            
            $this->info("Test email sent successfully!");
            $this->info("Check your email inbox and spam folder.");
            
        } catch (\Exception $e) {
            $this->error("Failed to send test email:");
            $this->error($e->getMessage());
            
            $this->newLine();
            $this->warn("Troubleshooting tips:");
            $this->line("1. Check your .env file mail configuration");
            $this->line("2. Ensure MAIL_USERNAME and MAIL_PASSWORD are correct");
            $this->line("3. For Gmail, use an App Password instead of your regular password");
            $this->line("4. Check if 2-factor authentication is enabled");
        }
    }
}
