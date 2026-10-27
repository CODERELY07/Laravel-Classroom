<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Standard JSON success payload understood by public/js/forms.js.
     */
    protected function success(string $message, ?string $redirectUrl = null, array $extra = []): JsonResponse
    {
        return response()->json(array_filter([
            'success' => $message,
            'redirectUrl' => $redirectUrl,
        ]) + $extra);
    }

    protected function error(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }
}
