<?php

namespace App\Http\Requests\Pharmacist;

use App\Enums\Role;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AddMedicineToInventoryRequest extends FormRequest
{
    protected $pharmacy;

    public function authorize(): bool
    {

        $user = $this->user();

        if ($user->role !== Role::PHARMACIST) {
            return false;
        }

        $this->pharmacy = $user->pharmacy;
        if (!$this->pharmacy) {
            return false;
        }

        return $user->can('manageInventory', $this->pharmacy);
    }

    public function rules(): array
    {
        $pharmacyId = auth()->user()->pharmacy->id;

        return [
            // الدواء لازم يكون موجود بالجدول المركزي
            'medicine_id' => [
                'required',
                'exists:medicines,id',
                Rule::unique('medicine_pharmacy', 'medicine_id')
                    ->where(fn($query) => $query->where('pharmacy_id', $pharmacyId)),
            ],      
            'quantity' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
        ];
    }
}
