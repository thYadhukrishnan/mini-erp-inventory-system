<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidOrderTransitionException extends Exception
{
    public function __construct(string $message = 'Invalid order status transition.')
    {
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error'   => 'INVALID_TRANSITION',
        ], 422);
    }
}
