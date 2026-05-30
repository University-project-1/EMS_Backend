<?php

use Illuminate\Http\JsonResponse;

if (! function_exists('successResponse')) {
    function successResponse(mixed $data = null, string $message = 'success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}

if (! function_exists('errorResponse')) {
    function errorResponse(string $message = 'error', mixed $data = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}