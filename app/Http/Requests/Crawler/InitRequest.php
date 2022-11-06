<?php

namespace App\Http\Requests\Crawler;

use Illuminate\Foundation\Http\FormRequest;

class InitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target' => [
                'required',
                'active_url',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'target.active_url' => 'Most likely you are using non existing URL for this assignment',
        ];
    }
}
