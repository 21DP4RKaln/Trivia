<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\GameSession;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;

class GamePersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_session_can_be_saved_with_token(): void
    {
        $response = $this->post('/start');
        
        $response->assertStatus(200);
        
        // Check that a game session was created
        $this->assertDatabaseHas('game_sessions', [
            'completed' => false,
        ]);
        
        // Check that a session token was generated
        $gameSession = GameSession::where('completed', false)->first();
        $this->assertNotNull($gameSession->session_token);
        $this->assertNotNull($gameSession->expires_at);
    }

    public function test_saved_game_can_be_restored(): void
    {
        // Create a game session with saved state
        $gameSession = GameSession::create([
            'user_id' => null,
            'guest_identifier' => 'test_guest',
            'session_token' => GameSession::generateSessionToken(),
            'game_state' => [
                'current_question' => 5,
                'correct_answers' => 3,
                'used_questions' => [1, 2, 3, 4],
                'game_active' => true
            ],
            'expires_at' => now()->addHours(24),
            'completed' => false
        ]);

        // Mock having the cookie
        $response = $this->withCookie('trivia_game_token', $gameSession->session_token)
                         ->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('savedGame');
    }

    public function test_expired_games_are_cleaned_up(): void
    {
        // Create an expired game session
        GameSession::create([
            'user_id' => null,
            'guest_identifier' => 'expired_guest',
            'session_token' => GameSession::generateSessionToken(),
            'game_state' => ['test' => 'data'],
            'expires_at' => now()->subHours(1), // Expired 1 hour ago
            'completed' => false
        ]);

        // Run cleanup
        $count = GameSession::clearExpiredGames();

        $this->assertEquals(1, $count);
        $this->assertDatabaseMissing('game_sessions', [
            'guest_identifier' => 'expired_guest'
        ]);
    }

    public function test_continue_game_route_works(): void
    {
        $gameSession = GameSession::create([
            'user_id' => null,
            'guest_identifier' => 'continue_test',
            'session_token' => GameSession::generateSessionToken(),
            'game_state' => [
                'current_question' => 3,
                'correct_answers' => 2,
                'game_active' => true
            ],
            'expires_at' => now()->addHours(24),
            'completed' => false
        ]);

        $response = $this->withCookie('trivia_game_token', $gameSession->session_token)
                         ->post('/continue');

        $response->assertStatus(200);
    }

    public function test_abandon_game_clears_saved_data(): void
    {
        $gameSession = GameSession::create([
            'user_id' => null,
            'guest_identifier' => 'abandon_test',
            'session_token' => GameSession::generateSessionToken(),
            'game_state' => ['test' => 'data'],
            'expires_at' => now()->addHours(24),
            'completed' => false
        ]);

        $response = $this->withCookie('trivia_game_token', $gameSession->session_token)
                         ->post('/abandon');

        $response->assertRedirect('/');
        
        $gameSession->refresh();
        $this->assertNull($gameSession->game_state);
        $this->assertTrue($gameSession->completed);
    }
}
