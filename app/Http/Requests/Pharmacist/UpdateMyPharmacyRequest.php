<?php

namespace App\Http\Requests\Pharmacist;

use App\Enums\PharmacyStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMyPharmacyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // تأكد أن المستخدم عنده صيدلية ويملكها
        $pharmacy = $this->user()->pharmacy;
        return $pharmacy && $this->user()->can('Pharmacistupdate', $pharmacy);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['sometimes', 'string', 'max:20'],
            'address_line_1' => ['sometimes', 'string', 'max:255'],
            'opening_time' => ['sometimes', 'date_format:H:i'], // H:i for 24-hour format like 09:00
            'closing_time' => ['sometimes', 'date_format:H:i', 'after:opening_time'],
            'status' => ['sometimes', 'string', new Enum(PharmacyStatusEnum::class)],
        ];
    }
}