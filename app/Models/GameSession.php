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
        'question_times' => 'array',
        'completed' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
