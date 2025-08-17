<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_identifier',
        'session_token',
        'game_state',
        'expires_at',
        'total_questions',
        'correct_answers',
        'accuracy',
        'start_time',
        'end_time',
        'duration_seconds',
        'question_times',
        'completed'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'expires_at' => 'datetime',
        'question_times' => 'array',
        'game_state' => 'array',
        'completed' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if this is a guest game session
     */
    public function isGuest(): bool
    {
        return $this->user_id === null;
    }
    
    /**
     * Get the player name (user name or "Guest Player")
     */
    public function getPlayerNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Guest Player';
    }
    
    /**
     * Get the player email (user email or null for guests)
     */
    public function getPlayerEmailAttribute(): ?string
    {
        return $this->user ? $this->user->email : null;
    }
    
    public function getDurationAttribute(): string
    {
        // If duration_seconds is set (even if 0), use it and don't fall back to timestamps
        if ($this->duration_seconds !== null) {
            $minutes = floor($this->duration_seconds / 60);
            $seconds = $this->duration_seconds % 60;
            return sprintf('%d:%02d', $minutes, $seconds);
        }
        
        // Only fall back to calculating from timestamps if duration_seconds is null
        if ($this->start_time && $this->end_time) {
            $calculatedSeconds = $this->end_time->diffInSeconds($this->start_time);
            if ($calculatedSeconds > 0) {
                $minutes = floor($calculatedSeconds / 60);
                $seconds = $calculatedSeconds % 60;
                return sprintf('%d:%02d', $minutes, $seconds);
            }
        }
        
        return '0:00';
    }

    public function getAccuracyAttribute(): float
    {
        if ($this->total_questions == 0) {
            return 0;
        }
        
        return round(($this->correct_answers / $this->total_questions) * 100, 1);
    }

    /**
     * Get average time per question
     */
    public function getAverageQuestionTimeAttribute(): float
    {
        if (!$this->question_times || empty($this->question_times)) {
            return 0;
        }

        $totalTime = array_sum(array_column($this->question_times, 'duration'));
        return round($totalTime / count($this->question_times), 1);
    }

    /**
     * Get fastest question time
     */
    public function getFastestQuestionTimeAttribute(): int
    {
        if (!$this->question_times || empty($this->question_times)) {
            return 0;
        }

        return min(array_column($this->question_times, 'duration'));
    }

    /**
     * Get slowest question time
     */
    public function getSlowestQuestionTimeAttribute(): int
    {
        if (!$this->question_times || empty($this->question_times)) {
            return 0;
        }

        return max(array_column($this->question_times, 'duration'));
    }

    /**
     * Get question timing breakdown (fast, medium, slow)
     */
    public function getQuestionTimingBreakdownAttribute(): array
    {
        if (!$this->question_times || empty($this->question_times)) {
            return ['fast' => 0, 'medium' => 0, 'slow' => 0];
        }

        $fast = $medium = $slow = 0;
        
        foreach ($this->question_times as $qt) {
            if ($qt['duration'] <= 5) {
                $fast++;
            } elseif ($qt['duration'] <= 10) {
                $medium++;
            } else {
                $slow++;
            }
        }

        return ['fast' => $fast, 'medium' => $medium, 'slow' => $slow];
    }

    /**
     * Check if this game session is active and not expired
     */
    public function isActive(): bool
    {
        return !$this->completed && 
               $this->expires_at && 
               $this->expires_at->isFuture();
    }
    
    /**
     * Generate a unique session token for game persistence
     */
    public static function generateSessionToken(): string
    {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Find an active saved game by session token
     */
    public static function findActiveGameByToken(string $token): ?self
    {
        return self::where('session_token', $token)
                   ->where('completed', false)
                   ->where('expires_at', '>', now())
                   ->first();
    }
    
    /**
     * Save the current game state
     */
    public function saveGameState(array $gameState): bool
    {
        // Convert Carbon instances to strings for JSON storage
        if (isset($gameState['start_time']) && $gameState['start_time'] instanceof \Carbon\Carbon) {
            $gameState['start_time'] = $gameState['start_time']->toISOString();
        }
        if (isset($gameState['gameplay_start_time']) && $gameState['gameplay_start_time'] instanceof \Carbon\Carbon) {
            $gameState['gameplay_start_time'] = $gameState['gameplay_start_time']->toISOString();
        }
        
        $this->game_state = $gameState;
        $this->expires_at = now()->addHours(24); // Game saves expire after 24 hours
        return $this->save();
    }
    
    /**
     * Get the restored game state with proper datetime conversion
     */
    public function getRestoredGameState(): ?array
    {
        if (!$this->game_state) {
            return null;
        }
        
        $gameState = $this->game_state;
        
        // Convert datetime strings back to Carbon instances
        if (isset($gameState['start_time']) && is_string($gameState['start_time'])) {
            $gameState['start_time'] = \Carbon\Carbon::parse($gameState['start_time']);
        }
        if (isset($gameState['gameplay_start_time']) && is_string($gameState['gameplay_start_time'])) {
            $gameState['gameplay_start_time'] = \Carbon\Carbon::parse($gameState['gameplay_start_time']);
        }
        
        return $gameState;
    }

    /**
     * Clear expired game sessions
     */
    public static function clearExpiredGames(): int
    {
        return self::where('completed', false)
                   ->where('expires_at', '<', now())
                   ->delete();
    }
}
