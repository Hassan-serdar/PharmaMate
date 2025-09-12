<?php

namespace App\Http\Requests\Pharmacist;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicineInInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|integer|min:0',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            if (! array_key_exists('quantity', $data) && ! array_key_exists('price', $data)) {
                $validator->errors()->add('update', 'You must provide at least one of quantity or price.');
            }
        });
    }

}
