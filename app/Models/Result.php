<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_cycle',
        'started_at',
        'correct_listening',
        'correct_structure',
        'correct_reading',
        'score_total',
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

    /**
     * Scope: Filter by exam cycle
     */
    public function scopeByCycle(Builder $query, int $cycle): Builder
    {
        return $query->where('exam_cycle', $cycle);
    }
}
