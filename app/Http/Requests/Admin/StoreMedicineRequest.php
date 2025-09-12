<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\MedicineTypeEnum;
use Illuminate\Validation\Rules\Enum;

class StoreMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        // الصلاحية يتم التحقق منها في الكونترولر باستخدام الـ Policy
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:medicines,name',
            'active_ingredient' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'type' => ['required', new Enum(MedicineTypeEnum::class)],
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
