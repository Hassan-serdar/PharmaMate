<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;

class AuthController extends Controller
{
    use ApiResponser;

    /**
     * Register a new user.
     * The validation is handled by RegisterRequest.
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            if (!isset($validatedData['role'])) {
                $validatedData['role'] = Role::USER->value;
            }
            $user = User::create($validatedData);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success(
                data: [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
                message: 'User registered successfully.',
                code: 201
            );
        } catch (\Exception $e) {
            Log::critical('User registration failed.', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error('Registration failed. Please try again later.', 500);
        }
    }

    /**
     * Login user and create token.
     * The validation is handled by LoginRequest.
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (!Auth::attempt($credentials)) {
                return $this->error('Invalid credentials.', 401);
            }

            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful.');

        } catch (\Exception $e) {
            Log::error('User login process failed.', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            return $this->error('An unexpected error occurred.', 500);
        }
    }

    /**
     * Get the authenticated User's profile.
     */
    public function profile(Request $request)
    {
        return $this->success($request->user(), 'User profile fetched successfully.');
    }

    /**
     * Update the authenticated user's profile.
     * The validation is handled by UpdateProfileRequest.
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();
            $validatedData = $request->validated();
    
            $user->update($validatedData);

            return $this->success($user->fresh(), 'Profile updated successfully.');

        } catch (\Exception $e) {
            Log::error('Profile update failed.', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return $this->error('Could not update profile.', 500);
        }
    }

    /**
     * Logout user (Revoke the token).
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->success(null, 'Successfully logged out.');
        } catch (\Exception $e) {
            Log::error('Logout failed.', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return $this->error('Could not log out.', 500);
        }
    }

    /**
     * Delete the authenticated user's account.
     */
    public function destroyAccount(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();
            $user->delete();

            return $this->success(null, 'Your account has been successfully deleted.');

        } catch (\Exception $e) {
            Log::error('Account deletion failed.', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return $this->error('Could not delete account.', 500);
        }
    }
}
