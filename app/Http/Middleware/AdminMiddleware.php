<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Role;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // إذا المستخدم مسجل دخول ودوره أدمن أو سوبر أدمن منخليه يكمل
        if ($user && ($user->role === Role::ADMIN || $user->role === Role::SUPER_ADMIN)) {
            return $next($request);
        }

        // إذا لأ منرجعله رسالة خطأ
        return response()->json(['message' => 'This action is unauthorized.'], 403);
    }
}
