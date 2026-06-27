<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeReviewUsage extends Model
{
    protected $fillable = [
        'user_id',
        'practice_result_item_id',
        'question_hash',
        'from_cache',
        'generated',
    ];

    protected $casts = [
        'from_cache' => 'boolean',
        'generated' => 'boolean',
    ];
}
