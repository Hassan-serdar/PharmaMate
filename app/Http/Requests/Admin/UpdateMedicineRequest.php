<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\MedicineTypeEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class UpdateMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $medicineId = $this->route('medicine')->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('medicines')->ignore($medicineId)],
            'active_ingredient' => 'sometimes|required|string|max:255',
            'dosage' => 'sometimes|required|string|max:100',
            'type' => ['sometimes', 'required', new Enum(MedicineTypeEnum::class)],
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
