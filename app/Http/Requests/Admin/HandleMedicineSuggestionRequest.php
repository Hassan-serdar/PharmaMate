<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HandleMedicineSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approved' => 'required|boolean',
            // سبب الرفض مطلوب فقط في حال كانت قيمة 'approved' هي 'false'
            'rejection_reason' => 'required_if:approved,false|nullable|string|max:1000',
        ];
    }
}
