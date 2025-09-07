<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'firstname' => 'sometimes|required|string|max:255',
            'lastname' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $userId,
            'phonenumber' => 'sometimes|required|string|max:20',
            'current_password' => 'required_with:password|string',
            'password' => 'sometimes|required|string|min:8|confirmed',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('password') && !Hash::check($this->input('current_password'), $this->user()->password)) {
                $validator->errors()->add(
                    'current_password',
                    'The provided current password does not match your actual password.'
                );
                return;
            }
            if (empty($this->all())) {
                $validator->errors()->add(
                    'general',
                    'You must provide at least one field to update.'
                );
            }
        });
    }
}
