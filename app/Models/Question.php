<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'paket_soal_id',
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

    public function paketSoal(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class);
    }
}