<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الصلاحيات يتم التحقق منها بالـ Policy
    }

    public function rules(): array
    {
        return [
            'comment' => 'required|string|max:5000',
        ];
    }
}