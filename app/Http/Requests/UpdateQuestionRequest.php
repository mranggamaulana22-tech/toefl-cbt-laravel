<?php

namespace App\Http\Requests;

use App\Enums\QuestionCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize()
    {
        $question = $this->route('question');
        return $this->user()->can('update', $question);
    }

    public function rules()
    {
        $question = $this->route('question');
        $hasAudio = $this->hasFile('audio') || !empty($question->audio_path);
        $categories = implode(',', array_map(fn($case) => $case->value, QuestionCategory::cases()));

        $rules = [
            'category' => "required|in:{$categories}",
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:A,B,C,D',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'audio_transcript' => ['nullable','string'],
            'passage' => 'nullable|string',
        ];

        if ($hasAudio) {
            $rules['audio_transcript'][] = 'required';
        }

        return $rules;
    }
}
