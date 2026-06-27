<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => 'required|array',
            'answers.*' => 'required|string|in:A,B,C,D',
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'Jawaban harus diisi.',
            'answers.*.in' => 'Jawaban harus pilihan A, B, C, atau D.',
        ];
    }
}
