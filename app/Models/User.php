<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
    
    /**
     * Get the game sessions for this user
     */
    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    /**
     * Get user's best game session (highest score)
     */
    public function getBestSession()
    {
        return $this->gameSessions()
            ->where('completed', true)
            ->orderByDesc('correct_answers')
            ->orderBy('duration_seconds')
            ->first();
    }

    /**
     * Get user's fastest completion time
     */
    public function getFastestSession()
    {
        return $this->gameSessions()
            ->where('completed', true)
            ->where('correct_answers', '>', 0)
            ->where('duration_seconds', '>', 0)
            ->orderBy('duration_seconds')
            ->first();
    }

    /**
     * Get total games played
     */
    public function getTotalGamesAttribute()
    {
        return $this->gameSessions()->where('completed', true)->count();
    }

    /**
     * Get average accuracy
     */
    public function getAverageAccuracyAttribute()
    {
        $completedGames = $this->gameSessions()->where('completed', true)->get();
        
        if ($completedGames->isEmpty()) {
            return 0;
        }
        
        $totalAccuracy = $completedGames->sum(function ($session) {
            return $session->accuracy;
        });
        
        return round($totalAccuracy / $completedGames->count(), 1);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Make user an admin
     */
    public function makeAdmin(): void
    {
        $this->update(['is_admin' => true]);
    }

    /**
     * Remove admin privileges
     */
    public function removeAdmin(): void
    {
        $this->update(['is_admin' => false]);
    }
}
