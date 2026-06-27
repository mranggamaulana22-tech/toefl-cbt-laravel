<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticeResult extends Model
{
    protected $fillable = [
        'user_id',
        'total_questions',
        'correct_listening',
        'correct_structure',
        'correct_reading',
        'score_total',
        'started_at',
        'submitted_at',
        'ai_suggestion',
        'ai_generated_at',
        'ai_model_used',
        'ai_parsed_json',
        'ai_parser_version',
        'ai_status',
        'ai_error',
        'ai_requested_at',
        'ai_completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'ai_generated_at' => 'datetime',
        'ai_parsed_json' => 'array',
        'ai_requested_at' => 'datetime',
        'ai_completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PracticeResultItem::class)->orderBy('question_order');
    }

    /**
     * Scope: Filter to submitted results only
     */
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereNotNull('submitted_at');
    }

    /**
     * Scope: Filter to specific user
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get latest submitted result
     */
    public function scopeLatestSubmitted(Builder $query): Builder
    {
        return $query->submitted()->latest('submitted_at');
    }
}
