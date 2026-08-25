<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'access_code' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'access_code.required' => 'Kode akses ujian wajib diisi.',
        ];
    }
}