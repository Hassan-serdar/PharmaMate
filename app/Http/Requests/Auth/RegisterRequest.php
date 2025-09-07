<?php

namespace App\Http\Requests\Auth;

use App\Enums\Role;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phonenumber' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ];

        if (auth()->check()) {
            $userRole = auth()->user()->role;
            if (($userRole instanceof Role && $userRole === Role::ADMIN) || $userRole === Role::ADMIN->value) {
                $rules['role'] = ['required', new Enum(Role::class)];
            }
        }

        return $rules;
    }
}
