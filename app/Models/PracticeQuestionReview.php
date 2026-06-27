<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeQuestionReview extends Model
{
    protected $fillable = [
        'question_hash',
        'question_snapshot',
        'ai_review_json',
        'ai_review_text',
        'ai_model_used',
        'ai_generated_at',
        'expires_at',
    ];

    protected $casts = [
        'question_snapshot' => 'array',
        'ai_review_json' => 'array',
        'ai_generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
