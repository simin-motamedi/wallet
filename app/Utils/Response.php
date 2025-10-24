<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;

class Response
{

    public static function success(array $data = [], string $message = 'success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error(array $errors, string $message = 'error', int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
