<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource; 
use App\Services\AuthService;
use App\Services\UserService;
use App\Traits\ApiResponser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponser, AuthorizesRequests;

    public function __construct(
        protected AuthService $authService,
        protected UserService $userService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->registerUser($request->validated());

            return $this->success(
                data: [
                    'user' => new UserResource($result['user']),
                    'access_token' => $result['token'],
                    'token_type' => 'Bearer',
                ],
                message: 'User registered successfully.',
                code: 201
            );
        } catch (\Exception $e) {
            Log::critical('User registration failed.', ['error_message' => $e->getMessage()]);
            return $this->error('Registration failed. Please try again later.', 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginUser($request->validated());

        if (!$result) {
            return $this->error('Invalid credentials', 401);
        }

        return $this->success(
            data: [
                'user' => new UserResource($result['user']),
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
            ],
            message: 'Login successful.'
        );
    }

    public function logout(): JsonResponse
    {
        $this->authService->logoutUser(auth()->user());
        return $this->success(message: 'You have successfully been logged out.');
    }

    public function profile(): JsonResponse
    {
        return $this->success(new UserResource(auth()->user()), 'User profile retrieved successfully.');
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $updatedUser = $this->userService->updateProfile(auth()->user(), $request->validated());
            return $this->success(new UserResource($updatedUser), 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Profile update failed.', ['user_id' => auth()->id(), 'error_message' => $e->getMessage()]);
            return $this->error('Profile update failed. Please try again.', 500);
        }
    }

    public function destroyAccount(): JsonResponse
    {
        try {
            $user = auth()->user();
            $this->authorize('delete', $user);
            $this->userService->deleteAccount($user);
            return $this->success(message: 'Your account has been deleted successfully.');

        } catch (\Exception $e) {
            Log::critical('Account deletion failed.', ['user_id' => auth()->id(), 'error_message' => $e->getMessage()]);
            return $this->error('Could not delete account. Please try again.', 500);
        }
    }
}

