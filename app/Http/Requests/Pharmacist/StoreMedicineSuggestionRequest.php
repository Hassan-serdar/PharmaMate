<?php

namespace App\Http\Requests\Pharmacist;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\MedicineTypeEnum;
use Illuminate\Validation\Rules\Enum;

class StoreMedicineSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'active_ingredient' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'type' => ['required', new Enum(MedicineTypeEnum::class)],
        ];
    }
}
