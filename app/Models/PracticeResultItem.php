<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeResultItem extends Model
{
    protected $fillable = [
        'practice_result_id',
        'practice_question_id',
        'question_order',
        'user_answer',
        'is_correct',
        'question_hash',
        'question_snapshot',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'question_snapshot' => 'array',
    ];

    public function practiceResult(): BelongsTo
    {
        return $this->belongsTo(PracticeResult::class);
    }

    public function practiceQuestion(): BelongsTo
    {
        return $this->belongsTo(PracticeQuestion::class);
    }
}
