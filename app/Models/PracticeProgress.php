<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeProgress extends Model
{
    protected $table = 'practice_progresses';

    protected $fillable = [
        'user_id',
        'question_ids',
        'answers',
        'active_question',
        'time_left',
        'tab_violation_count',
    ];

    protected $casts = [
        'question_ids' => 'array',
        'answers' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
