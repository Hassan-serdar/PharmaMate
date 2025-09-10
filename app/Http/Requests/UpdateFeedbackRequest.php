<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'sometimes|required|string|max:255',
            'message' => 'sometimes|required|string|max:5000',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->all())) {
                $validator->errors()->add('general', 'You must provide at least one field to update.');
            }
        });
    }
}