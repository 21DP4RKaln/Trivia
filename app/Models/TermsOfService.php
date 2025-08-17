<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermsOfService extends Model
{
    protected $table = 'terms_of_service';
    
    protected $fillable = [
        'content',
        'version',
        'effective_date',
        'is_active',
        'updated_by'
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function getActive()
    {
        return static::where('is_active', true)->latest('created_at')->first();
    }

    public static function getCurrentContent()
    {
        $activeTerms = static::getActive();
        
        if (!$activeTerms) {
            // Return default content if no terms exist
            return [
                'content' => static::getDefaultContent(),
                'version' => '1.0',
                'effective_date' => now()->format('F j, Y'),
                'last_updated' => now()->format('F j, Y'),
                'updated_by' => 'System'
            ];
        }

        return [
            'content' => $activeTerms->content,
            'version' => $activeTerms->version,
            'effective_date' => $activeTerms->effective_date instanceof \Carbon\Carbon ? $activeTerms->effective_date->format('F j, Y') : $activeTerms->effective_date,
            'last_updated' => $activeTerms->updated_at->format('F j, Y'),
            'updated_by' => $activeTerms->updatedBy->name ?? 'System'
        ];
    }

    /**
     * Get word count of terms content
     */
    public function getWordCountAttribute()
    {
        return str_word_count(strip_tags($this->content));
    }

    /**
     * Get estimated read time in minutes
     */
    public function getEstimatedReadTimeAttribute()
    {
        $wordCount = $this->word_count;
        $wordsPerMinute = 250; // Average reading speed
        $minutes = ceil($wordCount / $wordsPerMinute);
        return $minutes;
    }

    /**
     * Get content preview (first few sentences)
     */
    public function getContentPreviewAttribute()
    {
        $content = strip_tags($this->content);
        $sentences = explode('.', $content);
        $preview = implode('.', array_slice($sentences, 0, 3));
        return $preview . (count($sentences) > 3 ? '...' : '');
    }

    /**
     * Check if content contains specific keywords
     */
    public function containsKeywords($keywords)
    {
        $content = strtolower($this->content);
        foreach ($keywords as $keyword) {
            if (strpos($content, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all versions for comparison
     */
    public static function getVersionsForComparison()
    {
        return static::orderBy('created_at', 'desc')
            ->select('id', 'version', 'effective_date', 'created_at')
            ->get();
    }

    private static function getDefaultContent()
    {
        return "# Terms of Service

Welcome to Number Trivia Game!

## 1. Game Rules
- Answer trivia questions about numbers and mathematics
- Strive for accuracy and speed  
- Enjoy learning while playing!

## 2. User Conduct
- Be respectful to other players
- Do not attempt to cheat or exploit the system
- Report any bugs or issues you encounter

## 3. Privacy
- We respect your privacy and protect your personal data
- Game statistics are used to improve the experience
- Your email is kept secure and private

## 4. Updates
These terms may be updated from time to time. You will be notified of any significant changes.

Thank you for playing Number Trivia Game!";
    }
}
