<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Mail\ResetPasswordMail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable implements MustVerifyEmail
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
        'is_banned',
        'banned_at',
        'ban_reason',
        'ban_expires_at',
        'banned_by',
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
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
            'ban_expires_at' => 'datetime',
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

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        try {
            Mail::to($this->email)->send(new ResetPasswordMail($token, $this->email, $this));
        } catch (\Exception $e) {
            // Fallback to default Laravel notification if custom mail fails
            $this->notify(new ResetPassword($token));
        }
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        try {
            Mail::to($this->email)->send(new EmailVerificationMail($this));
        } catch (\Exception $e) {
            // Fallback to default Laravel notification if custom mail fails
            $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
        }
    }

    /**
     * Get the admin who banned this user
     */
    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * Check if user is currently banned
     */
    public function isBanned(): bool
    {
        if (!$this->is_banned) {
            return false;
        }

        // Check if ban has expired
        if ($this->ban_expires_at && $this->ban_expires_at->isPast()) {
            $this->unban();
            return false;
        }

        return true;
    }

    /**
     * Ban the user
     */
    public function ban(string $reason = null, $expiresAt = null, $bannedBy = null): void
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $reason,
            'ban_expires_at' => $expiresAt,
            'banned_by' => $bannedBy,
        ]);
    }

    /**
     * Unban the user
     */
    public function unban(): void
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
            'ban_expires_at' => null,
            'banned_by' => null,
        ]);
    }

    /**
     * Get ban status for display
     */
    public function getBanStatusAttribute(): string
    {
        if (!$this->is_banned) {
            return 'Active';
        }

        if ($this->ban_expires_at) {
            if ($this->ban_expires_at->isPast()) {
                return 'Expired (Auto-unbanned)';
            }
            return 'Temporarily Banned (Until ' . $this->ban_expires_at->format('M j, Y g:i A') . ')';
        }

        return 'Permanently Banned';
    }

    /**
     * Check if ban is temporary
     */
    public function hasTemporaryBan(): bool
    {
        return $this->is_banned && $this->ban_expires_at !== null;
    }

    /**
     * Check if ban is permanent
     */
    public function hasPermanentBan(): bool
    {
        return $this->is_banned && $this->ban_expires_at === null;
    }
}
