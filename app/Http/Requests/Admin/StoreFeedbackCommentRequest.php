<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'comment' => 'required|string|max:5000',
            'is_private' => 'required|boolean',

        ];
    }
}