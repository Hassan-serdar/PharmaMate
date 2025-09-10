<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use ApiResponser,AuthorizesRequests;

    public function __construct(protected UserService $userService)
    {
    }

    /**
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);
        $this->userService->deleteAccount($user);
        return $this->success(message: 'User has been deleted successfully.');
    }
}
