<?php

namespace App\Http\Controllers;

use App\Models\Classess;
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

    /** Archived classes are read-only: no new posts, answers, grades, chat or member changes. */
    protected function ensureActive(Classess $class): void
    {
        abort_if($class->isArchived(), 403, 'This class is archived. Restore it to make changes.');
    }
}
