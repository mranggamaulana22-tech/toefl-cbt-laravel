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
        'paket_soal_id',
        'started_at',
        'correct_listening',
        'correct_structure',
        'correct_reading',
        'score_total',
        'certificate_path',
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

    public function paketSoal(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereNotNull('submitted_at');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeLatestSubmitted(Builder $query): Builder
    {
        return $query->submitted()->latest('submitted_at');
    }

    public function scopeByCycle(Builder $query, int $cycle): Builder
    {
        return $query->where('exam_cycle', $cycle);
    }
}