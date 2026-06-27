<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PracticeQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category',
        'passage',
        'audio_path',
        'audio_transcript',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
    ];
}
