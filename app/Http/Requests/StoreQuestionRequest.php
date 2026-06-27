<?php

namespace App\Http\Requests;

use App\Enums\QuestionCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', \App\Models\Question::class);
    }

    public function rules()
    {
        $categories = implode(',', array_map(fn($case) => $case->value, QuestionCategory::cases()));
        
        return [
            'category' => "required|in:{$categories}",
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:A,B,C,D',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'audio_transcript' => 'nullable|string|required_with:audio',
            'passage' => 'nullable|string',
        ];
    }
}
