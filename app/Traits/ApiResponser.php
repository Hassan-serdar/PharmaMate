<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait ApiResponser
{
    /**
     * Build a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function success($data = null, string $message = 'Operation was successful.', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Build an error response.
     *
     * @param string|null $message
     * @param int $code
     * @param mixed|null $errors
     * @return JsonResponse
     */
    protected function error(string $message = null, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message ?: 'An error occurred.',
            'errors' => $errors,
        ], $code);
    }

    /**
     * Build a validation error response.
     *
     * @param ValidationException $e
     * @return JsonResponse
     */
    protected function validationError(ValidationException $e): JsonResponse
    {
        // Log the validation error for debugging purposes
        Log::warning('Validation failed for request.', [
            'errors' => $e->errors(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
        ]);

        return $this->error('The given data was invalid.', 422, $e->errors());
    }

    /**
     * Build a "not found" error response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Build an "unauthorized" error response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorized(string $message = 'You are not authorized to perform this action.'): JsonResponse
    {
        return $this->error($message, 403);
    }
}