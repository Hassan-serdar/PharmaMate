<?php

namespace App\Http\Requests\Admin;

use App\Enums\MedicineTypeEnum;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('medicines')->ignore($medicineId)],
            'active_ingredient' => 'sometimes|string|max:255',
            'dosage' => 'sometimes|string|max:100',
            'type' => ['sometimes', new Enum(MedicineTypeEnum::class)],
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
        public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $data = $this->except(['_method']);
            if (empty($data)) {
                $validator->errors()->add(
                    'general',
                    'You must provide at least one field to update'
                );
            }
        });
    }

}
