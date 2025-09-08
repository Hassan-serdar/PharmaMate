<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Register a new user and return it with a token.
     *
     * @param array $validatedData
     * @return array
     */
    public function registerUser(array $validatedData): array
    {
        if (!isset($validatedData['role'])) {
            $validatedData['role'] = Role::USER->value;
        }

        $user = User::create($validatedData);

        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token,
        ];
    }
        public function loginUser(array $credentials): array|false
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     *
     *
     * @param User
     * @return void
     */
    public function logoutUser(User $user): void
    {
        $user->tokens()->delete();
    }
}
