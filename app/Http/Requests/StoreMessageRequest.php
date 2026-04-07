<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message_text' => ['required', 'string', 'min:10', 'max:5000'],
            'tone'         => [
                'sometimes',
                'string',
                Rule::in(['professional', 'friendly', 'formal', 'empathetic', 'assertive']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'message_text.required' => 'Message text is required.',
            'message_text.min'      => 'Message must be at least 10 characters.',
            'message_text.max'      => 'Message must not exceed 5000 characters.',
            'tone.in'               => 'Tone must be one of: professional, friendly, formal, empathetic, assertive.',
        ];
    }
}
